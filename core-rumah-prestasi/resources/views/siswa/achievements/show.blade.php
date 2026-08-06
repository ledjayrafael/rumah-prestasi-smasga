@extends('layouts.siswa')

@section('title', $achievement->title)

@section('content')
    <div class="bg-navy-800 text-white px-5 py-4 flex items-center gap-3.5">
        <a href="{{ route('siswa.achievements.index') }}" class="w-9.5 h-9.5 rounded-xl bg-white/15 flex items-center justify-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div class="min-w-0">
            <div class="text-base font-extrabold truncate">{{ $achievement->title }}</div>
            <div class="text-xs opacity-70">{{ $achievement->category->label() }} &middot; Tingkat {{ $achievement->level->label() }}</div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <div class="mb-3">
            <x-status-badge :status="$achievement->status" class="text-xs" />
        </div>

        @if ($achievement->status->value === 'revision')
            @if ($achievement->reviewer_notes)
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl p-3.5 mb-3">
                    <div class="font-bold text-xs mb-1">Catatan revisi dari guru:</div>
                    {{ $achievement->reviewer_notes }}
                </div>
            @endif
            <a href="{{ route('siswa.achievements.edit', $achievement) }}"
               class="mb-3 flex items-center justify-center gap-2 h-12 rounded-2xl bg-navy-800 text-white text-sm font-bold shadow-lg shadow-navy-800/30">
                Perbaiki &amp; Kirim Ulang
            </a>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3">
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Juara</span><span class="font-bold text-navy-900">{{ $achievement->rank_label }}</span></div>
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Jenis</span><span class="font-bold text-navy-900">{{ $achievement->participation_type->label() }}</span></div>
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Tanggal</span><span class="font-bold text-navy-900">{{ $achievement->event_date->translatedFormat('d M Y') }}</span></div>
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Penyelenggara</span><span class="font-bold text-navy-900 text-right">{{ $achievement->organizer }}</span></div>
            @if ($achievement->description)
                <div class="border-t border-slate-100 pt-3 text-sm">
                    <div class="text-slate-500 mb-1">Deskripsi</div>
                    <div class="text-slate-600 leading-relaxed">{{ $achievement->description }}</div>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <div class="text-xs font-bold text-slate-600 mb-2">Berkas Bukti</div>
            <div class="flex flex-col gap-2">
                @foreach ($achievement->files as $file)
                    <a href="{{ $file->url() }}" target="_blank" class="bg-white border border-slate-200 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-navy-100 flex items-center justify-center shrink-0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#232168" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        </div>
                        <div class="min-w-0 flex-1 text-sm font-semibold text-navy-900 truncate">{{ $file->original_name }}</div>
                        <div class="text-[11px] text-slate-400 shrink-0">{{ number_format($file->size / 1024, 0) }} KB</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
