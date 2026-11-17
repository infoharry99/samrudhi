@extends('layouts.admin')

@section('title', 'Record Cash Payment - Shri Samruddhi Society')
@section('header_title', 'Record Cash Collection')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Record Cash Collection</h2>
            <p class="text-xs text-slate-500">Record physical cash received from member and generate instant receipt</p>
        </div>
        <a href="{{ route('admin.collections.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Collections
        </a>
    </div>

    <!-- Active Monthly Due Pre-fill Note if coming from register -->
    @if($selectedDue)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-600 text-base"></i>
                <div>
                    <span class="font-bold">Register Due Selected:</span> {{ $selectedDue->member->full_name }} ({{ $selectedDue->member->member_code }})
                    <span class="block text-[11px] text-amber-700">Total Monthly Payable: ₹{{ number_format($selectedDue->total_payable, 2) }}</span>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded bg-amber-200 font-bold font-mono">₹{{ number_format($selectedDue->total_payable, 2) }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <form action="{{ route('admin.collections.store') }}" method="POST" class="space-y-4">
            @csrf
            @if($selectedDue)
                <input type="hidden" name="monthly_due_id" value="{{ $selectedDue->id }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Member *</label>
                    <select name="member_id" id="memberSelect" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">-- Choose Member --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ (old('member_id', $selectedMemberId ?? ($selectedDue ? $selectedDue->member_id : null)) == $m->id) ? 'selected' : '' }}>
                                {{ $m->member_code }} - {{ $m->full_name }} ({{ $m->mobile_number }}) | Outstanding Loan: ₹{{ number_format($m->current_loan_balance, 0) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Receipt Number (Automatic)</label>
                    <input type="text" value="{{ $suggestedReceipt }}" readonly class="w-full px-3 py-2 border rounded-xl text-xs font-mono font-bold bg-slate-100 text-slate-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Collection Month (YYYY-MM) *</label>
                    <input type="month" name="payment_month" value="{{ old('payment_month', date('Y-m')) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Mode *</label>
                    <select name="payment_mode" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="cash" selected>Cash Payment</option>
                        <option value="online">Online / Direct UPI / Bank Transfer</option>
                    </select>
                </div>
            </div>

            <!-- Detailed Breakdown Section -->
            <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                <h4 class="font-bold text-slate-800 text-xs mb-3 flex items-center justify-between">
                    <span>Payment Allocation Breakdown</span>
                    <span class="text-[10px] text-slate-400 font-normal">Amounts will update respective ledgers separately</span>
                </h4>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-600 mb-1">1. Monthly Deposit (₹)</label>
                        <input type="number" step="0.01" name="deposit_portion" id="depPortion"
                            value="{{ old('deposit_portion', $selectedDue ? $selectedDue->monthly_deposit : '1000.00') }}"
                            required class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono text-amber-800 font-bold outline-none focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">2. Loan Interest (₹)</label>
                        <input type="number" step="0.01" name="interest_portion" id="intPortion"
                            value="{{ old('interest_portion', $selectedDue ? $selectedDue->interest : '0.00') }}"
                            required class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono text-teal-800 font-bold outline-none focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">3. Loan Installment (₹)</label>
                        <input type="number" step="0.01" name="installment_portion" id="instPortion"
                            value="{{ old('installment_portion', $selectedDue ? $selectedDue->monthly_installment : '0.00') }}"
                            required class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono text-purple-800 font-bold outline-none focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">4. Penalty / Fine (₹)</label>
                        <input type="number" step="0.01" name="penalty_portion" id="penPortion"
                            value="{{ old('penalty_portion', $selectedDue ? $selectedDue->penalty : '0.00') }}"
                            required class="w-full px-2.5 py-1.5 border rounded-lg text-xs font-mono text-rose-800 font-bold outline-none focus:ring-1 focus:ring-brand-500">
                    </div>
                </div>

                <!-- Total Received Calculator -->
                <div class="mt-4 pt-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Total Received Amount (₹) *</span>
                        <span class="text-[10px] text-slate-500">Must equal sum of (Deposit + Interest + Installment + Penalty)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.01" name="total_received" id="totalReceived"
                            value="{{ old('total_received', $selectedDue ? $selectedDue->total_payable : '1000.00') }}"
                            required class="w-40 px-3 py-2 border-2 border-emerald-500 rounded-xl text-base font-mono font-extrabold text-emerald-800 text-right outline-none">
                        <button type="button" onclick="autoSum()" class="px-2.5 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg text-[11px] font-bold text-slate-700" title="Calculate sum">
                            Auto Sum
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Transaction Ref / Cash Receipt Voucher No</label>
                    <input type="text" name="transaction_reference" value="{{ old('transaction_reference', 'CASH-REC-' . rand(1000, 9999)) }}" class="w-full px-3 py-2 border rounded-xl text-xs font-mono outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Remarks / Note (Optional)</label>
                    <input type="text" name="remarks" value="{{ old('remarks', 'Physical cash received in office') }}" placeholder="e.g. Paid in full" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.collections.index') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Record Collection & Issue Receipt</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function autoSum() {
        const dep = parseFloat(document.getElementById('depPortion').value) || 0;
        const int = parseFloat(document.getElementById('intPortion').value) || 0;
        const inst = parseFloat(document.getElementById('instPortion').value) || 0;
        const pen = parseFloat(document.getElementById('penPortion').value) || 0;

        const sum = dep + int + inst + pen;
        document.getElementById('totalReceived').value = sum.toFixed(2);
    }

    document.getElementById('depPortion').addEventListener('input', autoSum);
    document.getElementById('intPortion').addEventListener('input', autoSum);
    document.getElementById('instPortion').addEventListener('input', autoSum);
    document.getElementById('penPortion').addEventListener('input', autoSum);
</script>
@endpush
