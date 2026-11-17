@extends('layouts.admin')

@section('title', 'Society Settings - Shri Samruddhi Society')
@section('header_title', 'System & Society Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 font-display">Society System Configuration</h2>
        <p class="text-xs text-slate-500">Configure society profile, interest computation methods, default monthly dues, penalties, and payment gateways.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Society Profile -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-building text-brand-600"></i>
                <span>Society Profile & Registration</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Society Name</label>
                    <input type="text" name="society_name" value="{{ $settings['society_name'] ?? 'SHRI SAMRUDDHI SOCIETY' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Registration Number</label>
                    <input type="text" name="registration_number" value="{{ $settings['registration_number'] ?? 'REG/GUJ/AHM/2018/14892' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Official Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 98765 43210' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'contact@samruddhisociety.in' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Registered Society Office Address</label>
                    <textarea name="society_address" rows="2" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">{{ $settings['society_address'] ?? 'Shop No. 4-6, Samruddhi Commercial Complex, Ahmedabad' }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Accounting Rules & Defaults -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-calculator text-amber-600"></i>
                <span>Accounting & Calculation Rules</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Default Monthly Deposit (₹)</label>
                    <input type="number" step="100" name="default_monthly_deposit" value="{{ $settings['default_monthly_deposit'] ?? '1000.00' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono font-bold">
                    <span class="text-[10px] text-slate-400">Fixed monthly savings share</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Default Monthly Interest Rate (%)</label>
                    <input type="number" step="0.1" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? '1.0' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono font-bold">
                    <span class="text-[10px] text-slate-400">1.0% per month = 12% p.a.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Interest Calculation Method</label>
                    <select name="interest_calculation_method" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="reducing_balance" {{ ($settings['interest_calculation_method'] ?? '') === 'reducing_balance' ? 'selected' : '' }}>Reducing Balance (Approved)</option>
                        <option value="flat" {{ ($settings['interest_calculation_method'] ?? '') === 'flat' ? 'selected' : '' }}>Flat Interest</option>
                        <option value="fixed_monthly" {{ ($settings['interest_calculation_method'] ?? '') === 'fixed_monthly' ? 'selected' : '' }}>Fixed Monthly</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Default Late Penalty (₹)</label>
                    <input type="number" name="default_late_penalty" value="{{ $settings['default_late_penalty'] ?? '100.00' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penalty Grace Period (Days)</label>
                    <input type="number" name="penalty_grace_days" value="{{ $settings['penalty_grace_days'] ?? '10' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                    <span class="text-[10px] text-slate-400">Due by 10th of each month</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Financial Year</label>
                    <input type="text" name="financial_year" value="{{ $settings['financial_year'] ?? '2026-2027' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- 3. Online Payment Gateway Settings -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-emerald-600"></i>
                <span>UPI & Online Payment Gateway Settings</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Society UPI Virtual Payment Address (VPA)</label>
                    <input type="text" name="upi_id" value="{{ $settings['upi_id'] ?? 'samruddhisociety@upi' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500 font-mono font-bold">
                    <span class="text-[10px] text-slate-400">Members scan this UPI QR in the mobile app</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">UPI Payee Display Name</label>
                    <input type="text" name="upi_payee_name" value="{{ $settings['upi_payee_name'] ?? 'Shri Samruddhi Co-op Society' }}" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-md transition">
                Save Society Settings
            </button>
        </div>
    </form>
</div>
@endsection
