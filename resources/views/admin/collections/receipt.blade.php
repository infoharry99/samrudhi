@extends('layouts.admin')

@section('title', 'Receipt: ' . $payment->receipt_number)
@section('header_title', 'Payment Receipt')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.collections.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Collections
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.collections.print_receipt', $payment->id) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Print Official Receipt</span>
            </a>
        </div>
    </div>

    <!-- Official Receipt Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-8 relative overflow-hidden">
        <!-- Top decorative border -->
        <div class="h-2 bg-gradient-to-r from-brand-600 via-indigo-600 to-emerald-500 absolute top-0 left-0 right-0"></div>

        <!-- Receipt Header -->
        <div class="text-center pb-5 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-xl mx-auto mb-2 border border-brand-200">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wide font-display">SHRI SAMRUDDHI CO-OPERATIVE SOCIETY</h2>
            <p class="text-xs text-slate-500">Official Payment Receipt & Member Acknowledgment</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Registration No: REG/GUJ/AHM/2018/14892 • Ahmedabad</p>
        </div>

        <!-- Receipt Metadata -->
        <div class="grid grid-cols-2 gap-4 py-4 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Receipt Number</span>
                <span class="font-mono font-bold text-slate-900 text-sm">{{ $payment->receipt_number }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Payment Date</span>
                <span class="font-medium text-slate-900">{{ $payment->payment_date->format('d F Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Received From</span>
                <span class="font-bold text-slate-900 block">{{ $payment->member->full_name }}</span>
                <span class="text-[10px] font-mono text-slate-500">{{ $payment->member->member_code }} • {{ $payment->member->mobile_number }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Payment Mode</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase inline-block {{ $payment->payment_mode === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $payment->payment_mode }}
                </span>
                <span class="block text-[10px] font-mono text-slate-400 mt-0.5">Ref: {{ $payment->transaction_reference }}</span>
            </div>
        </div>

        <!-- Allocation Breakdown Table -->
        <div class="py-4">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Payment Particulars</h4>
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 divide-y divide-slate-200/60 text-xs">
                @if($payment->deposit_portion > 0)
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">1. Monthly Savings Deposit Contribution</span>
                        <span class="font-mono font-bold text-amber-800">₹{{ number_format($payment->deposit_portion, 2) }}</span>
                    </div>
                @endif

                @if($payment->interest_portion > 0)
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">2. Loan Interest Charge</span>
                        <span class="font-mono font-bold text-teal-800">₹{{ number_format($payment->interest_portion, 2) }}</span>
                    </div>
                @endif

                @if($payment->installment_portion > 0)
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">3. Loan Principal Repayment (Reduces Principal)</span>
                        <span class="font-mono font-bold text-purple-800">₹{{ number_format($payment->installment_portion, 2) }}</span>
                    </div>
                @endif

                @if($payment->penalty_portion > 0)
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-700 font-medium">4. Penalty / Late Fee</span>
                        <span class="font-mono font-bold text-rose-800">₹{{ number_format($payment->penalty_portion, 2) }}</span>
                    </div>
                @endif

                <div class="pt-2.5 flex items-center justify-between font-bold text-sm text-slate-900">
                    <span>Total Amount Paid</span>
                    <span class="font-mono text-emerald-700 text-base font-extrabold">₹{{ number_format($payment->total_received, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        @if($payment->remarks)
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 mb-6">
                <strong>Remarks:</strong> {{ $payment->remarks }}
            </div>
        @endif

        <!-- Signatures & Verification Stamp -->
        <div class="pt-6 border-t border-slate-100 grid grid-cols-2 gap-4 text-xs">
            <div>
                <p class="text-[10px] text-slate-400">Recorded By:</p>
                <p class="font-semibold text-slate-800">{{ $payment->recorder ? $payment->recorder->name : 'Online Self-Service' }}</p>
                <p class="text-[10px] text-emerald-600 font-bold mt-1">
                    <i class="fa-solid fa-circle-check"></i> System Verified Entry
                </p>
            </div>
            <div class="text-right flex flex-col justify-end">
                <div class="w-32 ml-auto border-b border-slate-400 pb-1 text-center font-bold text-[10px] uppercase text-slate-700">
                    Authorized Signatory
                </div>
                <p class="text-[9px] text-slate-400 mt-0.5">Shri Samruddhi Co-op Society</p>
            </div>
        </div>
    </div>
</div>
@endsection
