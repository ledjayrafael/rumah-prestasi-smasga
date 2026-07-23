@extends('layouts.desktop')

@section('title', 'Kelola Siswa')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Kelola Akun Siswa</div>
        <a href="{{ route('admin.students.create') }}" class="bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl">+ Tambah Siswa</a>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="grid grid-cols-[1.6fr_1fr_1fr_0.8fr_1fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                <div>Siswa</div><div>NIS</div><div>Kelas</div><div>Status</div><div class="text-right">Aksi</div>
            </div>

            @forelse ($students as $student)
                <div class="grid grid-cols-[1.6fr_1fr_1fr_0.8fr_1fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100">
                    <div class="text-sm font-bold text-navy-900">{{ $student->name }}</div>
                    <div class="text-sm text-slate-600">{{ optional($student->studentProfile)->nis }}</div>
                    <div class="text-sm text-slate-600">{{ optional($student->studentProfile->schoolClass)->name ?? '—' }}</div>
                    <div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="text-right flex justify-end gap-2">
                        <a href="{{ route('admin.students.edit', $student) }}" class="text-xs font-bold text-navy-800 px-3 py-1.5 rounded-lg border border-slate-200">Ubah</a>
                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus akun siswa ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 px-3 py-1.5 rounded-lg border border-red-200">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-sm text-slate-400 py-14">Belum ada akun siswa.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $students->links() }}</div>
    </div>
@endsection
