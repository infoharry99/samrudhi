@extends('layouts.admin')

@section('title', 'Admin Dashboard - Shri Samruddhi Society')
@section('header_title', 'Financial Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Greeting & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">System Live & Connected</span>
                <span class="text-xs text-slate-500">FY 2026-2027</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mt-1">Society Operational Overview</h3>
        </div>

        <!-- Quick Actions & Filters -->
        <div class="flex flex-wrap items-center gap-2">
            @if($currentRegister)
                <a href="{{ route('admin.registers.show', $currentRegister->id) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 text-slate-900 hover:bg-amber-400 font-semibold text-xs shadow-sm transition">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Open Current Register</span>
                </a>
            @endif

            <a href="{{ route('admin.collections.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 font-semibold text-xs shadow-sm transition">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Record Cash Payment</span>
            </a>
        </div>
    </div>

    <!-- 10 Dashboard Cards (Section 3) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <!-- 1. Total Members -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Members</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2 font-display">{{ $totalMembers }}</p>
            <span class="text-[11px] text-blue-600 font-medium">Migrated Register Base</span>
        </div>

        <!-- 2. Active Members -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Active Members</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-2 font-display">{{ $activeMembers }}</p>
            <span class="text-[11px] text-slate-500">100% Active in Register</span>
        </div>

        <!-- 3. New Members This Month -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">New This Month</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-indigo-600 mt-2 font-display">{{ $newMembersThisMonth }}</p>
            <span class="text-[11px] text-slate-500">Joining November</span>
        </div>

        <!-- 4. Total Monthly Deposits -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Deposits Pool</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-piggy-bank"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-amber-700 mt-2 font-display">₹{{ number_format($totalDeposits, 0) }}</p>
            <span class="text-[11px] text-amber-700 font-medium">Member Savings Balance</span>
        </div>

        <!-- 5. Total Loans Distributed -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Disbursed</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-purple-700 mt-2 font-display">₹{{ number_format($totalLoansDistributed, 0) }}</p>
            <span class="text-[11px] text-slate-500">{{ $activeLoansCount }} Active Loans</span>
        </div>

        <!-- 6. Total Outstanding Loans -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Outstanding Principal</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-scale-unbalanced"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-rose-700 mt-2 font-display">₹{{ number_format($totalOutstandingLoans, 0) }}</p>
            <span class="text-[11px] text-rose-700 font-medium">Principal Remaining</span>
        </div>

        <!-- 7. Total Interest Collected -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Interest Collected</span>
                <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-percent"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-teal-700 mt-2 font-display">₹{{ number_format($totalInterestCollected, 0) }}</p>
            <span class="text-[11px] text-teal-700 font-medium">Society Income</span>
        </div>

        <!-- 8. Total Collections -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Collections</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-emerald-700 mt-2 font-display">₹{{ number_format($totalCollections, 0) }}</p>
            <span class="text-[11px] text-slate-500">Deposits + Loans + Int</span>
        </div>

        <!-- 9. Pending Payments -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Pending Dues</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-amber-600 mt-2 font-display">₹{{ number_format($pendingPayments, 0) }}</p>
            <span class="text-[11px] text-amber-700">{{ $pendingMembersCount }} Members Pending</span>
        </div>

        <!-- 10. Total Penalties -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Penalties</span>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-red-600 mt-2 font-display">₹{{ number_format($totalPenalties, 0) }}</p>
            <span class="text-[11px] text-slate-500">Late fee / fines</span>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Collections Trend (2 cols) -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Monthly Collections & Deposit Trends</h4>
                    <p class="text-xs text-slate-500">Overview of total collections vs monthly deposits</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-brand-600 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-600"></span> Total Collected
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-600 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Deposits
                    </span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="collectionsChart"></canvas>
            </div>
        </div>

        <!-- Loan Portfolio Distribution (1 col) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-slate-900 text-sm">Member Loan Distribution</h4>
                <p class="text-xs text-slate-500">Active borrowers vs non-borrowers</p>
            </div>
            <div class="h-48 my-auto">
                <canvas id="loanDistChart"></canvas>
            </div>
            <div class="pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                <div>
                    <p class="font-bold text-purple-600">{{ $activeLoansCount }}</p>
                    <p class="text-[10px] text-slate-500">Active Loans</p>
                </div>
                <div>
                    <p class="font-bold text-emerald-600">{{ $completedLoansCount }}</p>
                    <p class="text-[10px] text-slate-500">Completed</p>
                </div>
                <div>
                    <p class="font-bold text-slate-700">{{ $membersWithoutLoan }}</p>
                    <p class="text-[10px] text-slate-500">Savings Only</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Register Quick View & Recent Collections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Register Preview (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Current Month Physical Register Replica</h4>
                    <p class="text-xs text-slate-500">November 2026 Society Collections & Outstanding Balances</p>
                </div>
                @if($currentRegister)
                    <a href="{{ route('admin.registers.show', $currentRegister->id) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800 flex items-center gap-1">
                        <span>Full Register (28 Members)</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Sr.</th>
                            <th class="py-2.5 px-3">Member Name</th>
                            <th class="py-2.5 px-3">Monthly Dep</th>
                            <th class="py-2.5 px-3">Interest</th>
                            <th class="py-2.5 px-3">Installment</th>
                            <th class="py-2.5 px-3">Payable</th>
                            <th class="py-2.5 px-3">Loan Bal</th>
                            <th class="py-2.5 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentDues as $idx => $due)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5 px-3 font-mono text-slate-500">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">
                                    {{ $due->member->full_name }}
                                    <span class="block text-[10px] font-normal text-slate-500">{{ $due->member->mobile_number }}</span>
                                </td>
                                <td class="py-2.5 px-3">₹{{ number_format($due->monthly_deposit, 0) }}</td>
                                <td class="py-2.5 px-3 text-teal-700 font-medium">₹{{ number_format($due->interest, 0) }}</td>
                                <td class="py-2.5 px-3 font-medium">₹{{ number_format($due->monthly_installment, 0) }}</td>
                                <td class="py-2.5 px-3 font-bold text-slate-900">₹{{ number_format($due->total_payable, 0) }}</td>
                                <td class="py-2.5 px-3 text-rose-600 font-medium">₹{{ number_format($due->remaining_loan_balance, 0) }}</td>
                                <td class="py-2.5 px-3">
                                    @if($due->payment_status === 'paid')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">PAID</span>
                                    @elseif($due->payment_status === 'partial')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">PARTIAL</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">PENDING</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-6 text-slate-400">No dues recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-slate-900 text-sm">Recent Receipts</h4>
                <a href="{{ route('admin.collections.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">View All</a>
            </div>

            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                    <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg {{ $payment->payment_mode === 'cash' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }} flex items-center justify-center text-xs">
                                <i class="fa-solid {{ $payment->payment_mode === 'cash' ? 'fa-money-bill-wave' : 'fa-bolt' }}"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 truncate max-w-[130px]">{{ $payment->member->full_name }}</p>
                                <p class="text-[10px] font-mono text-slate-400">{{ $payment->receipt_number }} • {{ $payment->payment_date->format('d M') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-emerald-600 font-display">₹{{ number_format($payment->total_received, 0) }}</p>
                            <a href="{{ route('admin.collections.receipt', $payment->id) }}" class="text-[10px] text-brand-600 hover:underline">Receipt</a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">No recent payments recorded.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Collections & Deposits Chart
    const ctxCollections = document.getElementById('collectionsChart');
    if (ctxCollections) {
        new Chart(ctxCollections, {
            type: 'bar',
            data: {
                labels: @json($chartMonths),
                datasets: [
                    {
                        label: 'Total Collections (₹)',
                        data: @json($collectionsChartData),
                        backgroundColor: '#2563eb',
                        borderRadius: 6,
                    },
                    {
                        label: 'Deposits (₹)',
                        data: @json($depositsChartData),
                        backgroundColor: '#f59e0b',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) { return '₹' + (val / 1000) + 'k'; }
                        }
                    }
                }
            }
        });
    }

    // Loan Distribution Doughnut Chart
    const ctxLoan = document.getElementById('loanDistChart');
    if (ctxLoan) {
        new Chart(ctxLoan, {
            type: 'doughnut',
            data: {
                labels: ['Active Loans', 'Completed Loans', 'Savings Only'],
                datasets: [{
                    data: [{{ $activeLoansCount }}, {{ $completedLoansCount }}, {{ $membersWithoutLoan }}],
                    backgroundColor: ['#9333ea', '#10b981', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '70%'
            }
        });
    }
</script>
@endpush
