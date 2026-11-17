@extends('layouts.admin')

@section('title', 'Edit Member: ' . $member->full_name)
@section('header_title', 'Edit Member Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Edit Member: {{ $member->full_name }}</h2>
            <p class="text-xs text-slate-500">Update member contact information, address, and status</p>
        </div>
        <a href="{{ route('admin.members.show', $member->id) }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Profile
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <form action="{{ route('admin.members.update', $member->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Member Code (Read-Only)</label>
                    <input type="text" value="{{ $member->member_code }}" disabled class="w-full px-3 py-2 border rounded-xl text-xs font-mono font-bold bg-slate-100 text-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mobile Number (Login) *</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $member->mobile_number) }}" required maxlength="10" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alternate Mobile Number</label>
                    <input type="text" name="alternate_mobile" value="{{ old('alternate_mobile', $member->alternate_mobile) }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Joining Date *</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', $member->joining_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Monthly Deposit Amount (₹) *</label>
                    <input type="number" step="0.01" name="monthly_deposit_amount" value="{{ old('monthly_deposit_amount', $member->monthly_deposit_amount) }}" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">{{ old('address', $member->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Membership Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="active" {{ $member->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $member->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Update Photograph</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.members.show', $member->id) }}" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs shadow-md transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
