@extends('layouts.admin')

@section('title', 'Reports & Financial Statements - Shri Samruddhi Society')
@section('header_title', 'Accounting & Financial Reports')

@section('content')
<div class="space-y-6">
    <!-- Overall Financial Health Strip (Section 10) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Total Member Savings</span>
            <span class="text-xl font-bold text-amber-700 font-display mt-1 block">₹{{ number_format($financialSummary['total_deposits_pool'], 0) }}</span>
            <span class="text-[10px] text-slate-400">Total accumulated deposits</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Total Loan Sanctions</span>
            <span class="text-xl font-bold text-purple-700 font-display mt-1 block">₹{{ number_format($financialSummary['total_loans_disbursed'], 0) }}</span>
            <span class="text-[10px] text-purple-700">Cumulative disbursement</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Principal Repaid</span>
            <span class="text-xl font-bold text-emerald-600 font-display mt-1 block">₹{{ number_format($financialSummary['total_principal_repaid'], 0) }}</span>
            <span class="text-[10px] text-emerald-600">Recovered principal</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Outstanding Loans</span>
            <span class="text-xl font-bold text-rose-600 font-display mt-1 block">₹{{ number_format($financialSummary['total_outstanding_loans'], 0) }}</span>
            <span class="text-[10px] text-rose-600">Remaining to collect</span>
        </div>
    </div>

    <!-- Filter & Export Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Filter Member</label>
                    <select name="member_id" class="px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">All Members</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ $memberId == $m->id ? 'selected' : '' }}>{{ $m->member_code }} - {{ $m->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="self-end">
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 transition">
                        Apply Filter
                    </button>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export', ['type' => $tab]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Export {{ ucfirst($tab) }} (CSV)</span>
                </a>
            </div>
        </form>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-2 mt-5 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.reports.index', ['tab' => 'collections', 'start_date' => $startDate, 'end_date' => $endDate, 'member_id' => $memberId]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $tab === 'collections' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Collections Report
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'loans', 'start_date' => $startDate, 'end_date' => $endDate, 'member_id' => $memberId]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $tab === 'loans' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Loans Portfolio Report
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'deposits', 'start_date' => $startDate, 'end_date' => $endDate, 'member_id' => $memberId]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $tab === 'deposits' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Deposit Contributions Report
            </a>
        </div>
    </div>

    <!-- Active Tab Content -->
    @if($tab === 'collections')
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
                            <th class="py-3 px-3 text-right font-extrabold">Total Paid</th>
                            <th class="py-3 px-3 text-center">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($collections as $col)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3 font-mono font-bold text-brand-700">{{ $col->receipt_number }}</td>
                                <td class="py-2.5 px-3 text-slate-500">{{ $col->payment_date->format('d-m-Y') }}</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $col->member->full_name }} ({{ $col->member->member_code }})</td>
                                <td class="py-2.5 px-3 text-right font-mono text-amber-700">₹{{ number_format($col->deposit_portion, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-teal-700">₹{{ number_format($col->interest_portion, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-purple-700">₹{{ number_format($col->installment_portion, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-rose-600">₹{{ number_format($col->penalty_portion, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900 text-sm">₹{{ number_format($col->total_received, 0) }}</td>
                                <td class="py-2.5 px-3 text-center uppercase font-mono text-[10px]">{{ $col->payment_mode }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-6 text-slate-400">No collection records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($tab === 'loans')
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Loan Account</th>
                            <th class="py-3 px-3">Member Details</th>
                            <th class="py-3 px-3 text-right">Disbursed</th>
                            <th class="py-3 px-3 text-center">Rate</th>
                            <th class="py-3 px-3 text-right">EMI</th>
                            <th class="py-3 px-3 text-right">Principal Repaid</th>
                            <th class="py-3 px-3 text-right">Remaining Principal</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($loans as $loan)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3 font-mono font-bold text-purple-700">{{ $loan->loan_number }}</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $loan->member->full_name }} ({{ $loan->member->member_code }})</td>
                                <td class="py-2.5 px-3 text-right font-mono">₹{{ number_format($loan->loan_amount, 0) }}</td>
                                <td class="py-2.5 px-3 text-center">{{ $loan->interest_rate }}%</td>
                                <td class="py-2.5 px-3 text-right font-mono">₹{{ number_format($loan->monthly_installment, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-emerald-600 font-bold">₹{{ number_format($loan->total_principal_paid, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-bold">₹{{ number_format($loan->remaining_principal, 0) }}</td>
                                <td class="py-2.5 px-3 text-center uppercase font-bold text-[10px]">{{ $loan->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-6 text-slate-400">No loan records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Member Details</th>
                            <th class="py-3 px-3">Transaction Type</th>
                            <th class="py-3 px-3 text-right">Deposit Amount (₹)</th>
                            <th class="py-3 px-3 text-right">Cumulative Balance (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($deposits as $dep)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3 font-mono text-slate-500">{{ $dep->transaction_date->format('d-m-Y') }}</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $dep->member->full_name }} ({{ $dep->member->member_code }})</td>
                                <td class="py-2.5 px-3 text-slate-700">{{ str_replace('_', ' ', $dep->transaction_type) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600">+₹{{ number_format($dep->amount, 0) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">₹{{ number_format($dep->balance_after, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-6 text-slate-400">No deposit records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
