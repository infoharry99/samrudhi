@extends('layouts.member')

@section('title', 'My Savings Passbook - Shri Samruddhi Society')

@section('content')
<div class="space-y-4">
    <!-- Header Card -->
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white p-5 rounded-2xl shadow-md">
        <span class="text-xs text-amber-100 font-medium uppercase tracking-wider block">Total Accumulated Savings</span>
        <h2 class="text-3xl font-extrabold mt-1 font-display">₹{{ number_format($totalSavings, 2) }}</h2>
        <div class="mt-3 pt-2.5 border-t border-white/20 flex items-center justify-between text-xs">
            <span>Monthly Contribution: <strong>₹{{ number_format($member->monthly_deposit_amount, 0) }}</strong></span>
            <span>Member Code: <strong class="font-mono">{{ $member->member_code }}</strong></span>
        </div>
    </div>

    <!-- Passbook Ledger -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Passbook Entries</h3>
            <span class="text-[10px] text-slate-400 font-mono">{{ $deposits->total() }} Transactions</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($deposits as $dep)
                <div class="p-3.5 hover:bg-slate-50 transition flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-900">{{ $dep->description }}</p>
                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                            {{ $dep->transaction_date->format('d M Y') }} • {{ strtoupper(str_replace('_', ' ', $dep->transaction_type)) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-mono font-bold text-emerald-600">+₹{{ number_format($dep->amount, 0) }}</p>
                        <p class="text-[10px] font-mono text-slate-500 mt-0.5">Bal: ₹{{ number_format($dep->balance_after, 0) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-6">No deposit transactions recorded.</p>
            @endforelse
        </div>

        @if($deposits->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
