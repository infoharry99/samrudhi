@extends('layouts.admin')

@section('title', 'Monthly Registers - Shri Samruddhi Society')
@section('header_title', 'Monthly Society Registers')

@section('content')
<div class="space-y-6">
    <!-- Header with Generate Register Button -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Society Collection Registers</h2>
            <p class="text-xs text-slate-500">Each monthly register records all active members, their monthly deposits, loan interests, installments, and penalties.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Year Selector -->
            <form method="GET" class="inline-flex">
                <select name="year" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-brand-500 outline-none">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endfor
                </select>
            </form>

            <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Generate Register</span>
            </button>
        </div>
    </div>

    <!-- Current Active Month Card Callout -->
    @if($currentMonthRegister)
        <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 rounded-2xl p-5 text-white shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider">Current Active Register</span>
                <h3 class="text-xl font-bold mt-1 font-display">{{ $currentMonthRegister->title }}</h3>
                <p class="text-xs text-amber-100 mt-0.5">
                    Expected: ₹{{ number_format($currentMonthRegister->total_payable_expected, 0) }} • Collected: ₹{{ number_format($currentMonthRegister->total_collected, 0) }}
                </p>
            </div>
            <a href="{{ route('admin.registers.show', $currentMonthRegister->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-950 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                <span>Open Digital Register</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    @endif

    <!-- Registers Grid / List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($registers as $register)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $register->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                            {{ strtoupper($register->status) }}
                        </span>
                        <span class="text-xs font-mono text-slate-400">
                            {{ date('F', mktime(0, 0, 0, $register->month, 1)) }} {{ $register->year }}
                        </span>
                    </div>

                    <h4 class="font-bold text-slate-900 text-base font-display">{{ $register->title }}</h4>
                    <p class="text-xs text-slate-500 mt-1">{{ $register->dues_count }} Member entries recorded</p>

                    <!-- Financial snapshot -->
                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Total Expected</span>
                            <span class="font-bold text-slate-800">₹{{ number_format($register->total_payable_expected, 0) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Total Collected</span>
                            <span class="font-bold text-emerald-600">₹{{ number_format($register->total_collected, 0) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.registers.print', $register->id) }}" target="_blank" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg text-xs" title="Print Register">
                        <i class="fa-solid fa-print"></i>
                    </a>
                    <a href="{{ route('admin.registers.export', $register->id) }}" class="p-2 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-lg text-xs" title="Export Excel">
                        <i class="fa-solid fa-file-excel"></i>
                    </a>
                    <a href="{{ route('admin.registers.show', $register->id) }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-sm">
                        View Register
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-dashed border-slate-300">
                <i class="fa-solid fa-book-open text-4xl text-slate-300 mb-3"></i>
                <h4 class="font-bold text-slate-700">No registers generated for year {{ $selectedYear }}</h4>
                <p class="text-xs text-slate-500 mt-1">Generate a new monthly register to start recording collections.</p>
                <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="mt-4 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl">
                    Generate Monthly Register
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal: Generate Monthly Register -->
<div id="generateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="document.getElementById('generateModal').classList.add('hidden')" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-sm">Generate Monthly Register</h3>
            <button onclick="document.getElementById('generateModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.registers.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Month</label>
                    <select name="month" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Year</label>
                    <input type="number" name="year" value="{{ date('Y') }}" required min="2020" max="2035" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Remarks / Register Title Notes (Optional)</label>
                <textarea name="notes" rows="2" placeholder="e.g. November 2026 Monthly Collection Register" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-xs">
                <i class="fa-solid fa-circle-info mr-1 text-amber-600"></i>
                This will automatically fetch all active members, calculate monthly savings deposits, compute loan interest, and apply installments.
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold shadow-sm">Generate Register Now</button>
            </div>
        </form>
    </div>
</div>
@endsection
