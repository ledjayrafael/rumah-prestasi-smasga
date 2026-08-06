@extends('layouts.desktop')

@section('title', 'Verifikasi Prestasi')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div>
            <div class="text-lg font-extrabold text-navy-900">Verifikasi Prestasi</div>
            <div class="text-xs text-slate-500">{{ $counts['pending'] }} pengajuan menunggu persetujuan Anda</div>
        </div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3.5 border-b border-slate-100">
                @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'revision' => 'Perlu Revisi', 'all' => 'Semua'] as $value => $label)
                    <a href="{{ route('guru.verification.index', ['status' => $value]) }}"
                       class="text-xs font-bold px-3.5 py-1.5 rounded-lg {{ $status === $value ? 'bg-navy-800 text-white' : 'text-slate-500' }}">
                        {{ $label }} @if($value !== 'all') &middot; {{ $counts[$value] }} @endif
                    </a>
                @endforeach
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    <div class="grid grid-cols-[2.2fr_2.6fr_1.2fr_1.1fr_1fr_1fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                        <div>Siswa</div><div>Prestasi</div><div>Tingkat</div><div>Tanggal</div><div>Status</div><div class="text-right">Aksi</div>
                    </div>

                    @forelse ($achievements as $achievement)
                        <div class="grid grid-cols-[2.2fr_2.6fr_1.2fr_1.1fr_1fr_1fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-navy-100 text-navy-800 flex items-center justify-center text-xs font-extrabold shrink-0">
                                    {{ Str::of($achievement->student->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-navy-900 truncate">{{ $achievement->student->name }}</div>
                                    <div class="text-xs text-slate-400">{{ optional($achievement->student->studentProfile->schoolClass)->name }}</div>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-navy-900 truncate">{{ $achievement->title }}</div>
                                <div class="text-xs text-slate-400">{{ $achievement->category->label() }}</div>
                            </div>
                            <div class="text-sm font-semibold text-slate-600">{{ $achievement->level->label() }}</div>
                            <div class="text-sm font-semibold text-slate-600">{{ $achievement->event_date->translatedFormat('d M Y') }}</div>
                            <div><x-status-badge :status="$achievement->status" /></div>
                            <div class="text-right">
                                <a href="{{ route('guru.verification.show', $achievement) }}" class="inline-flex items-center gap-1.5 bg-navy-800 text-white text-xs font-bold px-3.5 py-2 rounded-lg">Tinjau</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-slate-400 py-14">Tidak ada pengajuan pada status ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $achievements->links() }}
        </div>
    </div>
@endsection
