@extends('layouts.desktop')

@section('title', 'Kelola Kelas')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Kelola Kelas</div>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.classes.create') }}" class="bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl">+ Tambah Kelas</a>
        @endif
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <div class="min-w-[720px]">
                    <div class="grid grid-cols-[1.4fr_1fr_1.4fr_1fr_1fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                        <div>Nama Kelas</div><div>Tingkat</div><div>Wali Kelas</div><div>Jumlah Siswa</div><div class="text-right">{{ auth()->user()->isAdmin() ? 'Aksi' : '' }}</div>
                    </div>

                    @forelse ($classes as $class)
                        <div class="grid grid-cols-[1.4fr_1fr_1.4fr_1fr_1fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100">
                            <div class="text-sm font-bold text-navy-900">{{ $class->name }}</div>
                            <div class="text-sm font-semibold text-slate-600">Kelas {{ $class->grade_level }}</div>
                            <div class="text-sm font-semibold text-slate-600">{{ optional($class->homeroomTeacher)->name ?? '—' }}</div>
                            <div class="text-sm font-semibold text-slate-600">{{ $class->students_count }}</div>
                            <div class="text-right flex justify-end gap-2">
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="text-xs font-bold text-navy-800 px-3 py-1.5 rounded-lg border border-slate-200">Ubah</a>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-600 px-3 py-1.5 rounded-lg border border-red-200">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-slate-400 py-14">Belum ada kelas. Tambahkan kelas pertama.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
