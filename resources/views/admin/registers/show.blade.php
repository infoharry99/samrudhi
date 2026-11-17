@extends('layouts.admin')

@section('title', $monthlyRegister->title . ' - Physical Register Replica')
@section('header_title', 'Monthly Register Replica')

@section('content')
<div class="space-y-5">
    <!-- Header Controls & Register Banner -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                    <i class="fa-solid fa-book-bookmark mr-1"></i> Physical Register Layout
                </span>
                <span class="text-xs text-slate-500">
                    Generated: {{ $monthlyRegister->generated_date ? $monthlyRegister->generated_date->format('d M Y') : 'Auto' }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mt-1 font-display">{{ $monthlyRegister->title }}</h2>
            <p class="text-xs text-slate-500">Digitized exact replica of physical paper register with real-time automatic accounting</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.registers.print', $monthlyRegister->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                <span>Print Register</span>
            </a>

            <a href="{{ route('admin.registers.export', $monthlyRegister->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>

            <a href="{{ route('admin.registers.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Switch Month</span>
            </a>
        </div>
    </div>

    <!-- Register Financial Summary Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Expected</span>
            <span class="text-lg font-bold text-slate-900 font-display">₹{{ number_format($summary['total_payable'], 0) }}</span>
            <span class="text-[10px] text-slate-400 block">{{ $summary['total_members'] }} Members</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Collected</span>
            <span class="text-lg font-bold text-emerald-600 font-display">₹{{ number_format($summary['total_collected'], 0) }}</span>
            <span class="text-[10px] text-emerald-600 font-semibold block">{{ $summary['paid_count'] }} Paid</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Pending Amount</span>
            <span class="text-lg font-bold text-rose-600 font-display">₹{{ number_format($summary['total_pending'], 0) }}</span>
            <span class="text-[10px] text-rose-500 font-semibold block">{{ $summary['pending_count'] }} Pending</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Monthly Deposits</span>
            <span class="text-lg font-bold text-amber-600 font-display">₹{{ number_format($summary['total_deposit'], 0) }}</span>
            <span class="text-[10px] text-amber-600 block">Savings share</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Loan Interest</span>
            <span class="text-lg font-bold text-teal-600 font-display">₹{{ number_format($summary['total_interest'], 0) }}</span>
            <span class="text-[10px] text-teal-600 block">Society income</span>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Principal Repayment</span>
            <span class="text-lg font-bold text-purple-600 font-display">₹{{ number_format($summary['total_installment'], 0) }}</span>
            <span class="text-[10px] text-purple-600 block">Loan principal</span>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, mobile, ID..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-1.5 px-3 rounded-lg border border-slate-300 text-xs focus:ring-1 focus:ring-brand-500 outline-none">
                <option value="">All Statuses ({{ $summary['total_members'] }})</option>
                <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid Only ({{ $summary['paid_count'] }})</option>
                <option value="partial" {{ $statusFilter === 'partial' ? 'selected' : '' }}>Partial Only ({{ $summary['partial_count'] }})</option>
                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending Only ({{ $summary['pending_count'] }})</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700">Filter</button>
            @if($search || $statusFilter)
                <a href="{{ route('admin.registers.show', $monthlyRegister->id) }}" class="px-2.5 py-1.5 text-slate-500 hover:text-slate-800 text-xs">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-500 text-right w-full sm:w-auto">
            Showing <span class="font-bold text-slate-800">{{ $dues->count() }}</span> member entries
        </div>
    </div>

    <!-- The Exact Physical Register Replica Table -->
    <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
        <!-- Physical Register Top Header Ribbon -->
        <div class="bg-amber-50 border-b-2 border-amber-200 px-4 py-2.5 flex items-center justify-between text-xs text-amber-900 font-semibold">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-file-pen text-amber-700"></i>
                <span>SHRI SAMRUDDHI CO-OP SOCIETY — MONTHLY COLLECTION REGISTER (PHYSICAL REPLICA)</span>
            </div>
            <span class="font-mono text-[11px] bg-amber-200/60 px-2 py-0.5 rounded text-amber-900">
                Formula: Monthly Payable = Deposit + Interest + Installment + Penalty
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-200/90 text-slate-900 font-bold uppercase tracking-wider text-[11px] border-b-2 border-slate-300">
                    <tr>
                        <th class="py-3 px-2.5 text-center border-r border-slate-300 w-12">Sr. No.</th>
                        <th class="py-3 px-3 border-r border-slate-300 min-w-[170px]">Member Name & Mobile</th>
                        <th class="py-3 px-3 text-right border-r border-slate-300 bg-amber-50/50">Monthly Deposit (₹)</th>
                        <th class="py-3 px-3 text-right border-r border-slate-300">Loan Amount (₹)</th>
                        <th class="py-3 px-3 text-right border-r border-slate-300 text-teal-900 bg-teal-50/40">Interest (₹)</th>
                        <th class="py-3 px-3 text-right border-r border-slate-300 text-purple-900 bg-purple-50/40">Monthly Installment (₹)</th>
                        <th class="py-3 px-2.5 text-right border-r border-slate-300 text-rose-900">Penalty (₹)</th>
                        <th class="py-3 px-3 text-right border-r-2 border-slate-400 bg-blue-50/70 font-extrabold text-blue-950">Monthly Payable (₹)</th>
                        <th class="py-3 px-3 text-right border-r border-slate-300 text-rose-700">Loan Balance (₹)</th>
                        <th class="py-3 px-2.5 text-center border-r border-slate-300">Payment Status</th>
                        <th class="py-3 px-2.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($dues as $idx => $due)
                        <tr class="hover:bg-amber-50/40 transition {{ $due->payment_status === 'paid' ? 'bg-emerald-50/20' : ($due->payment_status === 'partial' ? 'bg-amber-50/30' : '') }}">
                            <!-- Sr. No. -->
                            <td class="py-3 px-2.5 text-center font-mono text-slate-600 font-semibold border-r border-slate-200">
                                {{ $idx + 1 }}
                            </td>

                            <!-- Member Name & Mobile -->
                            <td class="py-3 px-3 border-r border-slate-200">
                                <a href="{{ route('admin.members.show', $due->member_id) }}" class="font-bold text-slate-900 hover:text-brand-600 block">
                                    {{ $due->member->full_name }}
                                </a>
                                <div class="flex items-center gap-2 text-[10px] text-slate-500 font-mono mt-0.5">
                                    <span class="font-semibold text-slate-700">{{ $due->member->member_code }}</span>
                                    <span>•</span>
                                    <span>{{ $due->member->mobile_number }}</span>
                                </div>
                            </td>

                            <!-- Monthly Deposit -->
                            <td class="py-3 px-3 text-right font-medium text-slate-800 border-r border-slate-200 bg-amber-50/20 font-mono">
                                {{ number_format($due->monthly_deposit, 0) }}
                            </td>

                            <!-- Loan Amount -->
                            <td class="py-3 px-3 text-right font-mono text-slate-600 border-r border-slate-200">
                                {{ $due->loan_amount > 0 ? number_format($due->loan_amount, 0) : '-' }}
                            </td>

                            <!-- Interest -->
                            <td class="py-3 px-3 text-right font-mono font-medium text-teal-700 border-r border-slate-200 bg-teal-50/20">
                                {{ $due->interest > 0 ? number_format($due->interest, 0) : '-' }}
                            </td>

                            <!-- Monthly Installment -->
                            <td class="py-3 px-3 text-right font-mono font-medium text-purple-700 border-r border-slate-200 bg-purple-50/20">
                                {{ $due->monthly_installment > 0 ? number_format($due->monthly_installment, 0) : '-' }}
                            </td>

                            <!-- Penalty -->
                            <td class="py-3 px-2.5 text-right font-mono text-rose-600 border-r border-slate-200">
                                {{ $due->penalty > 0 ? number_format($due->penalty, 0) : '0' }}
                            </td>

                            <!-- Monthly Payable (Bold Highlighted) -->
                            <td class="py-3 px-3 text-right font-mono font-extrabold text-sm text-slate-900 border-r-2 border-slate-400 bg-blue-50/40">
                                ₹{{ number_format($due->total_payable, 0) }}
                                @if($due->amount_paid > 0 && $due->payment_status !== 'paid')
                                    <span class="block text-[10px] font-normal text-emerald-600">Paid: ₹{{ number_format($due->amount_paid, 0) }}</span>
                                @endif
                            </td>

                            <!-- Loan Balance -->
                            <td class="py-3 px-3 text-right font-mono font-semibold text-rose-600 border-r border-slate-200">
                                {{ $due->remaining_loan_balance > 0 ? '₹' . number_format($due->remaining_loan_balance, 0) : '-' }}
                            </td>

                            <!-- Payment Status -->
                            <td class="py-3 px-2.5 text-center border-r border-slate-200">
                                @if($due->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> PAID
                                    </span>
                                    @if($due->payment_mode)
                                        <span class="block text-[9px] uppercase font-mono text-slate-400 mt-0.5">{{ $due->payment_mode }}</span>
                                    @endif
                                @elseif($due->payment_status === 'partial')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-circle-half-stroke text-[9px]"></i> PARTIAL
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> PENDING
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($due->payment_status !== 'paid')
                                        <a href="{{ route('admin.collections.create', ['member_id' => $due->member_id, 'due_id' => $due->id]) }}" class="px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold transition" title="Record Cash Payment">
                                            <i class="fa-solid fa-money-bill-wave mr-0.5"></i> Pay
                                        </a>
                                    @else
                                        <span class="text-emerald-600 text-xs font-semibold px-2 py-1">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                    @endif

                                    <!-- Edit Dues Modal Trigger -->
                                    <button type="button" onclick="openEditModal({{ $due->id }}, {{ $due->monthly_deposit }}, {{ $due->interest }}, {{ $due->monthly_installment }}, {{ $due->penalty }}, '{{ addslashes($due->member->full_name) }}')" class="p-1 text-slate-400 hover:text-slate-700 rounded hover:bg-slate-100" title="Adjust Due Amounts">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-8 text-slate-400 text-sm">
                                No member entries found in this register.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Register Totals Row -->
                <tfoot class="bg-slate-100 font-bold text-slate-900 border-t-2 border-slate-300">
                    <tr>
                        <td colspan="2" class="py-3 px-3 text-right uppercase border-r border-slate-300 font-bold">TOTALS:</td>
                        <td class="py-3 px-3 text-right border-r border-slate-300 font-mono text-amber-900">₹{{ number_format($summary['total_deposit'], 0) }}</td>
                        <td class="py-3 px-3 text-right border-r border-slate-300 font-mono">-</td>
                        <td class="py-3 px-3 text-right border-r border-slate-300 font-mono text-teal-800">₹{{ number_format($summary['total_interest'], 0) }}</td>
                        <td class="py-3 px-3 text-right border-r border-slate-300 font-mono text-purple-800">₹{{ number_format($summary['total_installment'], 0) }}</td>
                        <td class="py-3 px-2.5 text-right border-r border-slate-300 font-mono text-rose-700">₹{{ number_format($summary['total_penalty'], 0) }}</td>
                        <td class="py-3 px-3 text-right border-r-2 border-slate-400 font-mono text-sm bg-blue-100/60 text-blue-950 font-extrabold">₹{{ number_format($summary['total_payable'], 0) }}</td>
                        <td class="py-3 px-3 text-right border-r border-slate-300 font-mono text-rose-700">-</td>
                        <td class="py-3 px-2.5 text-center border-r border-slate-300 text-emerald-700 font-mono font-bold">₹{{ number_format($summary['total_collected'], 0) }}</td>
                        <td class="py-3 px-2.5 text-center text-slate-400">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Adjusting Dues -->
<div id="editDueModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="closeEditModal()" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Adjust Register Dues</h3>
                <p id="modalMemberName" class="text-xs text-brand-600 font-semibold mt-0.5"></p>
            </div>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editDueForm" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Monthly Deposit (Savings)</label>
                <input type="number" step="0.01" name="monthly_deposit" id="modalDeposit" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Loan Interest</label>
                <input type="number" step="0.01" name="interest" id="modalInterest" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Monthly Installment (Principal)</label>
                <input type="number" step="0.01" name="monthly_installment" id="modalInstallment" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Penalty</label>
                <input type="number" step="0.01" name="penalty" id="modalPenalty" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Authorization Remarks / Note</label>
                <input type="text" name="remarks" placeholder="Reason for adjustment" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold shadow-sm">Save Adjustment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(dueId, deposit, interest, installment, penalty, memberName) {
        document.getElementById('editDueForm').action = "/admin/registers/dues/" + dueId + "/update";
        document.getElementById('modalMemberName').textContent = memberName;
        document.getElementById('modalDeposit').value = deposit;
        document.getElementById('modalInterest').value = interest;
        document.getElementById('modalInstallment').value = installment;
        document.getElementById('modalPenalty').value = penalty;
        document.getElementById('editDueModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editDueModal').classList.add('hidden');
    }
</script>
@endpush
