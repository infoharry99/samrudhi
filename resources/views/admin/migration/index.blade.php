@extends('layouts.admin')

@section('title', 'Existing Data Migration - Shri Samruddhi Society')
@section('header_title', 'Existing Register Data Migration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Digitize Physical Register Data</h2>
            <p class="text-xs text-slate-500">Migrate existing manual paper register records, member mobile numbers, savings balances, and loan balances</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.migration.template') }}" class="px-3.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold hover:bg-emerald-100 transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>Download CSV Template</span>
            </a>
        </div>
    </div>

    <!-- 7-Step Migration Workflow (Section 11) -->
    <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-xl">
        <h3 class="font-bold text-sm text-amber-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-list-check"></i>
            <span>Approved 7-Step Physical Register Migration Procedure</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                <span class="w-5 h-5 rounded-full bg-amber-400 text-slate-950 font-bold flex items-center justify-center text-[10px] mb-2">1</span>
                <p class="font-bold text-white">Physical Register</p>
                <p class="text-[11px] text-slate-300 mt-0.5">Collect latest register book & loan records</p>
            </div>
            <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                <span class="w-5 h-5 rounded-full bg-amber-400 text-slate-950 font-bold flex items-center justify-center text-[10px] mb-2">2</span>
                <p class="font-bold text-white">Excel Template</p>
                <p class="text-[11px] text-slate-300 mt-0.5">Prepare data in prescribed CSV format</p>
            </div>
            <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                <span class="w-5 h-5 rounded-full bg-amber-400 text-slate-950 font-bold flex items-center justify-center text-[10px] mb-2">3</span>
                <p class="font-bold text-white">Validation Check</p>
                <p class="text-[11px] text-slate-300 mt-0.5">Check for duplicate mobiles & numbers</p>
            </div>
            <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                <span class="w-5 h-5 rounded-full bg-amber-400 text-slate-950 font-bold flex items-center justify-center text-[10px] mb-2">4</span>
                <p class="font-bold text-white">Admin Approval</p>
                <p class="text-[11px] text-slate-300 mt-0.5">Verify balances against book & commit</p>
            </div>
        </div>
    </div>

    <!-- Upload Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <h3 class="font-bold text-slate-900 text-sm mb-2">Upload Completed Member Data File</h3>
        <p class="text-xs text-slate-500 mb-4">Select your formatted CSV file containing member names, mobiles, savings balances, and loan details.</p>

        <form action="{{ route('admin.migration.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:bg-slate-50 transition cursor-pointer">
                <i class="fa-solid fa-cloud-arrow-up text-3xl text-brand-500 mb-2"></i>
                <p class="text-xs font-semibold text-slate-700">Choose CSV File to Upload & Preview</p>
                <p class="text-[10px] text-slate-400 mt-1">Accepts UTF-8 .csv format with standard headers</p>
                <input type="file" name="file" accept=".csv" required class="mt-4 mx-auto block text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-700">
            </div>

            <div class="flex items-center justify-between pt-2">
                <span class="text-xs text-slate-500">Currently {{ $existingCount }} members already present in database</span>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i>
                    <span>Preview & Validate Data</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
