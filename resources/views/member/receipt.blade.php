@extends('layouts.member')

@section('title', 'Receipt ' . $payment->receipt_number)

@section('content')
<div class="space-y-4 max-w-lg mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('member.payments') }}" class="text-xs text-brand-600 font-semibold hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Receipts
        </a>
        <button onclick="window.print()" class="px-3 py-1.5 rounded-xl bg-slate-900 text-white font-semibold text-xs shadow-sm">
            <i class="fa-solid fa-print mr-1"></i> Print Receipt
        </button>
    </div>

    <!-- Receipt Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6 relative overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-brand-600 absolute top-0 left-0 right-0"></div>

        <div class="text-center pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-lg mx-auto mb-1.5">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-sm uppercase">SHRI SAMRUDDHI CO-OP SOCIETY</h3>
            <p class="text-[10px] text-slate-500">Official Digital Payment Receipt</p>
        </div>

        <div class="py-3 border-b border-slate-100 grid grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px]">Receipt No</span>
                <span class="font-mono font-bold text-slate-800">{{ $payment->receipt_number }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[10px]">Payment Date</span>
                <span class="font-medium text-slate-800">{{ $payment->payment_date->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Member</span>
                <span class="font-bold text-slate-800 block">{{ $payment->member->full_name }}</span>
                <span class="text-[10px] font-mono text-slate-500">{{ $payment->member->member_code }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[10px]">Payment Mode</span>
                <span class="font-bold uppercase text-emerald-600">{{ $payment->payment_mode }}</span>
                <span class="block text-[9px] font-mono text-slate-400">{{ $payment->transaction_reference }}</span>
            </div>
        </div>

        <div class="py-3 space-y-1.5 text-xs">
            @if($payment->deposit_portion > 0)
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Monthly Savings Deposit</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($payment->deposit_portion, 2) }}</span>
                </div>
            @endif
            @if($payment->interest_portion > 0)
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Loan Interest</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($payment->interest_portion, 2) }}</span>
                </div>
            @endif
            @if($payment->installment_portion > 0)
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Loan Installment (Principal)</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($payment->installment_portion, 2) }}</span>
                </div>
            @endif
            @if($payment->penalty_portion > 0)
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Penalty</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($payment->penalty_portion, 2) }}</span>
                </div>
            @endif

            <div class="pt-2 border-t flex items-center justify-between font-bold text-sm text-slate-900">
                <span>Total Received</span>
                <span class="font-mono text-emerald-600 text-base">₹{{ number_format($payment->total_received, 2) }}</span>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 text-center">
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i> Payment Confirmed & Verified
            </span>
        </div>
    </div>
</div>
@endsection
