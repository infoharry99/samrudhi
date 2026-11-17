@extends('layouts.app')

@section('layout_content')
<div class="min-h-full flex flex-col md:flex-row">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:w-64 md:flex-col bg-slate-900 text-slate-300 min-h-screen border-r border-slate-800 shrink-0">
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800 flex items-center gap-3 bg-slate-950">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/20 font-bold text-xl">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <h1 class="text-white font-bold text-sm tracking-wide font-display">SHRI SAMRUDDHI</h1>
                <p class="text-[10px] text-amber-400 font-medium tracking-wider uppercase">Society & Loan System</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
            <div class="px-3 pt-2 pb-1 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Core Operations</div>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-gauge w-5 text-center text-base {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.registers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.registers.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-book-bookmark w-5 text-center text-base {{ request()->routeIs('admin.registers.*') ? 'text-white' : 'text-amber-400' }}"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span>Monthly Register</span>
                    <span class="px-1.5 py-0.5 text-[10px] bg-amber-500/20 text-amber-300 rounded font-semibold">Paper Replica</span>
                </div>
            </a>

            <a href="{{ route('admin.collections.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.collections.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-receipt w-5 text-center text-base {{ request()->routeIs('admin.collections.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                <span>Collections & Receipts</span>
            </a>

            <a href="{{ route('admin.members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.members.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-users w-5 text-center text-base {{ request()->routeIs('admin.members.*') ? 'text-white' : 'text-blue-400' }}"></i>
                <span>Members (28 Active)</span>
            </a>

            <a href="{{ route('admin.loans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.loans.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-hand-holding-dollar w-5 text-center text-base {{ request()->routeIs('admin.loans.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Loan Management</span>
            </a>

            <a href="{{ route('admin.penalties.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.penalties.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-triangle-exclamation w-5 text-center text-base {{ request()->routeIs('admin.penalties.*') ? 'text-white' : 'text-rose-400' }}"></i>
                <span>Penalty Management</span>
            </a>

            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Accounting & Reports</div>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center text-base {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-cyan-400' }}"></i>
                <span>Financial Reports</span>
            </a>

            <a href="{{ route('admin.migration.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.migration.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-file-import w-5 text-center text-base {{ request()->routeIs('admin.migration.*') ? 'text-white' : 'text-teal-400' }}"></i>
                <span>Data Migration</span>
            </a>

            <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.notifications.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-bell w-5 text-center text-base {{ request()->routeIs('admin.notifications.*') ? 'text-white' : 'text-yellow-400' }}"></i>
                <span>WhatsApp / SMS Alerts</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-brand-600 text-white shadow-md' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-sliders w-5 text-center text-base {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-slate-400' }}"></i>
                <span>Society Settings</span>
            </a>

            <!-- PWA Sidebar Install Tile -->
            <div class="pt-2 px-1">
                <button type="button" data-pwa-action="install" class="pwa-install-btn w-full p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 text-left transition flex items-center gap-2.5 group">
                    <div class="w-7 h-7 rounded-lg bg-amber-400/20 text-amber-400 flex items-center justify-center font-bold text-xs shrink-0 group-hover:scale-105 transition">
                        <i class="fa-solid fa-download"></i>
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-slate-200">Install PWA App</p>
                        <p class="text-[10px] text-slate-400">Desktop / Mobile shortcut</p>
                    </div>
                </button>
            </div>
        </nav>

        <!-- Admin Profile Footer -->
        <div class="p-3 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-brand-700 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    AD
                </div>
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->mobile }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top App Bar (Mobile & Desktop) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 py-3 flex items-center justify-between shadow-sm">
            <!-- Mobile Menu Toggle & Title -->
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('mobile-drawer').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <i class="fa-solid fa-bars-staggered text-lg"></i>
                </button>
                <div>
                    <h2 class="text-base md:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                        @yield('header_title', 'Admin Dashboard')
                    </h2>
                    <p class="text-xs text-slate-500 hidden sm:block">Shri Samruddhi Co-operative Society Management</p>
                </div>
            </div>

            <!-- Quick Action Badges & Profile -->
            <div class="flex items-center gap-2 md:gap-3">
                <!-- PWA Action Buttons in Admin Header -->
                <button type="button" data-pwa-action="install" class="pwa-install-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-500/10 text-amber-800 border border-amber-300 hover:bg-amber-100 text-xs font-bold transition" title="Install Samruddhi PWA App">
                    <i class="fa-solid fa-download text-amber-600"></i>
                    <span class="hidden sm:inline">Install App</span>
                </button>
                <button type="button" data-pwa-action="open" class="pwa-open-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium transition" title="Open App in Fullscreen">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span class="hidden sm:inline">Open App</span>
                </button>
                <span class="pwa-standalone-badge hidden items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-300">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span class="hidden sm:inline">App Mode</span>
                </span>

                <a href="{{ route('admin.collections.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-medium shadow-sm transition">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Record Cash</span>
                </a>

                <a href="{{ route('demo.login', ['role' => 'member', 'memberIndex' => 1]) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 text-xs font-medium transition" title="Preview Member App View">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                    <span class="hidden md:inline">Preview Member App</span>
                </a>

                <!-- Notification Bell -->
                <a href="{{ route('admin.notifications.index') }}" class="p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 relative">
                    <i class="fa-regular fa-bell"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full"></span>
                </a>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-4 md:p-6 bg-slate-50 safe-bottom-padding md:safe-bottom-padding-none">
            @yield('content')
        </main>
    </div>
