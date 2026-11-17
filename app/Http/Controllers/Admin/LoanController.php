<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $loans = Loan::with('member')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where('loan_number', 'like', "%{$search}%")
                  ->orWhereHas('member', fn($m) => $m->where('full_name', 'like', "%{$search}%")->orWhere('member_code', 'like', "%{$search}%"));
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_distributed' => Loan::sum('loan_amount'),
            'total_outstanding' => Loan::where('status', 'active')->sum('remaining_principal'),
            'active_count' => Loan::where('status', 'active')->count(),
            'pending_count' => Loan::where('status', 'pending')->count(),
            'completed_count' => Loan::where('status', 'completed')->count(),
            'total_interest_earned' => Loan::sum('total_interest_paid'),
        ];

        return view('admin.loans.index', compact('loans', 'stats', 'status', 'search'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('full_name')->get();
        $nextNum = Loan::count() + 1;
        $suggestedNumber = 'LN-' . date('Y') . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.loans.create', compact('members', 'suggestedNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_number' => 'required|string|unique:loans,loan_number',
            'application_date' => 'required|date',
            'approval_date' => 'nullable|date',
            'loan_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'interest_calculation_method' => 'required|in:flat,reducing_balance,fixed_monthly',
            'monthly_installment' => 'required|numeric|min:100',
            'loan_duration_months' => 'required|integer|min:1|max:120',
            'first_installment_date' => 'required|date',
            'loan_purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'required|in:pending,approved,active',
        ]);

        DB::beginTransaction();
        try {
            $validated['remaining_principal'] = $validated['loan_amount'];
            if ($validated['status'] === 'active' && empty($validated['approval_date'])) {
                $validated['approval_date'] = Carbon::now()->toDateString();
            }

            $loan = Loan::create($validated);

            // If loan is created as active, record disbursement ledger transaction and update member balance
            if ($loan->status === 'active') {
                LoanTransaction::create([
                    'loan_id' => $loan->id,
                    'member_id' => $loan->member_id,
                    'transaction_type' => 'disbursement',
                    'principal_amount' => $loan->loan_amount,
                    'interest_amount' => 0.00,
                    'penalty_amount' => 0.00,
                    'principal_balance_after' => $loan->loan_amount,
                    'transaction_date' => $loan->approval_date ?? Carbon::now()->toDateString(),
                    'remarks' => 'Initial loan disbursement',
                ]);

                $loan->member->recalculateBalances();
            }

            AuditLog::log('loan_created', "Created loan {$loan->loan_number} for member {$loan->member->full_name} of ₹" . number_format($loan->loan_amount, 2));

            DB::commit();
            return redirect()->route('admin.loans.show', $loan->id)->with('success', "Loan {$loan->loan_number} registered successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating loan: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan)
    {
        $loan->load(['member', 'transactions', 'payments']);
        return view('admin.loans.show', compact('loan'));
    }

    public function approve(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Loan is not in pending status.');
        }

        DB::beginTransaction();
        try {
            $loan->update([
                'status' => 'active',
                'approval_date' => Carbon::now()->toDateString(),
            ]);

            LoanTransaction::create([
                'loan_id' => $loan->id,
                'member_id' => $loan->member_id,
                'transaction_type' => 'disbursement',
                'principal_amount' => $loan->loan_amount,
                'interest_amount' => 0.00,
                'penalty_amount' => 0.00,
                'principal_balance_after' => $loan->loan_amount,
                'transaction_date' => Carbon::now()->toDateString(),
                'remarks' => 'Loan approved and disbursed',
            ]);

            $loan->member->recalculateBalances();

            AuditLog::log('loan_approved', "Approved loan {$loan->loan_number} for {$loan->member->full_name}");

            DB::commit();
            return back()->with('success', "Loan {$loan->loan_number} has been approved and activated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving loan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Loan $loan)
    {
        $loan->update([
            'status' => 'rejected',
            'remarks' => ($loan->remarks ? $loan->remarks . ' | ' : '') . 'Rejected on ' . Carbon::now()->toFormattedDateString(),
        ]);

        AuditLog::log('loan_rejected', "Rejected loan {$loan->loan_number} for {$loan->member->full_name}");

        return back()->with('success', "Loan {$loan->loan_number} has been marked as rejected.");
    }
}
