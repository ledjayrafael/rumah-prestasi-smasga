@extends('layouts.desktop')

@section('title', 'Kelola Siswa')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div>
            <div class="text-lg font-extrabold text-navy-900 tracking-tight">Kelola Akun Siswa</div>
            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">
                @if ($homeroomClasses->isEmpty())
                    Belum ada kelas binaan
                @else
                    {{ $students->total() }} akun · {{ $homeroomClasses->count() }} kelas binaan ({{ $homeroomClasses->join(', ') }})
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('guru.students.import.create') }}"
               class="inline-flex items-center gap-2 bg-white border border-slate-200 text-navy-900 text-sm font-bold px-4 py-2.5 rounded-xl hover:border-navy-800/30 hover:bg-slate-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2">
                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                Import Excel/CSV
            </a>
            <a href="{{ $homeroomClasses->isEmpty() ? '#' : route('guru.students.create') }}"
               @if ($homeroomClasses->isEmpty()) aria-disabled="true" tabindex="-1" @endif
               class="inline-flex items-center gap-2 bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-navy-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2 {{ $homeroomClasses->isEmpty() ? 'opacity-40 pointer-events-none' : '' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Siswa
            </a>
        </div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        @if ($homeroomClasses->isEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 text-sm text-amber-900 font-semibold">
                Anda belum ditetapkan sebagai wali kelas — hubungi admin sekolah untuk menetapkan kelas binaan sebelum mengelola akun siswa.
            </div>
        @elseif ($students->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl py-16 flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-navy-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <p class="text-sm font-bold text-navy-900">Belum ada akun siswa</p>
                <p class="text-xs text-slate-400 max-w-xs text-center leading-relaxed">Tambahkan satu per satu atau import dari Excel/CSV untuk kelas binaan Anda.</p>
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('guru.students.import.create') }}" class="text-xs font-bold text-navy-800 bg-navy-100 px-3.5 py-2 rounded-lg hover:bg-navy-100/70 transition-colors">Import Excel/CSV</a>
                    <a href="{{ route('guru.students.create') }}" class="text-xs font-bold text-white bg-navy-800 px-3.5 py-2 rounded-lg hover:bg-navy-700 transition-colors">Tambah Siswa</a>
                </div>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-[640px]">
                        <div class="grid grid-cols-[1.6fr_1fr_0.8fr_1fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                            <div>Siswa</div><div>NIS</div><div>Status</div><div class="text-right">Aksi</div>
                        </div>

                        @php $lastClassId = null; @endphp
                        @foreach ($students as $student)
                            @php $classId = optional($student->studentProfile)->school_class_id; @endphp
                            @if ($classId !== $lastClassId)
                                <div class="px-6 py-2 bg-navy-100/50 border-t border-slate-100 text-[11px] font-extrabold text-navy-800 uppercase tracking-wide">
                                    {{ optional($student->studentProfile->schoolClass)->name ?? 'Tanpa kelas' }}
                                </div>
                                @php $lastClassId = $classId; @endphp
                            @endif
                            <div class="grid grid-cols-[1.6fr_1fr_0.8fr_1fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100 hover:bg-slate-50/60 transition-colors">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-lg bg-navy-100 text-navy-800 flex items-center justify-center text-xs font-extrabold shrink-0">
                                        {{ Str::of($student->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                                    </div>
                                    <div class="text-sm font-bold text-navy-900 truncate">{{ $student->name }}</div>
                                </div>
                                <div class="text-sm font-mono font-semibold text-slate-600">{{ optional($student->studentProfile)->nis }}</div>
                                <div>
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $student->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                                        {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <div class="text-right flex justify-end gap-2">
                                    <a href="{{ route('guru.students.edit', $student) }}"
                                       class="text-xs font-bold text-navy-800 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 hover:border-navy-800/30 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-1">Ubah</a>
                                    <form method="POST" action="{{ route('guru.students.destroy', $student) }}" onsubmit="return confirm('Hapus akun siswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-600 px-3 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-1">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4">{{ $students->links() }}</div>
        @endif
    </div>
@endsection
