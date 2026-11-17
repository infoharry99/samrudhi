<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\MonthlyDue;
use App\Models\MonthlyRegister;
use App\Models\Payment;
use App\Models\Penalty;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        $now = Carbon::now();

        // 1. Core KPIs
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $newMembersThisMonth = Member::whereMonth('joining_date', $now->month)
            ->whereYear('joining_date', $now->year)
            ->count();

        $totalDeposits = MemberDeposit::sum('amount');
        $totalLoansDistributed = Loan::sum('loan_amount');
        $totalOutstandingLoans = Loan::where('status', 'active')->sum('remaining_principal');
        $totalInterestCollected = Payment::sum('interest_portion');
        $totalCollections = Payment::sum('total_received');

        // Current Month Register stats
        $currentRegister = MonthlyRegister::where('month', $now->month)
            ->where('year', $now->year)
            ->first();

        if ($currentRegister) {
            $pendingPayments = MonthlyDue::where('monthly_register_id', $currentRegister->id)
                ->where('payment_status', '!=', 'paid')
                ->sum('total_payable');
            $pendingMembersCount = MonthlyDue::where('monthly_register_id', $currentRegister->id)
                ->where('payment_status', '!=', 'paid')
                ->count();
        } else {
            $pendingPayments = 0;
            $pendingMembersCount = 0;
        }

        $totalPenalties = Penalty::where('status', '!=', 'waived')->sum('amount');

        // 2. Trend & Chart Data (Last 6 Months)
        $chartMonths = [];
        $collectionsChartData = [];
        $depositsChartData = [];
        $interestChartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthObj = Carbon::now()->subMonths($i);
            $monthStr = $monthObj->format('M Y');
            $chartMonths[] = $monthStr;

            $collected = Payment::whereYear('payment_date', $monthObj->year)
                ->whereMonth('payment_date', $monthObj->month)
                ->sum('total_received');

            $dep = Payment::whereYear('payment_date', $monthObj->year)
                ->whereMonth('payment_date', $monthObj->month)
                ->sum('deposit_portion');

            $int = Payment::whereYear('payment_date', $monthObj->year)
                ->whereMonth('payment_date', $monthObj->month)
                ->sum('interest_portion');

            $collectionsChartData[] = (float) $collected;
            $depositsChartData[] = (float) $dep;
            $interestChartData[] = (float) $int;
        }

        // Loan Distribution Data
        $activeLoansCount = Loan::where('status', 'active')->count();
        $completedLoansCount = Loan::where('status', 'completed')->count();
        $membersWithoutLoan = Member::whereDoesntHave('loans', function ($q) {
            $q->where('status', 'active');
        })->count();

        // Recent Transactions
        $recentPayments = Payment::with('member')->orderBy('id', 'desc')->take(7)->get();
        $recentDues = MonthlyDue::with(['member', 'loan'])
            ->when($currentRegister, fn($q) => $q->where('monthly_register_id', $currentRegister->id))
            ->orderBy('id', 'asc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers',
            'activeMembers',
            'newMembersThisMonth',
            'totalDeposits',
            'totalLoansDistributed',
            'totalOutstandingLoans',
            'totalInterestCollected',
            'totalCollections',
            'pendingPayments',
            'pendingMembersCount',
            'totalPenalties',
            'chartMonths',
            'collectionsChartData',
            'depositsChartData',
            'interestChartData',
            'activeLoansCount',
            'completedLoansCount',
            'membersWithoutLoan',
            'recentPayments',
            'recentDues',
            'currentRegister',
            'period'
        ));
    }
}
