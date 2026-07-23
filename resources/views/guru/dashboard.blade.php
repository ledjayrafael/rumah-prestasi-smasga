@extends('layouts.desktop')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Dashboard</div>
    </div>

    <div class="flex-1 p-8">
        <x-flash />

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Menunggu Verifikasi</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Disetujui (bln ini)</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['approved_this_month'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Perlu Revisi</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['revision'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Siswa Binaan</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['active_students'] }}</div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('guru.verification.index') }}" class="inline-flex items-center gap-2 bg-navy-800 text-white text-sm font-bold px-5 py-3 rounded-xl">
                Buka Antrean Verifikasi
            </a>
        </div>
    </div>
@endsection
