@extends('layouts.siswa')

@section('title', 'Beranda')

@section('content')
    <div class="bg-navy-800 text-white px-6 pt-2 pb-7 rounded-b-[28px] relative overflow-hidden">
        <div class="absolute -right-10 -top-12 w-44 h-44 rounded-full bg-white/5"></div>
        <div class="flex items-center gap-3 relative pt-3">
            <div class="w-10.5 h-10.5 rounded-xl bg-white flex items-center justify-center p-1 shrink-0">
                <img src="{{ asset('images/logo-sman1.png') }}" alt="logo" class="h-8 w-auto">
            </div>
            <div>
                <div class="text-xs font-semibold opacity-70">SMAN 1 Tenggarang</div>
                <div class="text-sm font-bold">Portal Prestasi Siswa</div>
            </div>
        </div>
        <div class="mt-5 relative">
            <div class="text-sm opacity-75">Halo,</div>
            <div class="text-xl font-extrabold mt-0.5">{{ $student->name }}</div>
            <div class="inline-flex mt-2 gap-1.5 items-center bg-white/10 px-3 py-1.5 rounded-full text-xs font-semibold">
                {{ optional($student->studentProfile->schoolClass)->name ?? '—' }} &middot; NIS {{ $student->studentProfile->nis }}
            </div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <x-flash />
    </div>

    <div class="flex gap-2.5 px-4 mt-2">
        <div class="flex-1 bg-white border border-slate-200 rounded-2xl p-3.5">
            <div class="text-2xl font-extrabold text-navy-800">{{ $stats['total'] }}</div>
            <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Total</div>
        </div>
        <div class="flex-1 bg-white border border-slate-200 rounded-2xl p-3.5">
            <div class="text-2xl font-extrabold text-green-600">{{ $stats['approved'] }}</div>
            <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Disetujui</div>
        </div>
        <div class="flex-1 bg-white border border-slate-200 rounded-2xl p-3.5">
            <div class="text-2xl font-extrabold text-amber-600">{{ $stats['pending'] }}</div>
            <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Menunggu</div>
        </div>
    </div>

    <div class="flex items-center justify-between px-5 mt-5 mb-2">
        <div class="text-base font-extrabold text-navy-900">Prestasi Saya</div>
        <a href="{{ route('siswa.achievements.index') }}" class="text-xs font-bold text-gold-600">Lihat semua</a>
    </div>

    <div class="px-4 flex flex-col gap-2.5">
        @forelse ($achievements as $achievement)
            <a href="{{ route('siswa.achievements.show', $achievement) }}" class="bg-white border border-slate-200 rounded-2xl p-3.5 block">
                <div class="font-bold text-sm text-navy-900">{{ $achievement->title }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $achievement->category->label() }} &middot; Tingkat {{ $achievement->level->label() }}</div>
                <div class="flex items-center gap-2 mt-2.5">
                    <x-status-badge :status="$achievement->status" />
                    <span class="text-[11px] font-semibold text-slate-500">{{ $achievement->event_date->translatedFormat('d M Y') }}</span>
                </div>
            </a>
        @empty
            <div class="text-center text-sm text-slate-400 py-10">Belum ada prestasi yang diajukan.</div>
        @endforelse
    </div>
@endsection
