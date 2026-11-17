@extends('layouts.app')

@section('layout_content')
<div class="min-h-full flex flex-col bg-slate-100 max-w-md mx-auto md:max-w-2xl lg:max-w-4xl shadow-xl border-x border-slate-200">
    <!-- App Header -->
    <header class="bg-gradient-to-r from-brand-900 via-brand-800 to-indigo-900 text-white px-4 py-3.5 sticky top-0 z-30 shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-amber-400 font-bold text-lg shadow-inner">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-wide leading-tight">SHRI SAMRUDDHI SOCIETY</h1>
                    <p class="text-[11px] text-amber-300 font-medium">Member Portal & Digital Passbook</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- PWA Action Buttons -->
                <button type="button" data-pwa-action="install" class="pwa-install-btn flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow transition">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span>App</span>
                </button>
                <button type="button" data-pwa-action="open" class="pwa-open-btn flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/15 hover:bg-white/25 text-white font-semibold text-xs border border-white/20 transition">
                    <i class="fa-solid fa-mobile-screen text-[11px]"></i>
                    <span>Open</span>
                </button>
                <span class="pwa-standalone-badge hidden items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 font-bold text-[10px] border border-emerald-400/30">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    <span>App Mode</span>
                </span>

                <a href="{{ route('member.profile') }}" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-xs text-white transition">
                    <i class="fa-solid fa-circle-user text-amber-400"></i>
                    <span class="font-medium max-w-[80px] truncate">{{ explode(' ', auth()->user()->name)[0] }}</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Logout" class="p-1.5 rounded-full hover:bg-white/20 text-white/80 hover:text-white transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Member Quick Info Subheader -->
        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-400/20 text-amber-300 font-mono font-semibold">
                <i class="fa-solid fa-id-card text-[10px]"></i>
                {{ auth()->user()->member->member_code }}
            </span>
            <div class="flex items-center gap-2 text-white/90">
                <span class="text-slate-300">Savings Deposit:</span>
                <span class="font-bold text-emerald-300">₹{{ number_format(auth()->user()->member->current_deposit_balance, 2) }}</span>
            </div>
        </div>
    </header>

    <!-- Main Member Content with Safe Bottom Area for Thumb Dock -->
    <main class="flex-1 p-3.5 sm:p-5 safe-bottom-padding">
        @yield('content')
    </main>

    <!-- App Bottom Dock Navigation (Pure Mobile App User Experience) -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 py-1.5 px-4 max-w-md mx-auto md:max-w-2xl lg:max-w-4xl shadow-[0_-4px_20px_rgba(0,0,0,0.06)] flex items-center justify-around">
        <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('member.dashboard') ? 'text-brand-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-house-chimney text-lg mb-0.5"></i>
            <span class="text-[10px]">Home</span>
        </a>

        <a href="{{ route('member.deposits') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('member.deposits') ? 'text-brand-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-piggy-bank text-lg mb-0.5"></i>
            <span class="text-[10px]">Deposits</span>
        </a>

        <!-- Floating Pay Online Button -->
        <a href="{{ route('member.pay_online') }}" class="flex flex-col items-center justify-center -mt-5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white w-12 h-12 rounded-full shadow-lg shadow-emerald-600/30 active:scale-95 transition">
            <i class="fa-solid fa-qrcode text-xl"></i>
        </a>

        <a href="{{ route('member.loans') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('member.loans') ? 'text-brand-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-hand-holding-dollar text-lg mb-0.5"></i>
            <span class="text-[10px]">My Loans</span>
        </a>

        <a href="{{ route('member.payments') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('member.payments') || request()->routeIs('member.receipt') ? 'text-brand-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-file-invoice text-lg mb-0.5"></i>
            <span class="text-[10px]">Receipts</span>
        </a>

        <a href="{{ route('member.profile') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('member.profile') ? 'text-brand-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-user-gear text-lg mb-0.5"></i>
            <span class="text-[10px]">Profile</span>
        </a>
    </nav>
</div>
@endsection
