@extends('layouts.admin')

@section('title', 'Loan Account: ' . $loan->loan_number)
@section('header_title', 'Loan Account Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-purple-100 text-purple-800">
                    {{ $loan->loan_number }}
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $loan->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($loan->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                    {{ $loan->status }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mt-1 font-display">
                {{ $loan->member->full_name }}
                <span class="text-sm font-normal text-slate-500 font-mono">({{ $loan->member->member_code }})</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Application Date: {{ $loan->application_date->format('d M Y') }} • Mobile: {{ $loan->member->mobile_number }}</p>
        </div>

        <div class="flex items-center gap-2">
            @if($loan->status === 'pending')
                <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        <i class="fa-solid fa-check mr-1"></i> Approve & Disburse
                    </button>
                </form>

                <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        <i class="fa-solid fa-xmark mr-1"></i> Reject
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.loans.index') }}" class="px-3.5 py-2 border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold">
                Back to Loans
            </a>
        </div>
    </div>

    <!-- Loan Financial Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Sanctioned Amount</span>
            <span class="text-xl font-bold text-slate-900 font-display mt-1 block">₹{{ number_format($loan->loan_amount, 2) }}</span>
            <span class="text-[10px] text-slate-400">Initial principal</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Remaining Principal</span>
            <span class="text-xl font-bold text-rose-600 font-display mt-1 block">₹{{ number_format($loan->remaining_principal, 2) }}</span>
            <span class="text-[10px] text-rose-500 font-medium">To be recovered</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Principal Repaid</span>
            <span class="text-xl font-bold text-emerald-600 font-display mt-1 block">₹{{ number_format($loan->total_principal_paid, 2) }}</span>
            <span class="text-[10px] text-emerald-600">
                {{ $loan->loan_amount > 0 ? round(($loan->total_principal_paid / $loan->loan_amount) * 100, 1) : 0 }}% recovered
            </span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Interest Collected</span>
            <span class="text-xl font-bold text-teal-600 font-display mt-1 block">₹{{ number_format($loan->total_interest_paid, 2) }}</span>
            <span class="text-[10px] text-teal-600">{{ $loan->interest_rate }}% monthly</span>
        </div>
    </div>

    <!-- Loan Terms & Config Box -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <h4 class="font-bold text-slate-900 text-sm mb-3">Loan Terms & Configuration</h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px]">Interest Rate</span>
                <span class="font-bold text-slate-800">{{ $loan->interest_rate }}% per month</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Calculation Method</span>
                <span class="font-bold text-slate-800">{{ ucwords(str_replace('_', ' ', $loan->interest_calculation_method)) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Monthly Installment</span>
                <span class="font-bold text-purple-700">₹{{ number_format($loan->monthly_installment, 2) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Tenure</span>
                <span class="font-bold text-slate-800">{{ $loan->loan_duration_months }} Months</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Purpose</span>
                <span class="font-medium text-slate-700">{{ $loan->loan_purpose ?? 'General' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Approval Date</span>
                <span class="font-medium text-slate-700">{{ $loan->approval_date ? $loan->approval_date->format('d M Y') : 'Pending' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">First Installment Date</span>
                <span class="font-medium text-slate-700">{{ $loan->first_installment_date->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Accounting Rule</span>
                <span class="font-bold text-emerald-700">Principal reduced by EMI only</span>
            </div>
        </div>
    </div>

    <!-- Loan Ledger Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-slate-900 text-sm">Loan Transactions & Repayment Ledger</h4>
            <span class="text-xs text-slate-500 font-mono">{{ $loan->transactions->count() }} Entries</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Date</th>
                        <th class="py-2.5 px-3">Type</th>
                        <th class="py-2.5 px-3 text-right">Principal Paid</th>
                        <th class="py-2.5 px-3 text-right">Interest Paid</th>
                        <th class="py-2.5 px-3 text-right">Remaining Principal</th>
                        <th class="py-2.5 px-3">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loan->transactions as $tx)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 font-mono text-slate-500">{{ $tx->transaction_date->format('d-m-Y') }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $tx->transaction_type === 'disbursement' ? 'bg-purple-50 text-purple-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ str_replace('_', ' ', $tx->transaction_type) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-emerald-600">
                                {{ $tx->principal_amount > 0 ? '₹' . number_format($tx->principal_amount, 2) : '-' }}
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-teal-600">
                                {{ $tx->interest_amount > 0 ? '₹' . number_format($tx->interest_amount, 2) : '-' }}
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-rose-600">
                                ₹{{ number_format($tx->principal_balance_after, 2) }}
                            </td>
                            <td class="py-2 px-3 text-slate-500">{{ $tx->remarks }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-6 text-slate-400">No loan transactions recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
