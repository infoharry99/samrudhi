@extends('layouts.member')

@section('title', 'My Loans - Shri Samruddhi Society')

@section('content')
<div class="space-y-4">
    <!-- Active Loan Card -->
    @if($activeLoan)
        <div class="bg-gradient-to-br from-purple-800 to-indigo-900 text-white p-5 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-white/20">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="font-mono text-xs font-bold text-purple-200">{{ $activeLoan->loan_number }}</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white/20 text-white">ACTIVE LOAN</span>
            </div>

            <div class="my-3">
                <span class="text-[11px] text-purple-200 block">Remaining Loan Principal Balance</span>
                <span class="text-3xl font-extrabold font-display">₹{{ number_format($activeLoan->remaining_principal, 2) }}</span>
            </div>

            <!-- Progress Bar -->
            @php
                $pct = $activeLoan->loan_amount > 0 ? min(100, round(($activeLoan->total_principal_paid / $activeLoan->loan_amount) * 100)) : 0;
            @endphp
            <div>
                <div class="flex items-center justify-between text-[11px] text-purple-200 mb-1">
                    <span>Principal Repaid: ₹{{ number_format($activeLoan->total_principal_paid, 0) }}</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="w-full h-2 bg-purple-950/60 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-400 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>

            <!-- Loan Details Grid -->
            <div class="mt-4 pt-3 border-t border-white/15 grid grid-cols-3 gap-2 text-center text-xs">
                <div>
                    <span class="text-purple-300 block text-[10px]">Sanctioned</span>
                    <span class="font-bold">₹{{ number_format($activeLoan->loan_amount, 0) }}</span>
                </div>
                <div>
                    <span class="text-purple-300 block text-[10px]">Monthly EMI</span>
                    <span class="font-bold text-amber-300">₹{{ number_format($activeLoan->monthly_installment, 0) }}</span>
                </div>
                <div>
                    <span class="text-purple-300 block text-[10px]">Interest Rate</span>
                    <span class="font-bold text-emerald-300">{{ $activeLoan->interest_rate }}% Mo.</span>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-2xl border border-slate-200 text-center shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto mb-2">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-sm">No Active Loans</h3>
            <p class="text-xs text-slate-500 mt-0.5">You currently have no outstanding loan accounts with the society.</p>
        </div>
    @endif

    <!-- Loan Transactions History -->
    @if($activeLoan && $activeLoan->transactions->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Loan Repayment Ledger</h3>
                <span class="text-[10px] text-slate-400 font-mono">{{ $activeLoan->transactions->count() }} Entries</span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($activeLoan->transactions as $tx)
                    <div class="p-3.5 hover:bg-slate-50 transition flex items-center justify-between text-xs">
                        <div>
                            <p class="font-bold text-slate-900">{{ $tx->remarks ?? ucwords(str_replace('_', ' ', $tx->transaction_type)) }}</p>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $tx->transaction_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right font-mono">
                            @if($tx->principal_amount > 0)
                                <p class="font-bold text-emerald-600">-₹{{ number_format($tx->principal_amount, 0) }}</p>
                            @endif
                            @if($tx->interest_amount > 0)
                                <p class="text-[10px] text-teal-700">Int: ₹{{ number_format($tx->interest_amount, 0) }}</p>
                            @endif
                            <p class="text-[10px] text-slate-500 mt-0.5">Bal: ₹{{ number_format($tx->principal_balance_after, 0) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
