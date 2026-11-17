@extends('layouts.app')

@section('title', 'Sign In - Shri Samruddhi Society')

@section('layout_content')
<div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 bg-gradient-to-b from-slate-900 via-brand-950 to-slate-900 text-white relative">
    <!-- Ambient Glow -->
    <div class="absolute w-72 h-72 bg-brand-500/20 rounded-full blur-3xl -top-10 -left-10 pointer-events-none"></div>
    <div class="absolute w-72 h-72 bg-amber-500/10 rounded-full blur-3xl -bottom-10 -right-10 pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-500 to-indigo-600 items-center justify-center text-amber-300 text-3xl shadow-xl shadow-brand-500/30 mb-3 border border-white/20">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <h1 class="text-2xl font-bold font-display tracking-tight text-white">SHRI SAMRUDDHI SOCIETY</h1>
            <p class="text-xs text-amber-400 font-medium tracking-wider uppercase mt-1">Society Management & Loan Accounting</p>
            <p class="text-xs text-slate-400 mt-1">Digital Register & Online Member Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 sm:p-8 text-slate-800 shadow-2xl border border-white/20">
            <div class="mb-5 text-center">
                <h2 class="text-lg font-bold text-slate-900">Sign in to your account</h2>
                <p class="text-xs text-slate-500 mt-0.5">Admin & Members login using registered Mobile or Email</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-700 mb-1">
                        Mobile Number or Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                        <input type="text" name="login" id="login" required autofocus
                            value="{{ old('login') }}"
                            placeholder="e.g. 9876543210 or 9825000001"
                            class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-slate-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 text-sm transition outline-none">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 focus:border-brand-600 focus:ring-2 focus:ring-brand-500/20 text-sm transition outline-none">
                        <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fa-regular fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="rounded text-brand-600 focus:ring-brand-500">
                        <span>Remember me</span>
                    </label>
                    <a href="#" onclick="alert('Please contact the society administrator to reset your password.')" class="text-brand-600 hover:underline">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 active:scale-[0.98] text-white font-semibold text-sm shadow-lg shadow-brand-700/30 transition flex items-center justify-center gap-2">
                    <span>Secure Sign In</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <!-- 1-Click Demo Login Helper -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-3">1-Click Instant Demo Login</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('demo.login', ['role' => 'admin']) }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-purple-200 bg-purple-50 text-purple-900 hover:bg-purple-100 transition text-center">
                        <span class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-user-shield text-purple-600"></i> Admin
                        </span>
                        <span class="text-[10px] text-purple-700 mt-0.5">Full System Access</span>
                    </a>

                    <a href="{{ route('demo.login', ['role' => 'member', 'memberIndex' => 1]) }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 hover:bg-emerald-100 transition text-center">
                        <span class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-user text-emerald-600"></i> Member 1
                        </span>
                        <span class="text-[10px] text-emerald-700 mt-0.5">Rajeshbhai (₹70k Loan)</span>
                    </a>

                    <a href="{{ route('demo.login', ['role' => 'member', 'memberIndex' => 2]) }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-900 hover:bg-blue-100 transition text-center">
                        <span class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-user text-blue-600"></i> Member 2
                        </span>
                        <span class="text-[10px] text-blue-700 mt-0.5">Sureshbhai (₹35k Loan)</span>
                    </a>

                    <a href="{{ route('demo.login', ['role' => 'member', 'memberIndex' => 3]) }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 hover:bg-amber-100 transition text-center">
                        <span class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-piggy-bank text-amber-600"></i> Member 3
                        </span>
                        <span class="text-[10px] text-amber-700 mt-0.5">Amitbhai (Deposit Only)</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- PWA Install & Open App Section -->
        <div class="mt-6 space-y-3">
            <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 text-center shadow-lg">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 text-left">
                        <div class="w-9 h-9 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-base shrink-0 shadow-md">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Shri Samruddhi PWA App</p>
                            <p class="text-[10px] text-slate-300">Fast, app-like feel & 1-tap passbook</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" data-pwa-action="install" class="pwa-install-btn px-3 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-download"></i>
                            <span>Install App</span>
                        </button>
                        <button type="button" data-pwa-action="open" class="pwa-open-btn px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white font-semibold text-xs border border-white/20 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            <span>Open App</span>
                        </button>
                        <span class="pwa-standalone-badge hidden px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>App Mode Active</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-center text-[11px] text-slate-400">
                <span>Available on Android, iPhone & Desktop • Pure App Experience</span>
            </div>
        </div>
    </div>
</div>
@endsection
