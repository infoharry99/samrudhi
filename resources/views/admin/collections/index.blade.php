@extends('layouts.admin')

@section('title', 'Collections & Receipts - Shri Samruddhi Society')
@section('header_title', 'Collections & Receipts')

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Society Collections Ledger</h2>
            <p class="text-xs text-slate-500">Record cash collections, verify online payments, and generate digital receipts</p>
        </div>

        <a href="{{ route('admin.collections.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Record Cash Payment</span>
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Collections</span>
            <span class="text-lg font-bold text-slate-900 font-display">₹{{ number_format($stats['total_collected'], 0) }}</span>
            <span class="text-[10px] text-slate-400">{{ $payments->total() }} Receipts</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Cash Collected</span>
            <span class="text-lg font-bold text-emerald-600 font-display">₹{{ number_format($stats['cash_collected'], 0) }}</span>
            <span class="text-[10px] text-emerald-600">Manual vouchers</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Online Collected</span>
            <span class="text-lg font-bold text-blue-600 font-display">₹{{ number_format($stats['online_collected'], 0) }}</span>
            <span class="text-[10px] text-blue-600">UPI / Gateway</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">This Month (Nov)</span>
            <span class="text-lg font-bold text-amber-600 font-display">₹{{ number_format($stats['this_month'], 0) }}</span>
            <span class="text-[10px] text-amber-600">Current collections</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search receipt #, member..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <select name="mode" onchange="this.form.submit()" class="py-1.5 px-3 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
                <option value="">All Payment Modes</option>
                <option value="cash" {{ $mode === 'cash' ? 'selected' : '' }}>Cash Only</option>
                <option value="online" {{ $mode === 'online' ? 'selected' : '' }}>Online / UPI Only</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700">Filter</button>
            @if($search || $mode)
                <a href="{{ route('admin.collections.index') }}" class="px-2 py-1.5 text-slate-500 hover:text-slate-800 text-xs">Reset</a>
            @endif
        </form>

        <span class="text-xs text-slate-500">
            Showing {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} receipts
        </span>
    </div>

    <!-- Receipts Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Receipt No</th>
                        <th class="py-3 px-3">Date</th>
                        <th class="py-3 px-3">Member Details</th>
                        <th class="py-3 px-3 text-right">Deposit</th>
                        <th class="py-3 px-3 text-right">Interest</th>
                        <th class="py-3 px-3 text-right">Installment</th>
                        <th class="py-3 px-3 text-right">Penalty</th>
                        <th class="py-3 px-3 text-right font-extrabold">Total Received</th>
                        <th class="py-3 px-3 text-center">Mode</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3 font-mono font-bold text-brand-700">
                                <a href="{{ route('admin.collections.receipt', $payment->id) }}" class="hover:underline">
                                    {{ $payment->receipt_number }}
                                </a>
                            </td>
                            <td class="py-3 px-3 text-slate-500">
                                {{ $payment->payment_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900 block">{{ $payment->member->full_name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $payment->member->member_code }} • {{ $payment->member->mobile_number }}</span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-amber-700">
                                ₹{{ number_format($payment->deposit_portion, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-teal-700">
                                ₹{{ number_format($payment->interest_portion, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-purple-700">
                                ₹{{ number_format($payment->installment_portion, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-rose-600">
                                ₹{{ number_format($payment->penalty_portion, 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700 text-sm">
                                ₹{{ number_format($payment->total_received, 0) }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $payment->payment_mode === 'cash' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $payment->payment_mode }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <a href="{{ route('admin.collections.receipt', $payment->id) }}" class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-semibold text-xs transition">
                                    <i class="fa-solid fa-receipt mr-1"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-8 text-slate-400">No collection records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
