@extends('layouts.siswa')

@section('title', 'Prestasi Saya')

@section('content')
    <div class="bg-navy-800 text-white px-6 pt-6 pb-6 rounded-b-[28px] relative overflow-hidden">
        <div class="absolute -right-10 -top-12 w-44 h-44 rounded-full bg-white/5"></div>
        <div class="relative">
            <div class="text-xl font-extrabold">Prestasi Saya</div>
            <div class="text-xs opacity-70 mt-0.5">{{ $counts['all'] }} prestasi tercatat</div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <x-flash />
    </div>

    <div class="flex gap-2 px-4 mt-3 overflow-x-auto pb-1">
        @foreach (['' => ['Semua', $counts['all']], 'pending' => ['Menunggu', $counts['pending']], 'approved' => ['Disetujui', $counts['approved']], 'revision' => ['Revisi', $counts['revision']]] as $value => [$label, $count])
            <a href="{{ route('siswa.achievements.index', $value ? ['status' => $value] : []) }}"
               class="whitespace-nowrap text-xs font-semibold px-3.5 py-1.5 rounded-full
                      {{ request('status', '') === $value ? 'bg-navy-800 text-white' : 'bg-white border border-slate-200 text-slate-500' }}">
                {{ $label }} &middot; {{ $count }}
            </a>
        @endforeach
    </div>

    <div class="px-4 mt-3 flex flex-col gap-2.5">
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
            <div class="text-center text-sm text-slate-400 py-10">Tidak ada prestasi pada kategori ini.</div>
        @endforelse
    </div>

    <div class="px-4 mt-4">
        {{ $achievements->links() }}
    </div>
@endsection
