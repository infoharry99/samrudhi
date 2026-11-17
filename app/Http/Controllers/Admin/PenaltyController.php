<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Penalty;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $penalties = Penalty::with(['member', 'waiver'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('id', 'desc')
            ->paginate(15);

        $stats = [
            'total_penalties' => Penalty::count(),
            'pending_amount' => Penalty::where('status', 'pending')->sum('amount'),
            'paid_amount' => Penalty::where('status', 'paid')->sum('amount'),
            'waived_amount' => Penalty::where('status', 'waived')->sum('amount'),
        ];

        $members = Member::where('status', 'active')->orderBy('full_name')->get();

        return view('admin.penalties.index', compact('penalties', 'stats', 'status', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'penalty_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $validated['status'] = 'pending';

        $penalty = Penalty::create($validated);

        AuditLog::log('penalty_added', "Added penalty of ₹{$penalty->amount} to member {$penalty->member->full_name}. Reason: {$penalty->reason}");

        return back()->with('success', "Penalty of ₹{$penalty->amount} levied successfully.");
    }

    public function waive(Request $request, Penalty $penalty)
    {
        $validated = $request->validate([
            'waiver_reason' => 'required|string|max:255',
        ]);

        $penalty->status = 'waived';
        $penalty->waived_by = auth()->id();
        $penalty->waiver_reason = $validated['waiver_reason'];
        $penalty->save();

        AuditLog::log('penalty_waived', "Waived penalty #{$penalty->id} for {$penalty->member->full_name}. Reason: {$penalty->waiver_reason}");

        return back()->with('success', "Penalty of ₹{$penalty->amount} waived successfully.");
    }
}