</div>

<!-- Mobile Drawer Modal -->
<div id="mobile-drawer" class="hidden fixed inset-0 z-50 flex">
    <div onclick="document.getElementById('mobile-drawer').classList.add('hidden')" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
    <div class="relative w-72 max-w-[80%] bg-slate-900 text-slate-200 h-full flex flex-col p-4 shadow-2xl z-10 overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold">
                    <i class="fa-solid fa-building-columns text-sm"></i>
                </div>
                <span class="font-bold text-sm text-white">SHRI SAMRUDDHI</span>
            </div>
            <button onclick="document.getElementById('mobile-drawer').classList.add('hidden')" class="text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <nav class="mt-4 space-y-1.5 flex-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-gauge text-brand-400 w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.registers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-book-bookmark text-amber-400 w-5"></i>
                <span>Monthly Register</span>
            </a>
            <a href="{{ route('admin.collections.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-receipt text-emerald-400 w-5"></i>
                <span>Collections</span>
            </a>
            <a href="{{ route('admin.members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-users text-blue-400 w-5"></i>
                <span>Members (28)</span>
            </a>
            <a href="{{ route('admin.loans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-hand-holding-dollar text-purple-400 w-5"></i>
                <span>Loans</span>
            </a>
            <a href="{{ route('admin.penalties.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 w-5"></i>
                <span>Penalties</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-chart-pie text-cyan-400 w-5"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('admin.migration.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-file-import text-teal-400 w-5"></i>
                <span>Data Migration</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800">
                <i class="fa-solid fa-sliders text-slate-400 w-5"></i>
                <span>Settings</span>
            </a>

            <!-- Mobile Drawer PWA Action -->
            <div class="pt-2">
                <button type="button" data-pwa-action="install" class="pwa-install-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold bg-amber-400 text-slate-950 hover:bg-amber-300 transition">
                    <i class="fa-solid fa-download w-5 text-center"></i>
                    <span>Install PWA App</span>
                </button>
                <button type="button" data-pwa-action="open" class="pwa-open-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-slate-800 text-slate-200 hover:bg-slate-700 transition mt-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>
                    <span>Open App</span>
                </button>
            </div>
        </nav>

        <form action="{{ route('logout') }}" method="POST" class="pt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-rose-600/20 text-rose-400 hover:bg-rose-600 hover:text-white text-sm font-semibold transition">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Bottom Navigation Dock for Admin (App Feel) -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-2 flex items-center justify-around shadow-2xl">
    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('admin.dashboard') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
        <i class="fa-solid fa-gauge text-lg"></i>
        <span class="text-[10px]">Dashboard</span>
    </a>
    <a href="{{ route('admin.registers.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('admin.registers.*') ? 'text-amber-600 font-bold' : 'text-slate-500' }}">
        <i class="fa-solid fa-book-bookmark text-lg"></i>
        <span class="text-[10px]">Register</span>
    </a>
    <a href="{{ route('admin.collections.create') }}" class="flex flex-col items-center justify-center -mt-5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white w-12 h-12 rounded-full shadow-lg shadow-emerald-500/30">
        <i class="fa-solid fa-plus text-lg"></i>
    </a>
    <a href="{{ route('admin.collections.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('admin.collections.*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
        <i class="fa-solid fa-receipt text-lg"></i>
        <span class="text-[10px]">Collect</span>
    </a>
    <a href="{{ route('admin.members.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('admin.members.*') ? 'text-brand-600 font-bold' : 'text-slate-500' }}">
        <i class="fa-solid fa-users text-lg"></i>
        <span class="text-[10px]">Members</span>
    </a>
</div>
@endsection
