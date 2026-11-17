@extends('layouts.admin')

@section('title', 'Member Profile: ' . $member->full_name)
@section('header_title', 'Member Profile & Ledger')

@section('content')
<div class="space-y-6">
    <!-- Member Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-700 to-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-brand-500/20 shrink-0">
                {{ strtoupper(substr($member->full_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded font-mono text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                        {{ $member->member_code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $member->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ strtoupper($member->status) }}
                    </span>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mt-1 font-display">{{ $member->full_name }}</h2>
                <p class="text-xs text-slate-500 flex items-center gap-3 mt-0.5">
                    <span><i class="fa-solid fa-mobile-screen mr-1 text-slate-400"></i>{{ $member->mobile_number }}</span>
                    <span>•</span>
                    <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i>Joined {{ $member->joining_date->format('d M Y') }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.collections.create', ['member_id' => $member->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Record Payment</span>
            </a>

            <a href="{{ route('admin.members.statement', $member->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                <span>Statement</span>
            </a>

            <a href="{{ route('admin.members.edit', $member->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-pen"></i>
                <span>Edit</span>
            </a>

            <!-- Delete Safeguard Button -->
            <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure? Financial safeguards will prevent deletion if loans or transaction records exist.');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl text-xs transition" title="Delete Member">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Financial Balance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Savings -->
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white p-5 rounded-2xl shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-amber-100">Savings Deposit Balance</span>
                <i class="fa-solid fa-piggy-bank text-amber-200 text-xl"></i>
            </div>
            <p class="text-2xl font-bold mt-2 font-display">₹{{ number_format($member->current_deposit_balance, 2) }}</p>
            <span class="text-[11px] text-amber-100 mt-1 block">Monthly Share: ₹{{ number_format($member->monthly_deposit_amount, 0) }}</span>
        </div>

        <!-- Active Loan Balance -->
        <div class="bg-gradient-to-br from-rose-600 to-rose-700 text-white p-5 rounded-2xl shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-rose-100">Outstanding Loan Principal</span>
                <i class="fa-solid fa-scale-unbalanced text-rose-200 text-xl"></i>
            </div>
            <p class="text-2xl font-bold mt-2 font-display">₹{{ number_format($member->current_loan_balance, 2) }}</p>
            <span class="text-[11px] text-rose-100 mt-1 block">
                {{ $activeLoan ? 'Active Loan: ' . $activeLoan->loan_number : 'No Active Loans' }}
            </span>
        </div>

        <!-- Total Repaid Principal -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Payments Made</span>
                <i class="fa-solid fa-receipt text-emerald-500 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2 font-display">
                ₹{{ number_format($member->payments->sum('total_received'), 2) }}
            </p>
            <span class="text-[11px] text-emerald-600 font-medium mt-1 block">{{ $member->payments->count() }} Receipts Issued</span>
        </div>

        <!-- Active Loan EMI -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Monthly EMI / Installment</span>
                <i class="fa-solid fa-calculator text-purple-500 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-purple-700 mt-2 font-display">
                ₹{{ number_format($activeLoan ? $activeLoan->monthly_installment : 0, 2) }}
            </p>
            <span class="text-[11px] text-slate-500 mt-1 block">
                {{ $activeLoan ? 'Interest Rate: ' . $activeLoan->interest_rate . '% monthly' : 'N/A' }}
            </span>
        </div>
    </div>

    <!-- Active Loan Banner (If has active loan) -->
    @if($activeLoan)
        <div class="bg-white p-5 rounded-2xl border border-purple-200 shadow-sm bg-purple-50/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-purple-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    <h3 class="font-bold text-slate-900 text-sm">Active Loan Account: {{ $activeLoan->loan_number }}</h3>
                    <span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-2 py-0.5 rounded-full uppercase">ACTIVE</span>
                </div>
                <a href="{{ route('admin.loans.show', $activeLoan->id) }}" class="text-xs font-semibold text-purple-700 hover:text-purple-900">
                    View Full Loan Ledger <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px]">Sanctioned Amount</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($activeLoan->loan_amount, 0) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Remaining Principal</span>
                    <span class="font-bold text-rose-600">₹{{ number_format($activeLoan->remaining_principal, 0) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Monthly Installment</span>
                    <span class="font-bold text-purple-700">₹{{ number_format($activeLoan->monthly_installment, 0) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Interest Rate & Type</span>
                    <span class="font-bold text-teal-700">{{ $activeLoan->interest_rate }}% ({{ ucwords(str_replace('_', ' ', $activeLoan->interest_calculation_method)) }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Next Monthly Interest</span>
                    <span class="font-bold text-teal-700">₹{{ number_format($activeLoan->calculateMonthlyInterest(), 0) }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Tables: Member Savings Ledger & Payment Receipts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Savings Deposit Ledger -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Monthly Deposit Passbook</h4>
                    <p class="text-xs text-slate-500">Record of monthly contributions and cumulative savings</p>
                </div>
                <span class="text-xs font-bold text-amber-700 font-mono">₹{{ number_format($member->current_deposit_balance, 0) }}</span>
            </div>

            <div class="overflow-x-auto max-h-72">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Description</th>
                            <th class="py-2.5 px-3 text-right">Amount</th>
                            <th class="py-2.5 px-3 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($member->deposits as $dep)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 font-mono text-slate-500">{{ $dep->transaction_date->format('d-m-Y') }}</td>
                                <td class="py-2 px-3 text-slate-700">{{ $dep->description }}</td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-emerald-600">+₹{{ number_format($dep->amount, 0) }}</td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">₹{{ number_format($dep->balance_after, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-slate-400">No deposit records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Recent Payment Receipts -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Payment Receipts History</h4>
                    <p class="text-xs text-slate-500">Receipts generated from cash and online payments</p>
                </div>
            </div>

            <div class="overflow-x-auto max-h-72">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3">Receipt No</th>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">Mode</th>
                            <th class="py-2.5 px-3 text-right">Amount</th>
                            <th class="py-2.5 px-3 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($member->payments as $pay)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 font-mono font-bold text-brand-700">{{ $pay->receipt_number }}</td>
                                <td class="py-2 px-3 text-slate-500">{{ $pay->payment_date->format('d-m-Y') }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $pay->payment_mode === 'cash' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $pay->payment_mode }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">₹{{ number_format($pay->total_received, 0) }}</td>
                                <td class="py-2 px-3 text-center">
                                    <a href="{{ route('admin.collections.receipt', $pay->id) }}" class="text-brand-600 hover:underline">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-slate-400">No payment receipts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
