@extends('layouts.member')

@section('title', 'Member Home - Shri Samruddhi Society')

@section('content')
<div class="space-y-4">
    <!-- PWA Install Banner (Mobile App Feel) -->
    <div id="pwa-install-card" class="bg-gradient-to-r from-indigo-900 to-brand-900 text-white p-3.5 rounded-2xl shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-base shadow shrink-0">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
            <div>
                <p class="text-xs font-bold leading-tight">Install Samruddhi App</p>
                <p class="text-[10px] text-slate-300">Add to home screen for 1-tap passbook access</p>
            </div>
        </div>
        <button onclick="alert('Tap the browser menu (⋮ or Share) and select Add to Home Screen to install the native app experience!')" class="px-2.5 py-1.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-[11px] shadow transition shrink-0">
            Install
        </button>
    </div>

    <!-- Monthly Collection Due Card (If pending dues exist) -->
    @if($currentDue)
        <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 text-white p-4 sm:p-5 rounded-2xl shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-white/20">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-100">Monthly Dues Payable</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-white/20 text-white">
                    {{ $currentDue->monthlyRegister->title }}
                </span>
            </div>

            <div class="my-3 flex items-baseline justify-between">
                <div>
                    <span class="text-[11px] text-amber-100 block">Total Due for this Month</span>
                    <span class="text-2xl sm:text-3xl font-extrabold font-display">₹{{ number_format($currentDue->total_payable - $currentDue->amount_paid, 0) }}</span>
                </div>
                <a href="{{ route('member.pay_online') }}" class="px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-black text-amber-300 font-bold text-xs shadow-md transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-bolt text-amber-400"></i>
                    <span>Pay Online</span>
                </a>
            </div>

            <!-- Breakdown Pills -->
            <div class="grid grid-cols-4 gap-1.5 text-center pt-2.5 border-t border-white/15 text-[10px]">
                <div class="bg-black/15 rounded-lg p-1.5">
                    <span class="text-amber-200 block text-[9px]">Deposit</span>
                    <span class="font-bold">₹{{ number_format($currentDue->monthly_deposit, 0) }}</span>
                </div>
                <div class="bg-black/15 rounded-lg p-1.5">
                    <span class="text-amber-200 block text-[9px]">Interest</span>
                    <span class="font-bold">₹{{ number_format($currentDue->interest, 0) }}</span>
                </div>
                <div class="bg-black/15 rounded-lg p-1.5">
                    <span class="text-amber-200 block text-[9px]">EMI</span>
                    <span class="font-bold">₹{{ number_format($currentDue->monthly_installment, 0) }}</span>
                </div>
                <div class="bg-black/15 rounded-lg p-1.5">
                    <span class="text-amber-200 block text-[9px]">Penalty</span>
                    <span class="font-bold">₹{{ number_format($currentDue->penalty, 0) }}</span>
                </div>
            </div>
        </div>
    @else
        <!-- All dues clear banner -->
        <div class="bg-emerald-600 text-white p-4 rounded-2xl shadow-md flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm">All Current Dues Paid</h3>
                    <p class="text-xs text-emerald-100">Thank you! Your society monthly contributions are fully updated.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Two Main Balances: Savings Passbook & Active Loan Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Savings Deposit Card -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">My Savings Passbook</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-piggy-bank"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-amber-700 mt-2 font-display">
                    ₹{{ number_format($member->current_deposit_balance, 2) }}
                </p>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Monthly contribution: ₹{{ number_format($member->monthly_deposit_amount, 0) }}</span>
            </div>
            <a href="{{ route('member.deposits') }}" class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-amber-700 hover:text-amber-900 flex items-center justify-between">
                <span>View Full Passbook</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Active Loan Card -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">My Loan Account</span>
                    <div class="w-8 h-8 rounded-xl {{ $activeLoan ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-sm">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>

                @if($activeLoan)
                    <p class="text-2xl font-bold text-rose-600 mt-2 font-display">
                        ₹{{ number_format($activeLoan->remaining_principal, 2) }}
                    </p>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-0.5">
                        <span>EMI: ₹{{ number_format($activeLoan->monthly_installment, 0) }}</span>
                        <span>Rate: {{ $activeLoan->interest_rate }}%</span>
                    </div>
                @else
                    <p class="text-lg font-bold text-slate-400 mt-2">No Active Loan</p>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">You have zero outstanding loan balance</span>
                @endif
            </div>

            <a href="{{ route('member.loans') }}" class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-brand-700 hover:text-brand-900 flex items-center justify-between">
                <span>Loan Details & Repayments</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- Quick Actions 4-Grid (App Experience) -->
    <div class="grid grid-cols-4 gap-2 text-center text-xs">
        <a href="{{ route('member.pay_online') }}" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm hover:shadow active:scale-95 transition flex flex-col items-center">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base mb-1">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <span class="text-[11px] font-bold text-slate-800">Pay Online</span>
        </a>

        <a href="{{ route('member.deposits') }}" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm hover:shadow active:scale-95 transition flex flex-col items-center">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base mb-1">
                <i class="fa-solid fa-book"></i>
            </div>
            <span class="text-[11px] font-bold text-slate-800">Passbook</span>
        </a>

        <a href="{{ route('member.statement') }}" target="_blank" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm hover:shadow active:scale-95 transition flex flex-col items-center">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base mb-1">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <span class="text-[11px] font-bold text-slate-800">Statement</span>
        </a>

        <a href="{{ route('member.payments') }}" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm hover:shadow active:scale-95 transition flex flex-col items-center">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base mb-1">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="text-[11px] font-bold text-slate-800">Receipts</span>
        </a>
    </div>

    <!-- Recent Payments & Receipts -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Recent Payment Receipts</h3>
            <a href="{{ route('member.payments') }}" class="text-xs text-brand-600 font-semibold hover:underline">View All</a>
        </div>

        <div class="divide-y divide-slate-100 mt-2">
            @forelse($member->payments as $payment)
                <div class="py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl {{ $payment->payment_mode === 'online' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-xs">
                            <i class="fa-solid {{ $payment->payment_mode === 'online' ? 'fa-bolt' : 'fa-money-bill-wave' }}"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $payment->receipt_number }}</p>
                            <p class="text-[10px] text-slate-400">{{ $payment->payment_date->format('d M Y') }} • {{ strtoupper($payment->payment_mode) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-emerald-600 font-display">₹{{ number_format($payment->total_received, 0) }}</p>
                        <a href="{{ route('member.receipt', $payment->id) }}" class="text-[10px] text-brand-600 hover:underline">
                            View Receipt <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">No payment receipts yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
