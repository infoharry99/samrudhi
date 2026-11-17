@extends('layouts.app')

@section('title', 'Deployment Result - Shri Samruddhi Society')

@section('layout_content')
<div class="min-h-screen bg-slate-900 text-slate-100 p-4 sm:p-8 flex flex-col justify-center items-center">
    <div class="max-w-2xl w-full space-y-6">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr {{ $success ? 'from-emerald-600 to-teal-600 shadow-emerald-500/20' : 'from-rose-600 to-red-600 shadow-rose-500/20' }} items-center justify-center text-white text-2xl shadow-xl mb-3 border border-white/20">
                <i class="fa-solid {{ $success ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
            </div>
            <h1 class="text-2xl font-bold font-display text-white">
                {{ $success ? 'EXECUTION SUCCESSFUL' : 'EXECUTION FAILED' }}
            </h1>
            <p class="text-xs text-amber-400 font-mono tracking-wider uppercase mt-1">
                {{ $action }} • DURATION: {{ $duration }}
            </p>
        </div>

        <!-- Result Box -->
        <div class="bg-slate-800/95 border {{ $success ? 'border-emerald-600/60' : 'border-rose-600/60' }} rounded-2xl p-5 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Command Execution Logs</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $success ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                    {{ $success ? 'SUCCESS' : 'ERROR' }}
                </span>
            </div>

            <!-- Terminal output viewer -->
            <div class="space-y-3 max-h-80 overflow-y-auto font-mono text-xs bg-slate-950 p-4 rounded-xl border border-slate-700">
                @foreach($logs as $step => $log)
                    <div>
                        <span class="text-amber-400 font-bold">> [{{ strtoupper($step) }}]:</span>
                        <pre class="whitespace-pre-wrap text-[11px] text-slate-300 mt-1 pl-2 border-l-2 border-slate-700">{{ trim($log) }}</pre>
                    </div>
                @endforeach
            </div>

            <!-- Quick Action Buttons -->
            <div class="pt-3 border-t border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('deploy.index', ['token' => $token]) }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs text-center transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-server"></i>
                    <span>Deployment Control Panel</span>
                </a>

                <a href="{{ route('login') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold text-xs text-center shadow-lg shadow-brand-500/30 transition flex items-center justify-center gap-1.5">
                    <span>Go to Login Screen</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="text-center text-xs text-slate-500">
            Shri Samruddhi Society Management & Loan Accounting System
        </div>
    </div>
</div>
@endsection
