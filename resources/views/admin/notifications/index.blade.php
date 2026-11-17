@extends('layouts.admin')

@section('title', 'Notifications & Reminders - Shri Samruddhi Society')
@section('header_title', 'WhatsApp & SMS Member Alerts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Society Notification & Reminders Dispatcher</h2>
            <p class="text-xs text-slate-500">Send automated payment reminders, payment receipts, and announcements via WhatsApp, SMS, or In-App alerts</p>
        </div>

        <button onclick="document.getElementById('sendModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-sm transition flex items-center gap-1.5">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>Send New Alert</span>
        </button>
    </div>

    <!-- Integration Gateway Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-emerald-200 shadow-sm bg-emerald-50/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-base text-emerald-600"></i> WhatsApp Business API
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">CONNECTED</span>
            </div>
            <p class="text-[11px] text-slate-600 mt-2">Delivers instant PDF receipt links and due reminder alerts directly to member WhatsApp numbers.</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-blue-200 shadow-sm bg-blue-50/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-comment-sms text-base text-blue-600"></i> SMS Gateway (DLT)
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">ACTIVE</span>
            </div>
            <p class="text-[11px] text-slate-600 mt-2">Sends OTP login tokens, urgent collection due notices, and payment confirmation SMS.</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-purple-200 shadow-sm bg-purple-50/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-purple-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-bell text-base text-purple-600"></i> In-App Mobile PWA Alerts
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">ENABLED</span>
            </div>
            <p class="text-[11px] text-slate-600 mt-2">Real-time notification cards displayed inside the member's mobile dashboard app.</p>
        </div>
    </div>

    <!-- Notifications History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-slate-900 text-sm">Dispatched Alerts History</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Date & Time</th>
                        <th class="py-3 px-3">Channel</th>
                        <th class="py-3 px-3">Target Recipient</th>
                        <th class="py-3 px-3">Alert Title</th>
                        <th class="py-3 px-3">Message Content</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notifications as $notif)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3 font-mono text-slate-500 whitespace-nowrap">{{ $notif->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-2.5 px-3">
                                @if($notif->channel === 'whatsapp')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1 w-max">
                                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                    </span>
                                @elseif($notif->channel === 'sms')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-comment-sms"></i> SMS
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-bell"></i> In-App
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">
                                {{ $notif->member ? $notif->member->full_name . ' (' . $notif->member->mobile_number . ')' : 'All Members (Broadcast)' }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">{{ $notif->title }}</td>
                            <td class="py-2.5 px-3 text-slate-600 max-w-xs truncate">{{ $notif->message }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-6 text-slate-400">No alert logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Send Notification -->
<div id="sendModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('sendModal').classList.add('hidden')" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-sm">Send Member Reminder / Notification</h3>
            <button onclick="document.getElementById('sendModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Audience *</label>
                <select name="target" id="targetSelect" onchange="toggleMemberField(this.value)" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="all_pending">All Members with Pending Dues</option>
                    <option value="all_active">All 28 Active Members (General Broadcast)</option>
                    <option value="single">Specific Member Only</option>
                </select>
            </div>

            <div id="singleMemberBox" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Member</label>
                <select name="member_id" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose Member --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->member_code }} - {{ $m->full_name }} ({{ $m->mobile_number }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dispatch Channel *</label>
                    <select name="channel" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="whatsapp" selected>WhatsApp Message</option>
                        <option value="sms">SMS Text</option>
                        <option value="in_app">In-App Notification</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alert Category *</label>
                    <select name="type" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="payment_reminder">Payment Due Reminder</option>
                        <option value="loan_status">Loan Update</option>
                        <option value="penalty_alert">Penalty Notice</option>
                        <option value="general">General Society Notice</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Message Title *</label>
                <input type="text" name="title" value="Monthly Collection Payment Reminder" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Message Body *</label>
                <textarea name="message" rows="3" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">Dear Member, please pay your monthly society contribution & loan installment before the 10th to avoid late fee penalties. You can also pay online via your member portal.</textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('sendModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-sm">Dispatch Notification</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleMemberField(val) {
        const box = document.getElementById('singleMemberBox');
        if (val === 'single') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }
</script>
@endpush
