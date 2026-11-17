@extends('layouts.app')

@section('title', 'Unauthorized - Deployment Control')

@section('layout_content')
<div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col justify-center items-center p-4">
    <div class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-2xl p-6 text-center space-y-4 shadow-2xl">
        <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-2xl mx-auto border border-rose-500/30">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h2 class="text-lg font-bold text-white">Deployment Token Required</h2>
        <p class="text-xs text-slate-400">
            To prevent unauthorized access, the deployment control routes require a valid secret token.
        </p>

        <div class="p-3 bg-slate-900 rounded-xl border border-slate-700 font-mono text-xs text-slate-300">
            Pass <span class="text-amber-400">?token=samrudhi2026</span> in the URL or configure <code class="text-emerald-400">DEPLOY_TOKEN</code> in your environment file.
        </div>

        <div class="pt-2">
            <a href="/deploy?token=samrudhi2026" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs rounded-xl inline-block transition">
                Access with Default Token
            </a>
        </div>
    </div>
</div>
@endsection
