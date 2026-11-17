@extends('layouts.member')

@section('title', 'My Profile - Shri Samruddhi Society')

@section('content')
<div class="space-y-4 max-w-lg mx-auto">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 font-display">My Profile & Account</h2>
        <span class="text-xs font-mono font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-full border border-brand-200">
            {{ $member->member_code }}
        </span>
    </div>

    <!-- Profile Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-700 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-md">
                {{ strtoupper(substr($member->full_name, 0, 1)) }}
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $member->full_name }}</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $member->mobile_number }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                    Active Member
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 py-3 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px]">Joining Date</span>
                <span class="font-medium text-slate-800">{{ $member->joining_date->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Monthly Deposit Rule</span>
                <span class="font-mono font-bold text-slate-800">₹{{ number_format($member->monthly_deposit_amount, 0) }} / Month</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Total Savings Balance</span>
                <span class="font-mono font-bold text-amber-700">₹{{ number_format($member->current_deposit_balance, 2) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Outstanding Loan</span>
                <span class="font-mono font-bold text-rose-600">₹{{ number_format($member->current_loan_balance, 2) }}</span>
            </div>
        </div>

        <!-- Editable Details Form -->
        <form action="{{ route('member.update_profile') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alternate Contact Number</label>
                <input type="text" name="alternate_mobile" value="{{ old('alternate_mobile', $member->alternate_mobile) }}" placeholder="Optional second number" class="w-full px-3 py-2 border rounded-xl text-xs font-mono outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Residential Address</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">{{ old('address', $member->address) }}</textarea>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <h4 class="font-bold text-xs text-slate-900 mb-2">Change Account Password (Optional)</h4>
                <div class="space-y-2">
                    <input type="password" name="current_password" placeholder="Current Password" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                    <input type="password" name="new_password" placeholder="New Password (min 6 characters)" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                    <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs shadow transition">
                Update Profile & Password
            </button>
        </form>
    </div>

    <!-- Logout Action Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sign Out from Member Portal</span>
            </button>
        </form>
    </div>
</div>
@endsection
