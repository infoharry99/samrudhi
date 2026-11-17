@extends('layouts.admin')

@section('title', 'Preview Migration Data - Shri Samruddhi Society')
@section('header_title', 'Migration Data Preview & Validation')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Data Validation & Preview ({{ count($rows) }} Entries)</h2>
            <p class="text-xs text-slate-500">Review member details and opening balances before committing them to the live system</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.migration.index') }}" class="px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100">
                Cancel
            </a>

            <form action="{{ route('admin.migration.process') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Approve & Import Valid Records</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Member Name</th>
                        <th class="py-3 px-3">Mobile</th>
                        <th class="py-3 px-3">Joining Date</th>
                        <th class="py-3 px-3 text-right">Initial Savings</th>
                        <th class="py-3 px-3 text-right">Loan Amount</th>
                        <th class="py-3 px-3 text-right">Remaining Principal</th>
                        <th class="py-3 px-3 text-right">Installment</th>
                        <th class="py-3 px-3">Validation Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rows as $row)
                        <tr class="hover:bg-slate-50 {{ !$row['valid'] ? 'bg-rose-50/50' : '' }}">
                            <td class="py-2.5 px-3">
                                @if($row['valid'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">READY</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">INVALID</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $row['name'] }}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-700">{{ $row['mobile'] }}</td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $row['joining_date'] }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-amber-700">₹{{ number_format($row['initial_savings'], 0) }}</td>
                            <td class="py-2.5 px-3 text-right font-mono">₹{{ number_format($row['loan_amount'], 0) }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-600">₹{{ number_format($row['rem_loan'], 0) }}</td>
                            <td class="py-2.5 px-3 text-right font-mono">₹{{ number_format($row['installment'], 0) }}</td>
                            <td class="py-2.5 px-3 text-[11px]">
                                @if($row['valid'])
                                    <span class="text-emerald-600 font-semibold">Passes all validation checks</span>
                                @else
                                    <span class="text-rose-600 font-semibold">{{ implode(', ', $row['errors']) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
