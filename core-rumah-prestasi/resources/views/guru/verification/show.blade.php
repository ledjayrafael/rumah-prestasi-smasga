@extends('layouts.desktop')

@section('title', $achievement->title)

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center gap-3.5 px-8 shrink-0">
        <a href="{{ route('guru.verification.index') }}" class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4B4A66" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <div class="text-xs text-slate-400 font-semibold">Verifikasi Prestasi / Tinjau</div>
            <div class="text-lg font-extrabold text-navy-900">{{ $achievement->title }}</div>
        </div>
        <div class="ml-auto"><x-status-badge :status="$achievement->status" /></div>
    </div>

    <div class="flex-1 p-8 overflow-auto grid grid-cols-[1fr_400px] gap-6 items-start">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="text-sm font-bold text-navy-900 mb-3">Berkas Bukti</div>
            <div class="flex flex-col gap-2.5">
                @foreach ($achievement->files as $file)
                    <a href="{{ $file->url() }}" target="_blank" class="flex items-center gap-3 border border-slate-200 rounded-xl p-3 hover:bg-slate-50">
                        <div class="w-10 h-10 rounded-lg bg-navy-100 flex items-center justify-center shrink-0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#232168" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-navy-900 truncate">{{ $file->original_name }}</div>
                            <div class="text-xs text-slate-400">{{ number_format($file->size / 1024, 0) }} KB &middot; buka di tab baru</div>
                        </div>
                    </a>

                    @if ($file->isImage())
                        <img src="{{ $file->url() }}" alt="{{ $file->original_name }}" class="rounded-xl border border-slate-200 max-h-[420px] object-contain w-full">
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy-100 text-navy-800 flex items-center justify-center text-sm font-extrabold shrink-0">
                    {{ Str::of($achievement->student->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                </div>
                <div>
                    <div class="text-sm font-extrabold text-navy-900">{{ $achievement->student->name }}</div>
                    <div class="text-xs text-slate-500">{{ optional($achievement->student->studentProfile->schoolClass)->name }} &middot; NIS {{ $achievement->student->studentProfile->nis }}</div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-sm font-extrabold text-navy-900 mb-3">Detail Pengajuan</div>
                <div class="flex flex-col gap-2.5 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-slate-500">Kategori</span><span class="font-bold text-navy-900">{{ $achievement->category->label() }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500">Tingkat</span><span class="font-bold text-navy-900">{{ $achievement->level->label() }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500">Juara</span><span class="font-bold text-navy-900">{{ $achievement->rank_label }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500">Jenis</span><span class="font-bold text-navy-900">{{ $achievement->participation_type->label() }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500">Tanggal</span><span class="font-bold text-navy-900">{{ $achievement->event_date->translatedFormat('d M Y') }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500">Penyelenggara</span><span class="font-bold text-navy-900 text-right">{{ $achievement->organizer }}</span></div>
                    @if ($achievement->description)
                        <div class="border-t border-slate-100 pt-2.5">
                            <div class="text-slate-500 mb-1">Deskripsi</div>
                            <div class="text-slate-600 leading-relaxed">{{ $achievement->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($achievement->status->value === 'pending')
                <form method="POST" action="{{ route('guru.verification.revise', $achievement) }}" id="revision-form">
                    @csrf
                    <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                        <label class="block text-xs font-bold text-slate-600 mb-2">Catatan untuk siswa (wajib jika minta revisi)</label>
                        <textarea name="reviewer_notes" rows="3" placeholder="Tulis catatan jika perlu revisi..."
                                  class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">{{ old('reviewer_notes') }}</textarea>
                    </div>
                </form>

                <div class="flex gap-3">
                    <button type="submit" form="revision-form"
                            class="flex-1 h-12.5 rounded-2xl bg-white border-[1.5px] border-red-200 text-red-600 text-sm font-bold flex items-center justify-center gap-2">
                        Minta Revisi
                    </button>
                    <form method="POST" action="{{ route('guru.verification.approve', $achievement) }}" class="flex-[1.4]">
                        @csrf
                        <button type="submit" class="w-full h-12.5 rounded-2xl bg-green-600 text-white text-sm font-bold shadow-lg shadow-green-600/30">
                            Setujui Prestasi
                        </button>
                    </form>
                </div>
            @elseif ($achievement->reviewer_notes)
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4.5 text-sm text-slate-600">
                    <div class="font-bold text-navy-900 text-xs mb-1.5">Catatan verifikator</div>
                    {{ $achievement->reviewer_notes }}
                </div>
            @endif
        </div>
    </div>
@endsection
