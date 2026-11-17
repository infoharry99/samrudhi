@extends('layouts.app')

@section('title', 'Server Deployment & Migration Control - Shri Samruddhi Society')

@section('layout_content')
<div class="min-h-screen bg-slate-900 text-slate-100 p-4 sm:p-8 flex flex-col justify-center items-center">
    <div class="max-w-2xl w-full space-y-6">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 items-center justify-center text-amber-300 text-2xl shadow-xl shadow-brand-500/20 mb-3 border border-white/20">
                <i class="fa-solid fa-server"></i>
            </div>
            <h1 class="text-2xl font-bold font-display text-white">DEPLOYMENT & DATABASE MIGRATION</h1>
            <p class="text-xs text-amber-400 font-mono tracking-wider uppercase mt-1">SHRI SAMRUDDHI SOCIETY MANAGEMENT SYSTEM</p>
            <p class="text-xs text-slate-400 mt-1">One-click server initialization, migration, and seeder management</p>
        </div>

        <!-- Database Status Card -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700 p-5 shadow-xl backdrop-blur-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $dbConnected ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                    <span class="font-bold text-sm text-white">Database Connection Status</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $dbConnected ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                    {{ $dbConnected ? 'CONNECTED' : 'DISCONNECTED' }}
                </span>
            </div>

            @if($dbConnected)
                <div class="grid grid-cols-3 gap-3 mt-4 text-center">
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                        <span class="text-[10px] text-slate-400 block uppercase font-mono">Members</span>
                        <span class="text-xl font-bold font-mono text-emerald-400">{{ $membersCount }}</span>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                        <span class="text-[10px] text-slate-400 block uppercase font-mono">User Logins</span>
                        <span class="text-xl font-bold font-mono text-blue-400">{{ $usersCount }}</span>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                        <span class="text-[10px] text-slate-400 block uppercase font-mono">Registers</span>
                        <span class="text-xl font-bold font-mono text-amber-400">{{ $registersCount }}</span>
                    </div>
                </div>
            @else
                <div class="mt-3 p-3 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-mono">
                    <strong>Error:</strong> {{ $dbError }}
                </div>
            @endif
        </div>

        <!-- Result Logs (if run) -->
        @if(session('deploy_success'))
            @php $res = session('deploy_success'); @endphp
            <div class="bg-emerald-950/80 border border-emerald-600 rounded-2xl p-5 shadow-xl text-emerald-200">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-emerald-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                        <span class="font-bold text-sm text-white">Action Completed: {{ $res['action'] }}</span>
                    </div>
                    <span class="text-xs font-mono bg-emerald-900/80 px-2 py-0.5 rounded text-emerald-300">{{ $res['duration'] }}</span>
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto font-mono text-xs bg-slate-950 p-3 rounded-xl border border-emerald-900">
                    @foreach($res['logs'] as $step => $log)
                        <div>
                            <span class="text-amber-400 font-bold">> [{{ strtoupper($step) }}]:</span>
                            <pre class="whitespace-pre-wrap text-[11px] text-slate-300 mt-1">{{ trim($log) }}</pre>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(session('deploy_error'))
            <div class="bg-rose-950/80 border border-rose-600 rounded-2xl p-5 shadow-xl text-rose-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
                    <span class="font-bold text-sm text-white">Execution Failed</span>
                </div>
                <pre class="bg-slate-950 p-3 rounded-xl text-xs font-mono text-rose-300 whitespace-pre-wrap">{{ session('deploy_error') }}</pre>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700 p-5 shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Execute Deployment Commands</h3>

            <!-- 1. Migrate & Seed (Main Recommended Action) -->
            <form action="{{ route('deploy.migrate_seed') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" onclick="return confirm('Run full migration and seed the 28 register members?')" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 active:scale-[0.99] text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-play"></i>
                    <span>Run Complete Migrate & Seed (Recommended)</span>
                </button>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2">
                <!-- Migrate Only -->
                <form action="{{ route('deploy.migrate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs border border-slate-600 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-database text-purple-400"></i>
                        <span>Migrate Only</span>
                    </button>
                </form>

                <!-- Seed Only -->
                <form action="{{ route('deploy.seed') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs border border-slate-600 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-seedling text-amber-400"></i>
                        <span>Seed Only</span>
                    </button>
                </form>

                <!-- Cache & Optimize -->
                <form action="{{ route('deploy.optimize') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs border border-slate-600 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-bolt text-yellow-400"></i>
                        <span>Clear Cache</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Direct API / Webhook Trigger Endpoints Note -->
        <div class="bg-slate-900/60 rounded-xl p-4 border border-slate-800 text-xs text-slate-400 font-mono">
            <span class="text-amber-400 font-bold block mb-1">Direct CLI / Webhook Endpoints:</span>
            <p>GET/POST: <code class="text-slate-200">/deploy/migrate-seed?token={{ $token }}</code></p>
            <p>GET/POST: <code class="text-slate-200">/deploy/migrate?token={{ $token }}</code></p>
            <p>GET/POST: <code class="text-slate-200">/deploy/seed?token={{ $token }}</code></p>
        </div>

        <!-- Links to App -->
        <div class="flex items-center justify-between text-xs pt-2">
            <a href="{{ route('login') }}" class="text-brand-400 hover:underline flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Go to Login Screen</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="text-emerald-400 hover:underline flex items-center gap-1">
                <span>Go to Admin Dashboard</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection
