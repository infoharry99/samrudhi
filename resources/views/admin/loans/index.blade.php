@extends('layouts.admin')

@section('title', 'Loan Management - Shri Samruddhi Society')
@section('header_title', 'Loan Accounts Management')

@section('content')
<div class="space-y-5">
    <!-- Top Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Society Loan Management</h2>
            <p class="text-xs text-slate-500">Track loan sanctions, reducing balance interest calculations, EMIs, and repayments</p>
        </div>

        <a href="{{ route('admin.loans.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white text-xs font-semibold shadow-sm transition">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Sanction New Loan</span>
        </a>
    </div>

    <!-- Loan Stats Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Loans Disbursed</span>
            <span class="text-lg font-bold text-purple-700 font-display">₹{{ number_format($stats['total_distributed'], 0) }}</span>
            <span class="text-[10px] text-slate-400 block">{{ $loans->total() }} Loan Accounts</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Active Principal Outstanding</span>
            <span class="text-lg font-bold text-rose-600 font-display">₹{{ number_format($stats['total_outstanding'], 0) }}</span>
            <span class="text-[10px] text-rose-500 block">{{ $stats['active_count'] }} Active Borrowers</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Interest Earned</span>
            <span class="text-lg font-bold text-teal-600 font-display">₹{{ number_format($stats['total_interest_earned'], 0) }}</span>
            <span class="text-[10px] text-teal-600 block">Paid interest income</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Pending Applications</span>
            <span class="text-lg font-bold text-amber-600 font-display">{{ $stats['pending_count'] }}</span>
            <span class="text-[10px] text-amber-600 block">Awaiting approval</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by loan # or member..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-1.5 px-3 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
                <option value="">All Statuses</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Only</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed Only</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700">Search</button>
            @if($search || $status)
                <a href="{{ route('admin.loans.index') }}" class="px-2 py-1.5 text-slate-500 hover:text-slate-800 text-xs">Reset</a>
            @endif
        </form>

        <span class="text-xs text-slate-500">
            Showing {{ $loans->firstItem() ?? 0 }} - {{ $loans->lastItem() ?? 0 }} of {{ $loans->total() }}
        </span>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Loan Account</th>
                        <th class="py-3 px-3">Member Name</th>
                        <th class="py-3 px-3 text-right">Sanctioned Principal</th>
                        <th class="py-3 px-3 text-center">Interest Rate</th>
                        <th class="py-3 px-3 text-right">Monthly Installment</th>
                        <th class="py-3 px-3 text-right">Principal Repaid</th>
                        <th class="py-3 px-3 text-right">Remaining Principal</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3 font-mono font-bold text-purple-700">
                                {{ $loan->loan_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">App: {{ $loan->application_date->format('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                <a href="{{ route('admin.members.show', $loan->member_id) }}" class="hover:text-brand-600">
                                    {{ $loan->member->full_name }}
                                </a>
                                <span class="block text-[10px] text-slate-500 font-mono">{{ $loan->member->member_code }}</span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                                ₹{{ number_format($loan->loan_amount, 0) }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="font-bold text-teal-700">{{ $loan->interest_rate }}%</span>
                                <span class="block text-[9px] text-slate-400">Monthly</span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-purple-700">
                                ₹{{ number_format($loan->monthly_installment, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-emerald-600 font-medium">
                                ₹{{ number_format($loan->total_principal_paid, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-rose-600">
                                ₹{{ number_format($loan->remaining_principal, 0) }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($loan->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">ACTIVE</span>
                                @elseif($loan->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">PENDING</span>
                                @elseif($loan->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">COMPLETED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ strtoupper($loan->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('admin.loans.show', $loan->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-700 font-semibold text-xs transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-8 text-slate-400">No loan records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
