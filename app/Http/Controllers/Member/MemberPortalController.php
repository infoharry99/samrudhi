<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LoanTransaction;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MonthlyDue;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberPortalController extends Controller
{
    private function getAuthMember(): Member
    {
        $user = auth()->user();
        return Member::where('id', $user->member_id)->firstOrFail();
    }

    public function dashboard()
    {
        $member = $this->getAuthMember();
        $member->load([
            'loans' => fn($q) => $q->where('status', 'active'),
            'deposits' => fn($q) => $q->take(5),
            'payments' => fn($q) => $q->take(5),
            'monthlyDues' => fn($q) => $q->with('monthlyRegister')->take(6),
        ]);

        $activeLoan = $member->loans->first();

        // Get latest monthly due
        $currentDue = MonthlyDue::where('member_id', $member->id)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('id', 'desc')
            ->first();

        $notifications = $member->notifications()->take(3)->get();

        return view('member.dashboard', compact('member', 'activeLoan', 'currentDue', 'notifications'));
    }

    public function deposits()
    {
        $member = $this->getAuthMember();
        $deposits = $member->deposits()->paginate(15);
        $totalSavings = $member->current_deposit_balance;

        return view('member.deposits', compact('member', 'deposits', 'totalSavings'));
    }

    public function loans()
    {
        $member = $this->getAuthMember();
        $loans = $member->loans()->with('transactions')->get();
        $activeLoan = $loans->where('status', 'active')->first();

        return view('member.loans', compact('member', 'loans', 'activeLoan'));
    }

    public function payments()
    {
        $member = $this->getAuthMember();
        $payments = $member->payments()->paginate(15);

        return view('member.payments', compact('member', 'payments'));
    }

    public function showReceipt(Payment $payment)
    {
        $member = $this->getAuthMember();
        // Strict isolation check
        if ($payment->member_id !== $member->id) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        $payment->load(['member', 'loan']);
        return view('member.receipt', compact('payment'));
    }

    public function statement()
    {
        $member = $this->getAuthMember();
        $member->load([
            'deposits',
            'loans.transactions',
            'payments',
            'monthlyDues.monthlyRegister',
        ]);

        return view('member.statement', compact('member'));
    }

    public function payOnline(Request $request)
    {
        $member = $this->getAuthMember();
        $activeLoan = $member->loans()->where('status', 'active')->first();

        $currentDue = MonthlyDue::where('member_id', $member->id)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('id', 'desc')
            ->first();

        $defaultAmount = $currentDue
            ? ($currentDue->total_payable - $currentDue->amount_paid)
            : $member->monthly_deposit_amount;

        $upiId = Setting::get('upi_id', 'samruddhisociety@upi');
        $upiName = Setting::get('upi_payee_name', 'Shri Samruddhi Co-op Society');

        return view('member.pay_online', compact('member', 'currentDue', 'activeLoan', 'defaultAmount', 'upiId', 'upiName'));
    }

    public function processOnlinePayment(Request $request)
    {
        $member = $this->getAuthMember();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:upi,card,netbanking',
            'monthly_due_id' => 'nullable|exists:monthly_dues,id',
            'utr_reference' => 'nullable|string',
        ]);

        $activeLoan = $member->loans()->where('status', 'active')->first();
        $due = null;
        if (!empty($validated['monthly_due_id'])) {
            $due = MonthlyDue::where('id', $validated['monthly_due_id'])->where('member_id', $member->id)->first();
        }

        DB::beginTransaction();
        try {
            $totalAmount = (float) $validated['amount'];
            $receiptNum = Payment::generateReceiptNumber();
            $txnRef = !empty($validated['utr_reference']) ? $validated['utr_reference'] : 'PG-' . strtoupper($validated['payment_method']) . '-' . rand(10000000, 99999999);

            // Calculate allocations
            $depositPortion = 0;
            $interestPortion = 0;
            $installmentPortion = 0;
            $penaltyPortion = 0;

            if ($due) {
                $remDueDeposit = max(0, $due->monthly_deposit - $due->deposit_paid);
                $remDueInterest = max(0, $due->interest - $due->interest_paid);
                $remDueInstallment = max(0, $due->monthly_installment - $due->installment_paid);
                $remDuePenalty = max(0, $due->penalty - $due->penalty_paid);

                $remainingToAllocate = $totalAmount;

                // Priority 1: Penalty
                $pAlloc = min($remainingToAllocate, $remDuePenalty);
                $penaltyPortion += $pAlloc;
                $remainingToAllocate -= $pAlloc;

                // Priority 2: Interest
                $iAlloc = min($remainingToAllocate, $remDueInterest);
                $interestPortion += $iAlloc;
                $remainingToAllocate -= $iAlloc;

                // Priority 3: Deposit (Member Savings)
                $dAlloc = min($remainingToAllocate, $remDueDeposit);
                $depositPortion += $dAlloc;
                $remainingToAllocate -= $dAlloc;

                // Priority 4: Loan Installment (Principal)
                $instAlloc = min($remainingToAllocate, $remDueInstallment);
                $installmentPortion += $instAlloc;
                $remainingToAllocate -= $instAlloc;

                // Extra amount goes to principal if loan exists, else to deposit
                if ($remainingToAllocate > 0) {
                    if ($activeLoan && $activeLoan->remaining_principal > 0) {
                        $installmentPortion += $remainingToAllocate;
                    } else {
                        $depositPortion += $remainingToAllocate;
                    }
                }
            } else {
                // If no specific due, if loan exists allocate to installment, else to deposit
                if ($activeLoan && $activeLoan->remaining_principal > 0) {
                    $installmentPortion = $totalAmount;
                } else {
                    $depositPortion = $totalAmount;
                }
            }

            // Create Payment record
            $payment = Payment::create([
                'receipt_number' => $receiptNum,
                'member_id' => $member->id,
                'monthly_due_id' => $due ? $due->id : null,
                'loan_id' => $activeLoan ? $activeLoan->id : null,
                'payment_date' => Carbon::now()->toDateString(),
                'payment_month' => Carbon::now()->format('Y-m'),
                'total_received' => $totalAmount,
                'deposit_portion' => $depositPortion,
                'interest_portion' => $interestPortion,
                'installment_portion' => $installmentPortion,
                'penalty_portion' => $penaltyPortion,
                'payment_mode' => 'online',
                'payment_status' => 'paid',
                'transaction_reference' => $txnRef,
                'gateway_payment_id' => 'PAY_' . strtoupper(bin2hex(random_bytes(6))),
                'recorded_by' => null, // Online self-service
                'remarks' => "Online payment via " . strtoupper($validated['payment_method']) . " ({$txnRef})",
            ]);

            // Deposit ledger update
            if ($depositPortion > 0) {
                $newDepositBal = $member->current_deposit_balance + $depositPortion;
                MemberDeposit::create([
                    'member_id' => $member->id,
                    'payment_id' => $payment->id,
                    'transaction_type' => 'monthly_deposit',
                    'amount' => $depositPortion,
                    'balance_after' => $newDepositBal,
                    'transaction_date' => Carbon::now()->toDateString(),
                    'description' => "Online member deposit payment (Receipt: {$receiptNum})",
                ]);
                $member->current_deposit_balance = $newDepositBal;
            }

            // Loan ledger update (Installment reduces principal, interest does NOT reduce principal)
            if ($activeLoan && ($installmentPortion > 0 || $interestPortion > 0)) {
                $newPrincipal = max(0, $activeLoan->remaining_principal - $installmentPortion);
                $activeLoan->remaining_principal = $newPrincipal;
                $activeLoan->total_principal_paid += $installmentPortion;
                $activeLoan->total_interest_paid += $interestPortion;
                if ($newPrincipal == 0 && $activeLoan->total_principal_paid >= $activeLoan->loan_amount) {
                    $activeLoan->status = 'completed';
                }
                $activeLoan->save();

                LoanTransaction::create([
                    'loan_id' => $activeLoan->id,
                    'member_id' => $member->id,
                    'payment_id' => $payment->id,
                    'transaction_type' => 'principal_repayment',
                    'principal_amount' => $installmentPortion,
                    'interest_amount' => $interestPortion,
                    'penalty_amount' => $penaltyPortion,
                    'principal_balance_after' => $newPrincipal,
                    'transaction_date' => Carbon::now()->toDateString(),
                    'remarks' => "Online EMI payment ({$txnRef})",
                ]);

                $member->current_loan_balance = $newPrincipal;
            }

            $member->save();

            // Update due row
            if ($due) {
                $due->amount_paid += $totalAmount;
                $due->deposit_paid += $depositPortion;
                $due->interest_paid += $interestPortion;
                $due->installment_paid += $installmentPortion;
                $due->penalty_paid += $penaltyPortion;
                $due->remaining_loan_balance = $activeLoan ? $activeLoan->remaining_principal : 0;
                $due->payment_date = Carbon::now()->toDateString();
                $due->payment_mode = 'online';
                $due->payment_reference = $txnRef;
                $due->payment_status = ($due->amount_paid >= $due->total_payable) ? 'paid' : 'partial';
                $due->save();
                $due->monthlyRegister->recalculateTotals();
            }

            AuditLog::log('online_payment_success', "Member {$member->full_name} completed online payment of ₹{$totalAmount}. Receipt: {$receiptNum}");

            DB::commit();
            return redirect()->route('member.receipt', $payment->id)->with('success', "Payment of ₹" . number_format($totalAmount, 2) . " processed successfully! Receipt #{$receiptNum} generated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Online payment failed: ' . $e->getMessage());
        }
    }

    public function profile()
    {
        $member = $this->getAuthMember();
        return view('member.profile', compact('member'));
    }

    public function updateProfile(Request $request)
    {
        $member = $this->getAuthMember();

        $validated = $request->validate([
            'alternate_mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $member->alternate_mobile = $validated['alternate_mobile'];
        $member->address = $validated['address'];
        $member->save();

        if (!empty($validated['new_password'])) {
            $user = auth()->user();
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = Hash::make($validated['new_password']);
            $user->save();
        }

        AuditLog::log('profile_updated', "Member {$member->full_name} updated profile details.");

        return back()->with('success', 'Profile updated successfully.');
    }
}
