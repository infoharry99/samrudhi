<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MonthlyDue;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('member')->orderBy('id', 'desc')->paginate(20);
        $members = Member::where('status', 'active')->orderBy('full_name')->get();
        return view('admin.notifications.index', compact('notifications', 'members'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:single,all_pending,all_active',
            'member_id' => 'required_if:target,single|nullable|exists:members,id',
            'channel' => 'required|in:whatsapp,sms,in_app',
            'type' => 'required|in:payment_reminder,loan_status,penalty_alert,general',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $count = 0;

        if ($validated['target'] === 'single') {
            Notification::create([
                'member_id' => $validated['member_id'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'channel' => $validated['channel'],
            ]);
            $count = 1;
        } elseif ($validated['target'] === 'all_pending') {
            $pendingMemberIds = MonthlyDue::where('payment_status', '!=', 'paid')
                ->pluck('member_id')
                ->unique();

            foreach ($pendingMemberIds as $mId) {
                Notification::create([
                    'member_id' => $mId,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'type' => $validated['type'],
                    'channel' => $validated['channel'],
                ]);
                $count++;
            }
        } else {
            // all active
            $activeMembers = Member::where('status', 'active')->get();
            foreach ($activeMembers as $m) {
                Notification::create([
                    'member_id' => $m->id,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'type' => $validated['type'],
                    'channel' => $validated['channel'],
                ]);
                $count++;
            }
        }

        AuditLog::log('notification_sent', "Dispatched {$validated['channel']} notification '{$validated['title']}' to {$count} member(s).");

        return back()->with('success', "Notification sent successfully via " . strtoupper($validated['channel']) . " to {$count} recipient(s).");
    }
}
