@extends('layouts.admin')

@section('title', 'Add New Member - Shri Samruddhi Society')
@section('header_title', 'Register New Member')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">New Member Enrollment</h2>
            <p class="text-xs text-slate-500">Add a new member to the society register with initial balances</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Members
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Member Code / ID *</label>
                    <input type="text" name="member_code" value="{{ old('member_code', $suggestedCode) }}" required class="w-full px-3 py-2 border rounded-xl text-xs font-mono font-bold bg-slate-50 outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Ramesh K. Patel" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mobile Number (Login ID) *</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" required maxlength="10" placeholder="10-digit mobile number" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alternate Mobile Number</label>
                    <input type="text" name="alternate_mobile" value="{{ old('alternate_mobile') }}" placeholder="Optional alternate number" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Joining Date *</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Monthly Deposit Default (₹) *</label>
                    <input type="number" step="0.01" name="monthly_deposit_amount" value="{{ old('monthly_deposit_amount', '1000.00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Deposit Balance (₹) *</label>
                    <input type="number" step="0.01" name="initial_deposit_balance" value="{{ old('initial_deposit_balance', '0.00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                    <span class="text-[10px] text-slate-400">Opening savings balance if transferring from register</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Loan Balance (₹) *</label>
                    <input type="number" step="0.01" name="initial_loan_balance" value="{{ old('initial_loan_balance', '0.00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                    <span class="text-[10px] text-slate-400">Opening loan principal remaining (if applicable)</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Residential Address</label>
                    <textarea name="address" rows="2" placeholder="Full house address with pincode" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Member Photograph (Optional)</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Membership Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active Member</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive / Closed</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-[11px] text-slate-500">Default Member Password: <code class="font-bold text-brand-700 bg-slate-100 px-1.5 py-0.5 rounded">member123</code></p>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs shadow-md transition">
                    Enroll Member & Create Login
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
