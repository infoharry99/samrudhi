<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MonthlyDue;
use App\Models\Payment;
use App\Models\Penalty;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'collections');
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $memberId = $request->get('member_id');

        $members = Member::orderBy('full_name')->get();

        // 1. Overall Financial Summary
        $financialSummary = [
            'total_deposits_pool' => Member::sum('current_deposit_balance'),
            'total_loans_disbursed' => Loan::sum('loan_amount'),
            'total_principal_repaid' => Loan::sum('total_principal_paid'),
            'total_outstanding_loans' => Loan::where('status', 'active')->sum('remaining_principal'),
            'total_interest_collected' => Payment::sum('interest_portion'),
            'total_penalties_collected' => Payment::sum('penalty_portion'),
            'total_cash_collected' => Payment::where('payment_mode', 'cash')->sum('total_received'),
            'total_online_collected' => Payment::where('payment_mode', 'online')->sum('total_received'),
        ];

        // 2. Collection Query based on filters
        $collections = Payment::with('member')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->when($memberId, fn($q) => $q->where('member_id', $memberId))
            ->orderBy('payment_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // 3. Loans Query
        $loans = Loan::with('member')
            ->when($memberId, fn($q) => $q->where('member_id', $memberId))
            ->orderBy('id', 'desc')
            ->get();

        // 4. Deposits Query
        $deposits = MemberDeposit::with('member')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($memberId, fn($q) => $q->where('member_id', $memberId))
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', compact(
            'tab',
            'startDate',
            'endDate',
            'memberId',
            'members',
            'financialSummary',
            'collections',
            'loans',
            'deposits'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'collections');
        $filename = "Samruddhi_Society_{$type}_report_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'collections') {
                fputcsv($file, ['Receipt No', 'Date', 'Member Code', 'Member Name', 'Deposit', 'Interest', 'Installment', 'Penalty', 'Total Received', 'Mode', 'Reference']);
                $payments = Payment::with('member')->orderBy('payment_date', 'desc')->get();
                foreach ($payments as $p) {
                    fputcsv($file, [
                        $p->receipt_number,
                        $p->payment_date->format('d-m-Y'),
                        $p->member->member_code,
                        $p->member->full_name,
                        $p->deposit_portion,
                        $p->interest_portion,
                        $p->installment_portion,
                        $p->penalty_portion,
                        $p->total_received,
                        strtoupper($p->payment_mode),
                        $p->transaction_reference
                    ]);
                }
            } elseif ($type === 'loans') {
                fputcsv($file, ['Loan Number', 'Member Code', 'Member Name', 'Amount', 'Interest Rate', 'Duration', 'EMI', 'Principal Repaid', 'Remaining Balance', 'Status']);
                $loans = Loan::with('member')->get();
                foreach ($loans as $l) {
                    fputcsv($file, [
                        $l->loan_number,
                        $l->member->member_code,
                        $l->member->full_name,
                        $l->loan_amount,
                        $l->interest_rate . '%',
                        $l->loan_duration_months . ' Mos',
                        $l->monthly_installment,
                        $l->total_principal_paid,
                        $l->remaining_principal,
                        strtoupper($l->status)
                    ]);
                }
            } else {
                fputcsv($file, ['Member Code', 'Member Name', 'Mobile', 'Joining Date', 'Total Savings Balance', 'Outstanding Loan', 'Status']);
                $members = Member::all();
                foreach ($members as $m) {
                    fputcsv($file, [
                        $m->member_code,
                        $m->full_name,
                        $m->mobile_number,
                        $m->joining_date->format('d-m-Y'),
                        $m->current_deposit_balance,
                        $m->current_loan_balance,
                        strtoupper($m->status)
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
