@extends('layouts.siswa')

@section('title', 'Info Lomba')

@section('content')
    <div class="bg-navy-800 text-white px-6 pt-6 pb-5 rounded-b-[28px] relative overflow-hidden">
        <div class="absolute -right-10 -top-12 w-44 h-44 rounded-full bg-white/5"></div>
        <div class="relative">
            <div class="text-xl font-extrabold">Info Lomba</div>
            <div class="text-xs opacity-70 mt-0.5">{{ $competitions->count() + ($featured ? 1 : 0) }} lomba dibuka</div>
        </div>

        <form method="GET" action="{{ route('siswa.competitions.index') }}" class="mt-4 relative">
            <input type="hidden" name="category" value="{{ request('category') }}">
            <div class="flex items-center gap-2 bg-white/15 rounded-xl px-3.5 py-2.5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari lomba..."
                       class="bg-transparent outline-none text-sm placeholder-white/60 text-white flex-1">
            </div>
        </form>
    </div>

    <div class="px-4 mt-4">
        <x-flash />
    </div>

    <div class="flex gap-2 px-4 mt-3 overflow-x-auto pb-1">
        @foreach (['' => 'Semua', 'akademik' => 'Akademik', 'non_akademik' => 'Non-Akademik', 'organisasi' => 'Organisasi'] as $value => $label)
            <a href="{{ route('siswa.competitions.index', array_filter(['category' => $value, 'q' => request('q')])) }}"
               class="whitespace-nowrap text-xs font-semibold px-3.5 py-1.5 rounded-full
                      {{ request('category', '') === $value ? 'bg-navy-800 text-white' : 'bg-white border border-slate-200 text-slate-500' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="px-4 mt-3 flex flex-col gap-2.5">
        @if ($featured)
            <div class="bg-gradient-to-br from-navy-700 to-navy-950 rounded-2xl p-4 text-white relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-gold-400/10"></div>
                <div class="relative">
                    <span class="inline-flex items-center gap-1.5 bg-gold-400/20 text-gold-400 text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wide">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#EBC15A" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>Segera Ditutup
                    </span>
                    <div class="text-base font-extrabold mt-2.5 leading-snug">{{ $featured->title }}</div>
                    <div class="text-xs opacity-70 mt-1">{{ $featured->organizer }} &middot; Tingkat {{ $featured->level->label() }}</div>
                    <div class="flex items-center justify-between mt-3.5">
                        <div class="text-xs font-bold text-gold-400">
                            Ditutup {{ $featured->registration_deadline->translatedFormat('d M') }} &middot; {{ today()->diffInDays($featured->registration_deadline) }} hari lagi
                        </div>
                        @if ($featured->registration_url)
                            <a href="{{ $featured->registration_url }}" target="_blank" class="bg-gold-400 text-[#231e52] text-xs font-extrabold px-4 py-2 rounded-lg">Daftar</a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @forelse ($competitions as $competition)
            <div class="bg-white border border-slate-200 rounded-2xl p-3.5">
                <div class="font-bold text-sm text-navy-900">{{ $competition->title }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $competition->organizer }} &middot; Tingkat {{ $competition->level->label() }}</div>
                <div class="flex items-center gap-2 mt-2.5">
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-navy-100 text-navy-800">{{ $competition->category->label() }}</span>
                    <span class="text-[11px] font-semibold text-slate-500">Ditutup {{ $competition->registration_deadline->translatedFormat('d M') }}</span>
                </div>
            </div>
        @empty
            @if (!$featured)
                <div class="text-center text-sm text-slate-400 py-10">Tidak ada info lomba yang ditemukan.</div>
            @endif
        @endforelse
    </div>
@endsection
