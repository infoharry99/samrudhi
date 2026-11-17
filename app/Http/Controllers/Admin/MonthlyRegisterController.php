<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\Member;
use App\Models\MonthlyDue;
use App\Models\MonthlyRegister;
use App\Models\Payment;
use App\Models\Penalty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyRegisterController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('year', Carbon::now()->year);

        $registers = MonthlyRegister::withCount('dues')
            ->where('year', $selectedYear)
            ->orderBy('month', 'desc')
            ->get();

        // If no register exists for current month, find latest or provide generation prompt
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentMonthRegister = MonthlyRegister::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        return view('admin.registers.index', compact('registers', 'selectedYear', 'currentMonthRegister'));
    }

    public function show(Request $request, MonthlyRegister $monthlyRegister)
    {
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        $duesQuery = $monthlyRegister->dues()
            ->with(['member', 'loan'])
            ->when($statusFilter, fn($q) => $q->where('payment_status', $statusFilter))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('member', function ($m) use ($search) {
                    $m->where('full_name', 'like', "%{$search}%")
                      ->orWhere('member_code', 'like', "%{$search}%")
                      ->orWhere('mobile_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'asc');

        $dues = $duesQuery->get();

        // Register Summary Stats
        $summary = [
            'total_members' => $monthlyRegister->dues()->count(),
            'paid_count' => $monthlyRegister->dues()->where('payment_status', 'paid')->count(),
            'partial_count' => $monthlyRegister->dues()->where('payment_status', 'partial')->count(),
            'pending_count' => $monthlyRegister->dues()->where('payment_status', 'pending')->count(),
            'total_deposit' => $monthlyRegister->dues()->sum('monthly_deposit'),
            'total_interest' => $monthlyRegister->dues()->sum('interest'),
            'total_installment' => $monthlyRegister->dues()->sum('monthly_installment'),
            'total_penalty' => $monthlyRegister->dues()->sum('penalty'),
            'total_payable' => $monthlyRegister->dues()->sum('total_payable'),
            'total_collected' => $monthlyRegister->dues()->sum('amount_paid'),
            'total_pending' => $monthlyRegister->dues()->sum('total_payable') - $monthlyRegister->dues()->sum('amount_paid'),
        ];

        return view('admin.registers.show', compact('monthlyRegister', 'dues', 'summary', 'statusFilter', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2020,2035',
            'notes' => 'nullable|string',
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];

        // Check if register already exists
        $existing = MonthlyRegister::where('month', $month)->where('year', $year)->first();
        if ($existing) {
            return redirect()->route('admin.registers.show', $existing->id)->with('info', 'Register for this month already exists.');
        }

        DB::beginTransaction();
        try {
            $monthName = Carbon::create($year, $month, 1)->format('F Y');
            $register = MonthlyRegister::create([
                'month' => $month,
                'year' => $year,
                'title' => "{$monthName} Society Collection Register",
                'generated_date' => Carbon::now()->toDateString(),
                'status' => 'active',
                'notes' => $validated['notes'] ?? "Collection register for {$monthName}",
            ]);

            // Fetch all active members
            $activeMembers = Member::where('status', 'active')->orderBy('id', 'asc')->get();

            foreach ($activeMembers as $member) {
                // Check if member has active loan
                $activeLoan = $member->loans()->where('status', 'active')->first();

                $deposit = $member->monthly_deposit_amount;
                $loanAmt = $activeLoan ? $activeLoan->loan_amount : 0.00;
                $remLoan = $activeLoan ? $activeLoan->remaining_principal : 0.00;
                $interest = $activeLoan ? $activeLoan->calculateMonthlyInterest() : 0.00;
                $installment = $activeLoan ? min($activeLoan->monthly_installment, $remLoan) : 0.00;

                // Check any pending penalty for this member
                $penalty = Penalty::where('member_id', $member->id)
                    ->where('status', 'pending')
                    ->whereNull('monthly_due_id')
                    ->sum('amount');

                $totalPayable = round($deposit + $interest + $installment + $penalty, 2);

                $due = MonthlyDue::create([
                    'monthly_register_id' => $register->id,
                    'member_id' => $member->id,
                    'loan_id' => $activeLoan ? $activeLoan->id : null,
                    'monthly_deposit' => $deposit,
                    'loan_amount' => $loanAmt,
                    'interest' => $interest,
                    'monthly_installment' => $installment,
                    'penalty' => $penalty,
                    'total_payable' => $totalPayable,
                    'remaining_loan_balance' => $remLoan,
                    'amount_paid' => 0.00,
                    'deposit_paid' => 0.00,
                    'interest_paid' => 0.00,
                    'installment_paid' => 0.00,
                    'penalty_paid' => 0.00,
                    'payment_status' => 'pending',
                ]);

                // Attach penalty to this monthly due if applicable
                if ($penalty > 0) {
                    Penalty::where('member_id', $member->id)
                        ->where('status', 'pending')
                        ->whereNull('monthly_due_id')
                        ->update(['monthly_due_id' => $due->id]);
                }
            }

            $register->recalculateTotals();

            AuditLog::log('register_generated', "Generated monthly register for {$monthName} with {$activeMembers->count()} members");

            DB::commit();
            return redirect()->route('admin.registers.show', $register->id)->with('success', "Monthly Register for {$monthName} generated successfully with {$activeMembers->count()} members!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error generating register: ' . $e->getMessage());
        }
    }

    public function updateDue(Request $request, MonthlyDue $due)
    {
        $validated = $request->validate([
            'monthly_deposit' => 'required|numeric|min:0',
            'interest' => 'required|numeric|min:0',
            'monthly_installment' => 'required|numeric|min:0',
            'penalty' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $due->monthly_deposit = $validated['monthly_deposit'];
        $due->interest = $validated['interest'];
        $due->monthly_installment = $validated['monthly_installment'];
        $due->penalty = $validated['penalty'];
        $due->remarks = $validated['remarks'];
        $due->calculateTotalPayable();
        $due->save();

        $due->monthlyRegister->recalculateTotals();

        AuditLog::log('due_adjusted', "Adjusted dues for {$due->member->full_name} in register #{$due->monthly_register_id}");

        return back()->with('success', "Dues updated successfully for {$due->member->full_name}. Total payable: ₹" . number_format($due->total_payable, 2));
    }

    public function print(MonthlyRegister $monthlyRegister)
    {
        $dues = $monthlyRegister->dues()->with(['member', 'loan'])->get();
        return view('admin.registers.print', compact('monthlyRegister', 'dues'));
    }

    public function exportExcel(MonthlyRegister $monthlyRegister)
    {
        $dues = $monthlyRegister->dues()->with(['member', 'loan'])->get();
        $filename = "Register_" . date('F_Y', mktime(0, 0, 0, $monthlyRegister->month, 1, $monthlyRegister->year)) . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Sr. No.',
            'Member ID',
            'Member Name',
            'Mobile Number',
            'Monthly Deposit (₹)',
            'Loan Amount (₹)',
            'Interest (₹)',
            'Monthly Installment (₹)',
            'Penalty (₹)',
            'Monthly Payable (₹)',
            'Loan Balance (₹)',
            'Amount Paid (₹)',
            'Payment Status',
            'Payment Mode',
            'Payment Date',
            'Reference'
        ];

        $callback = function () use ($dues, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);

            foreach ($dues as $idx => $due) {
                fputcsv($file, [
                    $idx + 1,
                    $due->member->member_code,
                    $due->member->full_name,
                    $due->member->mobile_number,
                    $due->monthly_deposit,
                    $due->loan_amount,
                    $due->interest,
                    $due->monthly_installment,
                    $due->penalty,
                    $due->total_payable,
                    $due->remaining_loan_balance,
                    $due->amount_paid,
                    strtoupper($due->payment_status),
                    $due->payment_mode ? strtoupper($due->payment_mode) : '-',
                    $due->payment_date ? $due->payment_date->format('d-m-Y') : '-',
                    $due->payment_reference ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
