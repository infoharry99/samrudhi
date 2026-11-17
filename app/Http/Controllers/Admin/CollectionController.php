<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MonthlyDue;
use App\Models\MonthlyRegister;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->get('mode');
        $status = $request->get('status');
        $search = $request->get('search');
        $month = $request->get('month');

        $payments = Payment::with(['member', 'loan', 'recorder'])
            ->when($mode, fn($q) => $q->where('payment_mode', $mode))
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->when($month, fn($q) => $q->where('payment_month', $month))
            ->when($search, function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('transaction_reference', 'like', "%{$search}%")
                  ->orWhereHas('member', fn($m) => $m->where('full_name', 'like', "%{$search}%")->orWhere('member_code', 'like', "%{$search}%"));
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_collected' => Payment::sum('total_received'),
            'cash_collected' => Payment::where('payment_mode', 'cash')->sum('total_received'),
            'online_collected' => Payment::where('payment_mode', 'online')->sum('total_received'),
            'this_month' => Payment::whereMonth('payment_date', Carbon::now()->month)->whereYear('payment_date', Carbon::now()->year)->sum('total_received'),
        ];

        return view('admin.collections.index', compact('payments', 'stats', 'mode', 'status', 'search', 'month'));
    }

    public function create(Request $request)
    {
        $members = Member::where('status', 'active')->orderBy('full_name')->get();
        $selectedMemberId = $request->get('member_id');
        $selectedDueId = $request->get('due_id');

        $selectedDue = null;
        if ($selectedDueId) {
            $selectedDue = MonthlyDue::with(['member', 'loan'])->find($selectedDueId);
        }

        $suggestedReceipt = Payment::generateReceiptNumber();

        return view('admin.collections.create', compact('members', 'selectedMemberId', 'selectedDue', 'suggestedReceipt'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'monthly_due_id' => 'nullable|exists:monthly_dues,id',
            'payment_date' => 'required|date',
            'payment_month' => 'required|string',
            'deposit_portion' => 'required|numeric|min:0',
            'interest_portion' => 'required|numeric|min:0',
            'installment_portion' => 'required|numeric|min:0',
            'penalty_portion' => 'required|numeric|min:0',
            'total_received' => 'required|numeric|min:1',
            'payment_mode' => 'required|in:cash,online',
            'transaction_reference' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $sumBreakdown = round(
            $validated['deposit_portion'] +
            $validated['interest_portion'] +
            $validated['installment_portion'] +
            $validated['penalty_portion'],
            2
        );

        if (abs($sumBreakdown - (float)$validated['total_received']) > 0.05) {
            return back()->withInput()->with('error', "Total received (₹{$validated['total_received']}) must match the sum of deposit, interest, installment, and penalty breakdown (₹{$sumBreakdown}).");
        }

        DB::beginTransaction();
        try {
            $member = Member::findOrFail($validated['member_id']);
            $activeLoan = $member->loans()->where('status', 'active')->first();
            $receiptNum = Payment::generateReceiptNumber();

            $payment = Payment::create([
                'receipt_number' => $receiptNum,
                'member_id' => $member->id,
                'monthly_due_id' => $validated['monthly_due_id'] ?? null,
                'loan_id' => $activeLoan ? $activeLoan->id : null,
                'payment_date' => $validated['payment_date'],
                'payment_month' => $validated['payment_month'],
                'total_received' => $validated['total_received'],
                'deposit_portion' => $validated['deposit_portion'],
                'interest_portion' => $validated['interest_portion'],
                'installment_portion' => $validated['installment_portion'],
                'penalty_portion' => $validated['penalty_portion'],
                'payment_mode' => $validated['payment_mode'],
                'payment_status' => 'paid',
                'transaction_reference' => $validated['transaction_reference'] ?? ($validated['payment_mode'] === 'cash' ? 'CASH-REC' : 'ONLINE-REC'),
                'recorded_by' => auth()->id(),
                'remarks' => $validated['remarks'],
            ]);

            // 1. Update Member Deposit Ledger & Savings Balance
            if ($validated['deposit_portion'] > 0) {
                $newDepositBal = $member->current_deposit_balance + $validated['deposit_portion'];
                MemberDeposit::create([
                    'member_id' => $member->id,
                    'payment_id' => $payment->id,
                    'transaction_type' => 'monthly_deposit',
                    'amount' => $validated['deposit_portion'],
                    'balance_after' => $newDepositBal,
                    'transaction_date' => $validated['payment_date'],
                    'description' => "Monthly deposit for {$validated['payment_month']} (Receipt: {$receiptNum})",
                ]);
                $member->current_deposit_balance = $newDepositBal;
            }

            // 2. Update Loan Balance & Principal Ledger
            if ($activeLoan && ($validated['installment_portion'] > 0 || $validated['interest_portion'] > 0)) {
                $inst = (float) $validated['installment_portion'];
                $interest = (float) $validated['interest_portion'];
                $penaltyPortion = (float) $validated['penalty_portion'];

                // Loan principal is reduced by installment portion ONLY (Never by interest)
                $newRemaining = max(0, $activeLoan->remaining_principal - $inst);
                $activeLoan->remaining_principal = $newRemaining;
                $activeLoan->total_principal_paid += $inst;
                $activeLoan->total_interest_paid += $interest;

                if ($newRemaining == 0 && $activeLoan->total_principal_paid >= $activeLoan->loan_amount) {
                    $activeLoan->status = 'completed';
                }
                $activeLoan->save();

                LoanTransaction::create([
                    'loan_id' => $activeLoan->id,
                    'member_id' => $member->id,
                    'payment_id' => $payment->id,
                    'transaction_type' => 'principal_repayment',
                    'principal_amount' => $inst,
                    'interest_amount' => $interest,
                    'penalty_amount' => $penaltyPortion,
                    'principal_balance_after' => $newRemaining,
                    'transaction_date' => $validated['payment_date'],
                    'remarks' => "Payment received via {$validated['payment_mode']} (Receipt: {$receiptNum})",
                ]);

                $member->current_loan_balance = $newRemaining;
            }

            $member->save();

            // 3. Update Monthly Due record if linked
            if (!empty($validated['monthly_due_id'])) {
                $due = MonthlyDue::find($validated['monthly_due_id']);
                if ($due) {
                    $due->amount_paid += $validated['total_received'];
                    $due->deposit_paid += $validated['deposit_portion'];
                    $due->interest_paid += $validated['interest_portion'];
                    $due->installment_paid += $validated['installment_portion'];
                    $due->penalty_paid += $validated['penalty_portion'];
                    $due->remaining_loan_balance = $activeLoan ? $activeLoan->remaining_principal : 0;
                    $due->payment_date = $validated['payment_date'];
                    $due->payment_mode = $validated['payment_mode'];
                    $due->payment_reference = $payment->transaction_reference;

                    if ($due->amount_paid >= $due->total_payable) {
                        $due->payment_status = 'paid';
                    } else {
                        $due->payment_status = 'partial';
                    }
                    $due->save();
                    $due->monthlyRegister->recalculateTotals();
                }
            }

            AuditLog::log('payment_recorded', "Recorded {$validated['payment_mode']} payment of ₹{$validated['total_received']} for {$member->full_name} (Receipt: {$receiptNum})");

            DB::commit();
            return redirect()->route('admin.collections.receipt', $payment->id)->with('success', "Payment recorded successfully! Receipt #{$receiptNum} generated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error recording collection: ' . $e->getMessage());
        }
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['member', 'loan', 'recorder']);
        return view('admin.collections.receipt', compact('payment'));
    }

    public function printReceipt(Payment $payment)
    {
        $payment->load(['member', 'loan', 'recorder']);
        return view('admin.collections.print_receipt', compact('payment'));
    }
}
