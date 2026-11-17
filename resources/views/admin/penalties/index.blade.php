@extends('layouts.admin')

@section('title', 'Penalty Management - Shri Samruddhi Society')
@section('header_title', 'Penalty Management')

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Society Penalties & Late Fees</h2>
            <p class="text-xs text-slate-500">Levy penalties according to society rules, track collections, or waive with authorization</p>
        </div>

        <button onclick="document.getElementById('levyPenaltyModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Levy Penalty</span>
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Total Penalties</span>
            <span class="text-lg font-bold text-slate-900 font-display">{{ $stats['total_penalties'] }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Pending Fine Amount</span>
            <span class="text-lg font-bold text-rose-600 font-display">₹{{ number_format($stats['pending_amount'], 0) }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Paid Amount</span>
            <span class="text-lg font-bold text-emerald-600 font-display">₹{{ number_format($stats['paid_amount'], 0) }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-medium text-slate-500 block">Waived Amount</span>
            <span class="text-lg font-bold text-slate-500 font-display">₹{{ number_format($stats['waived_amount'], 0) }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Date</th>
                        <th class="py-3 px-3">Member Details</th>
                        <th class="py-3 px-3">Reason / Description</th>
                        <th class="py-3 px-3 text-right">Amount (₹)</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penalties as $penalty)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-3 font-mono text-slate-500">{{ $penalty->penalty_date->format('d M Y') }}</td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900 block">{{ $penalty->member->full_name }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $penalty->member->member_code }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-700">{{ $penalty->reason }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-rose-600">₹{{ number_format($penalty->amount, 2) }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($penalty->status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">PAID</span>
                                @elseif($penalty->status === 'waived')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">WAIVED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">PENDING</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if($penalty->status === 'pending')
                                    <button onclick="openWaiveModal({{ $penalty->id }}, '{{ addslashes($penalty->member->full_name) }}', {{ $penalty->amount }})" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                        Waive Penalty
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-slate-400">No penalties recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Levy Penalty -->
<div id="levyPenaltyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('levyPenaltyModal').classList.add('hidden')" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-sm">Levy Member Penalty</h3>
            <button onclick="document.getElementById('levyPenaltyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.penalties.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Member *</label>
                <select name="member_id" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose Member --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->member_code }} - {{ $m->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Penalty Date *</label>
                <input type="date" name="penalty_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Penalty Amount (₹) *</label>
                <input type="number" step="1" name="amount" value="100" required class="w-full px-3 py-2 border rounded-xl text-xs font-mono font-bold text-rose-600 outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Reason / Description *</label>
                <input type="text" name="reason" placeholder="e.g. Late monthly collection payment after 10th" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('levyPenaltyModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm">Levy Penalty</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Waive Penalty -->
<div id="waiveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('waiveModal').classList.add('hidden')" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-sm">Waive Penalty</h3>
            <button onclick="document.getElementById('waiveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="waiveForm" method="POST" class="space-y-3.5">
            @csrf
            <p id="waiveMemberText" class="text-xs text-slate-600 font-semibold"></p>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Admin Authorization Reason *</label>
                <textarea name="waiver_reason" rows="3" required placeholder="State authorized reason for waiving this penalty..." class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('waiveModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold shadow-sm">Authorize Waiver</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openWaiveModal(penaltyId, memberName, amount) {
        document.getElementById('waiveForm').action = "/admin/penalties/" + penaltyId + "/waive";
        document.getElementById('waiveMemberText').textContent = "Waiving ₹" + amount + " penalty for " + memberName;
        document.getElementById('waiveModal').classList.remove('hidden');
    }
</script>
@endpush
