@extends('layouts.admin')

@section('title', 'Members Management - Shri Samruddhi Society')
@section('header_title', 'Member Management')

@section('content')
<div class="space-y-5">
    <!-- Header Controls -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Society Members ({{ $stats['total'] }})</h2>
            <p class="text-xs text-slate-500">Manage member accounts, personal profiles, monthly savings, and loan balances</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.migration.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-file-import text-teal-600"></i>
                <span>Import CSV</span>
            </a>

            <a href="{{ route('admin.members.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Member</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Enrolled</span>
            <span class="text-lg font-bold text-slate-900 font-display">{{ $stats['total'] }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Active Status</span>
            <span class="text-lg font-bold text-emerald-600 font-display">{{ $stats['active'] }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Member Deposits</span>
            <span class="text-lg font-bold text-amber-600 font-display">₹{{ number_format($stats['total_deposits'], 0) }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Outstanding Loans</span>
            <span class="text-lg font-bold text-rose-600 font-display">₹{{ number_format($stats['total_loans'], 0) }}</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, ID, or mobile..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-1.5 px-3 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
                <option value="">All Status</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700">Search</button>
            @if($search || $status)
                <a href="{{ route('admin.members.index') }}" class="px-2 py-1.5 text-slate-500 hover:text-slate-800 text-xs">Reset</a>
            @endif
        </form>

        <span class="text-xs text-slate-500">
            Showing {{ $members->firstItem() ?? 0 }} - {{ $members->lastItem() ?? 0 }} of {{ $members->total() }}
        </span>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Member ID</th>
                        <th class="py-3 px-3">Full Name</th>
                        <th class="py-3 px-3">Mobile Number</th>
                        <th class="py-3 px-3">Joining Date</th>
                        <th class="py-3 px-3 text-right">Monthly Deposit</th>
                        <th class="py-3 px-3 text-right">Total Deposit Bal</th>
                        <th class="py-3 px-3 text-right">Outstanding Loan</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3 font-mono font-bold text-brand-700">
                                {{ $member->member_code }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-900">
                                <a href="{{ route('admin.members.show', $member->id) }}" class="hover:text-brand-600 hover:underline">
                                    {{ $member->full_name }}
                                </a>
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-600">
                                {{ $member->mobile_number }}
                            </td>
                            <td class="py-3 px-3 text-slate-500">
                                {{ $member->joining_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono">
                                ₹{{ number_format($member->monthly_deposit_amount, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-amber-700">
                                ₹{{ number_format($member->current_deposit_balance, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold {{ $member->current_loan_balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ $member->current_loan_balance > 0 ? '₹' . number_format($member->current_loan_balance, 0) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $member->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ strtoupper($member->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="p-1.5 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded-lg" title="View Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.members.statement', $member->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg" title="Statement">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>
                                    <a href="{{ route('admin.members.edit', $member->id) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">No members found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
