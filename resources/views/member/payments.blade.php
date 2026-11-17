@extends('layouts.member')

@section('title', 'Payment Receipts - Shri Samruddhi Society')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 font-display">My Payment Receipts</h2>
        <span class="text-xs text-slate-500 font-mono">{{ $payments->total() }} Receipts</span>
    </div>

    <!-- Receipts List -->
    <div class="space-y-3">
        @forelse($payments as $payment)
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:shadow transition flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $payment->payment_mode === 'online' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-sm font-bold shrink-0">
                        <i class="fa-solid {{ $payment->payment_mode === 'online' ? 'fa-bolt' : 'fa-money-bill-wave' }}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-brand-700">{{ $payment->receipt_number }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $payment->payment_mode === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $payment->payment_mode }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            {{ $payment->payment_date->format('d M Y') }} • {{ $payment->payment_month }}
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-sm font-bold font-mono text-emerald-600">₹{{ number_format($payment->total_received, 0) }}</p>
                    <a href="{{ route('member.receipt', $payment->id) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-600 hover:text-brand-800 mt-1">
                        <span>View Receipt</span>
                        <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
                <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs text-slate-500">No payment receipts found.</p>
            </div>
        @endforelse
    </div>

    @if($payments->hasPages())
        <div class="pt-2">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
