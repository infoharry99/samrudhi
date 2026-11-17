@extends('layouts.admin')

@section('title', 'Sanction New Loan - Shri Samruddhi Society')
@section('header_title', 'Create & Sanction Loan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">New Loan Application</h2>
            <p class="text-xs text-slate-500">Sanction a member loan with configurable interest calculation method</p>
        </div>
        <a href="{{ route('admin.loans.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Loans
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <form action="{{ route('admin.loans.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Member *</label>
                    <select name="member_id" id="memberSelect" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">-- Choose Member --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->member_code }} - {{ $m->full_name }} ({{ $m->mobile_number }}) | Savings: ₹{{ number_format($m->current_deposit_balance, 0) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Loan Account Number *</label>
                    <input type="text" name="loan_number" value="{{ old('loan_number', $suggestedNumber) }}" required class="w-full px-3 py-2 border rounded-xl text-xs font-mono font-bold bg-slate-50 outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Loan Sanction Amount (₹) *</label>
                    <input type="number" step="1000" name="loan_amount" id="loanAmount" value="{{ old('loan_amount', '50000') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Monthly Interest Rate (%) *</label>
                    <input type="number" step="0.1" name="interest_rate" id="interestRate" value="{{ old('interest_rate', '1.0') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                    <span class="text-[10px] text-slate-400">Default: 1.0% per month (= 12% p.a.)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Interest Calculation Method *</label>
                    <select name="interest_calculation_method" id="calcMethod" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="reducing_balance" selected>Reducing Balance (Recommended)</option>
                        <option value="flat">Flat Interest</option>
                        <option value="fixed_monthly">Fixed Monthly Interest</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Monthly Principal Installment (₹) *</label>
                    <input type="number" step="100" name="monthly_installment" id="monthlyInstallment" value="{{ old('monthly_installment', '5000') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono font-bold text-purple-700">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tenure / Duration (Months) *</label>
                    <input type="number" name="loan_duration_months" id="loanDuration" value="{{ old('loan_duration_months', '12') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Application Date *</label>
                    <input type="date" name="application_date" value="{{ old('application_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">First Installment Due Date *</label>
                    <input type="date" name="first_installment_date" value="{{ old('first_installment_date', date('Y-m-01', strtotime('+1 month'))) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Loan Purpose</label>
                    <input type="text" name="loan_purpose" value="{{ old('loan_purpose') }}" placeholder="e.g. Household renovation, medical emergency, business need" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="active" selected>Active & Disbursed Immediately</option>
                        <option value="pending">Pending Approval</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Approval Date</label>
                    <input type="date" name="approval_date" value="{{ old('approval_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <!-- Real-time calculation preview card -->
            <div class="mt-4 p-4 rounded-xl bg-purple-50 border border-purple-200 text-xs">
                <h4 class="font-bold text-purple-900 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Automatic Calculation Preview:</span>
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-purple-950">
                    <div>
                        <span class="text-purple-700 block text-[10px]">First Month Interest:</span>
                        <span id="previewInterest" class="font-bold font-mono text-sm">₹500.00</span>
                    </div>
                    <div>
                        <span class="text-purple-700 block text-[10px]">Monthly Principal EMI:</span>
                        <span id="previewPrincipal" class="font-bold font-mono text-sm">₹5,000.00</span>
                    </div>
                    <div>
                        <span class="text-purple-700 block text-[10px]">Estimated Total Monthly:</span>
                        <span id="previewTotal" class="font-bold font-mono text-sm text-brand-800">₹5,500.00</span>
                    </div>
                    <div>
                        <span class="text-purple-700 block text-[10px]">Interest Treatment:</span>
                        <span class="font-medium text-emerald-700">Does NOT reduce principal</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.loans.index') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-semibold text-xs shadow-md transition">
                    Create & Sanction Loan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateLoanPreview() {
        const amt = parseFloat(document.getElementById('loanAmount').value) || 0;
        const rate = parseFloat(document.getElementById('interestRate').value) || 0;
        const inst = parseFloat(document.getElementById('monthlyInstallment').value) || 0;

        const firstInterest = (amt * rate) / 100;
        const totalFirst = inst + firstInterest;

        document.getElementById('previewInterest').textContent = '₹' + firstInterest.toFixed(2);
        document.getElementById('previewPrincipal').textContent = '₹' + inst.toFixed(2);
        document.getElementById('previewTotal').textContent = '₹' + totalFirst.toFixed(2);
    }

    document.getElementById('loanAmount').addEventListener('input', updateLoanPreview);
    document.getElementById('interestRate').addEventListener('input', updateLoanPreview);
    document.getElementById('monthlyInstallment').addEventListener('input', updateLoanPreview);
    updateLoanPreview();
</script>
@endpush
