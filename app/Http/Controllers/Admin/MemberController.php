<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MemberDeposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $members = Member::with(['loans' => fn($q) => $q->where('status', 'active')])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('member_code', 'like', "%{$search}%")
                      ->orWhere('mobile_number', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Member::count(),
            'active' => Member::where('status', 'active')->count(),
            'inactive' => Member::where('status', 'inactive')->count(),
            'total_deposits' => Member::sum('current_deposit_balance'),
            'total_loans' => Member::sum('current_loan_balance'),
        ];

        return view('admin.members.index', compact('members', 'stats', 'search', 'status'));
    }

    public function create()
    {
        $lastMember = Member::orderBy('id', 'desc')->first();
        $nextNum = $lastMember ? ((int) str_replace('SSS-', '', $lastMember->member_code) + 1) : 1;
        $suggestedCode = 'SSS-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.members.create', compact('suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_code' => 'required|string|unique:members,member_code',
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|digits:10|unique:members,mobile_number',
            'alternate_mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'joining_date' => 'required|date',
            'monthly_deposit_amount' => 'required|numeric|min:0',
            'initial_deposit_balance' => 'required|numeric|min:0',
            'initial_loan_balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Photo upload if provided
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('member_photos', 'public');
            }

            // Create Member User Account
            $user = User::create([
                'name' => $validated['full_name'],
                'mobile' => $validated['mobile_number'],
                'email' => strtolower(str_replace(' ', '', $validated['member_code'])) . '@samruddhi.org',
                'password' => Hash::make('member123'),
                'role' => 'member',
                'status' => $validated['status'],
            ]);

            // Create Member
            $member = Member::create([
                'member_code' => $validated['member_code'],
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'mobile_number' => $validated['mobile_number'],
                'alternate_mobile' => $validated['alternate_mobile'],
                'address' => $validated['address'],
                'joining_date' => $validated['joining_date'],
                'monthly_deposit_amount' => $validated['monthly_deposit_amount'],
                'initial_deposit_balance' => $validated['initial_deposit_balance'],
                'initial_loan_balance' => $validated['initial_loan_balance'],
                'current_deposit_balance' => $validated['initial_deposit_balance'],
                'current_loan_balance' => $validated['initial_loan_balance'],
                'photo_path' => $photoPath,
                'status' => $validated['status'],
            ]);

            $user->update(['member_id' => $member->id]);

            // If initial deposit > 0, create opening deposit transaction
            if ($validated['initial_deposit_balance'] > 0) {
                MemberDeposit::create([
                    'member_id' => $member->id,
                    'transaction_type' => 'opening_balance',
                    'amount' => $validated['initial_deposit_balance'],
                    'balance_after' => $validated['initial_deposit_balance'],
                    'transaction_date' => $validated['joining_date'],
                    'description' => 'Initial deposit balance upon registration',
                ]);
            }

            AuditLog::log('member_created', "Created member {$member->member_code} - {$member->full_name}");

            DB::commit();
            return redirect()->route('admin.members.show', $member->id)->with('success', "Member {$member->full_name} added successfully! Default login password: member123");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating member: ' . $e->getMessage());
        }
    }

    public function show(Member $member)
    {
        $member->load([
            'loans',
            'deposits',
            'monthlyDues.monthlyRegister',
            'payments',
            'penalties',
            'loanTransactions.loan',
        ]);

        $activeLoan = $member->loans->where('status', 'active')->first();

        return view('admin.members.show', compact('member', 'activeLoan'));
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => "required|string|digits:10|unique:members,mobile_number,{$member->id}",
            'alternate_mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'joining_date' => 'required|date',
            'monthly_deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('photo')) {
                $validated['photo_path'] = $request->file('photo')->store('member_photos', 'public');
            }

            $member->update($validated);

            // Sync User login details
            if ($member->user) {
                $member->user->update([
                    'name' => $validated['full_name'],
                    'mobile' => $validated['mobile_number'],
                    'status' => $validated['status'],
                ]);
            }

            AuditLog::log('member_updated', "Updated member {$member->member_code} details");

            DB::commit();
            return redirect()->route('admin.members.show', $member->id)->with('success', 'Member details updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating member: ' . $e->getMessage());
        }
    }

    public function destroy(Member $member)
    {
        // Safeguard check: Cannot delete if financial transactions exist
        $hasTransactions = $member->payments()->exists() ||
            $member->loans()->where('status', 'active')->exists() ||
            $member->deposits()->count() > 1;

        if ($hasTransactions) {
            return back()->with('error', "Financial Safeguard: Cannot delete member {$member->member_code} because active loans or financial transaction records exist. You may mark this member as Inactive instead.");
        }

        DB::beginTransaction();
        try {
            $name = $member->full_name;
            $code = $member->member_code;

            if ($member->user) {
                $member->user->delete();
            }
            $member->delete();

            AuditLog::log('member_deleted', "Deleted member {$code} - {$name}");

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', "Member {$name} deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Could not delete member: ' . $e->getMessage());
        }
    }

    public function statement(Member $member)
    {
        $member->load([
            'deposits',
            'loans.transactions',
            'monthlyDues.monthlyRegister',
            'payments',
        ]);

        return view('admin.members.statement', compact('member'));
    }
}
