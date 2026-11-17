@extends('layouts.member')

@section('title', 'Pay Dues Online - Shri Samruddhi Society')

@section('content')
<div class="space-y-4 max-w-lg mx-auto">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 font-display">Pay Outstanding Dues Online</h2>
        <a href="{{ route('member.dashboard') }}" class="text-xs text-brand-600 font-semibold hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    <!-- Payment Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form action="{{ route('member.process_online_payment') }}" method="POST" id="onlinePayForm" class="space-y-4">
            @csrf

            @if($currentDue)
                <input type="hidden" name="monthly_due_id" value="{{ $currentDue->id }}">
                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                    <div class="flex items-center justify-between font-bold text-amber-900 mb-1">
                        <span>Current Due: {{ $currentDue->monthlyRegister->title }}</span>
                        <span>₹{{ number_format($currentDue->total_payable - $currentDue->amount_paid, 0) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-1 text-[11px] text-amber-800">
                        <span>Monthly Deposit: ₹{{ number_format($currentDue->monthly_deposit, 0) }}</span>
                        <span>Loan Interest: ₹{{ number_format($currentDue->interest, 0) }}</span>
                        <span>Loan Installment: ₹{{ number_format($currentDue->monthly_installment, 0) }}</span>
                        <span>Penalty: ₹{{ number_format($currentDue->penalty, 0) }}</span>
                    </div>
                </div>
            @endif

            <!-- Amount Input -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Amount (₹) *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold">₹</span>
                    <input type="number" step="1" name="amount" id="payAmountInput"
                        value="{{ old('amount', $defaultAmount) }}"
                        required min="10"
                        class="w-full pl-8 pr-3 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-lg text-slate-900 focus:border-brand-600 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
            </div>

            <!-- Payment Method Tabs -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Payment Mode</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-950 cursor-pointer transition active:scale-95 has-[:checked]:ring-2 has-[:checked]:ring-emerald-600">
                        <input type="radio" name="payment_method" value="upi" checked class="sr-only" onchange="togglePayView('upi')">
                        <i class="fa-solid fa-qrcode text-lg text-emerald-600 mb-1"></i>
                        <span class="text-xs font-bold">UPI QR</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 cursor-pointer transition active:scale-95 has-[:checked]:ring-2 has-[:checked]:ring-brand-600">
                        <input type="radio" name="payment_method" value="card" class="sr-only" onchange="togglePayView('card')">
                        <i class="fa-solid fa-credit-card text-lg text-brand-600 mb-1"></i>
                        <span class="text-xs font-bold">Debit / Card</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 cursor-pointer transition active:scale-95 has-[:checked]:ring-2 has-[:checked]:ring-indigo-600">
                        <input type="radio" name="payment_method" value="netbanking" class="sr-only" onchange="togglePayView('netbanking')">
                        <i class="fa-solid fa-landmark text-lg text-indigo-600 mb-1"></i>
                        <span class="text-xs font-bold">Net Banking</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic UPI QR Display Box -->
            <div id="upiBox" class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                <p class="text-xs font-bold text-slate-800 mb-2">Scan Society UPI QR with any UPI App</p>

                <!-- High Quality SVG QR Code Replica -->
                <div class="inline-block p-3 bg-white rounded-2xl shadow border border-slate-200 mb-2">
                    <svg viewBox="0 0 100 100" class="w-40 h-40 mx-auto">
                        <!-- Simulated UPI QR Code Matrix -->
                        <rect width="100" height="100" fill="#ffffff" />
                        <!-- Top Left Position Target -->
                        <rect x="10" y="10" width="24" height="24" fill="#000000" rx="2" />
                        <rect x="14" y="14" width="16" height="16" fill="#ffffff" />
                        <rect x="18" y="18" width="8" height="8" fill="#000000" />
                        <!-- Top Right Position Target -->
                        <rect x="66" y="10" width="24" height="24" fill="#000000" rx="2" />
                        <rect x="70" y="14" width="16" height="16" fill="#ffffff" />
                        <rect x="74" y="18" width="8" height="8" fill="#000000" />
                        <!-- Bottom Left Position Target -->
                        <rect x="10" y="66" width="24" height="24" fill="#000000" rx="2" />
                        <rect x="14" y="70" width="16" height="16" fill="#ffffff" />
                        <rect x="18" y="74" width="8" height="8" fill="#000000" />
                        <!-- QR Data modules -->
                        <rect x="40" y="12" width="6" height="6" fill="#000" />
                        <rect x="50" y="18" width="6" height="6" fill="#000" />
                        <rect x="38" y="28" width="6" height="6" fill="#000" />
                        <rect x="52" y="32" width="6" height="6" fill="#000" />
                        <rect x="12" y="42" width="6" height="6" fill="#000" />
                        <rect x="24" y="48" width="6" height="6" fill="#000" />
                        <rect x="38" y="42" width="24" height="24" fill="#1e40af" rx="4" />
                        <text x="50" y="58" font-family="Arial" font-size="14" font-weight="bold" fill="#ffffff" text-anchor="middle">₹</text>
                        <rect x="66" y="42" width="6" height="6" fill="#000" />
                        <rect x="80" y="48" width="6" height="6" fill="#000" />
                        <rect x="42" y="72" width="6" height="6" fill="#000" />
                        <rect x="54" y="78" width="6" height="6" fill="#000" />
                        <rect x="68" y="68" width="6" height="6" fill="#000" />
                        <rect x="82" y="80" width="6" height="6" fill="#000" />
                    </svg>
                </div>

                <div class="text-xs">
                    <p class="font-bold text-slate-800">{{ $upiName }}</p>
                    <p class="font-mono text-brand-700 bg-brand-50 inline-block px-2 py-0.5 rounded text-[11px] mt-0.5">
                        {{ $upiId }}
                    </p>
                    <p class="text-[10px] text-slate-500 mt-1">GPay • PhonePe • Paytm • BHIM • Amazon Pay</p>
                </div>

                <div class="mt-3 text-left">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Optional: Enter UPI UTR / Ref No.</label>
                    <input type="text" name="utr_reference" placeholder="e.g. 12-digit UTR from your UPI App" class="w-full px-3 py-1.5 border rounded-lg text-xs font-mono outline-none">
                </div>
            </div>

            <!-- Card simulation box -->
            <div id="cardBox" class="hidden p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <p class="text-xs font-bold text-slate-800">Enter Debit or Credit Card</p>
                <input type="text" placeholder="Card Number (4111 2222 3333 4444)" class="w-full px-3 py-2 border rounded-xl text-xs font-mono outline-none">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" placeholder="MM/YY" class="px-3 py-2 border rounded-xl text-xs font-mono outline-none">
                    <input type="password" placeholder="CVV" maxlength="3" class="px-3 py-2 border rounded-xl text-xs font-mono outline-none">
                </div>
            </div>

            <!-- Netbanking simulation box -->
            <div id="netbankingBox" class="hidden p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs font-bold text-slate-800 mb-2">Select Your Bank</p>
                <select class="w-full px-3 py-2 border rounded-xl text-xs outline-none">
                    <option>State Bank of India (SBI)</option>
                    <option>HDFC Bank</option>
                    <option>ICICI Bank</option>
                    <option>Axis Bank</option>
                    <option>Bank of Baroda</option>
                    <option>Gujarat State Co-op Bank</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock"></i>
                <span>Complete Payment Now</span>
            </button>

            <p class="text-[10px] text-slate-400 text-center">
                <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>
                256-bit Secure Gateway Simulation • Instant Digital Receipt Generated Upon Approval
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePayView(mode) {
        document.getElementById('upiBox').classList.toggle('hidden', mode !== 'upi');
        document.getElementById('cardBox').classList.toggle('hidden', mode !== 'card');
        document.getElementById('netbankingBox').classList.toggle('hidden', mode !== 'netbanking');
    }
</script>
@endpush
