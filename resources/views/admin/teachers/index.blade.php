@extends('layouts.desktop')

@section('title', 'Kelola Guru')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Kelola Akun Guru</div>
        <a href="{{ route('admin.teachers.create') }}" class="bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl">+ Tambah Guru</a>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden" data-sortable-table>
            <div class="grid grid-cols-[1.3fr_1.3fr_0.8fr_1.2fr_0.7fr_1.9fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                <button type="button" data-sort-col="0" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Guru <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                <button type="button" data-sort-col="1" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Email <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                <button type="button" data-sort-col="2" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Peran <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                <button type="button" data-sort-col="3" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Kelas Binaan <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                <button type="button" data-sort-col="4" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Status <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                <div class="text-right">Aksi</div>
            </div>

            @forelse ($teachers as $teacher)
                <div class="grid grid-cols-[1.3fr_1.3fr_0.8fr_1.2fr_0.7fr_1.9fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100" data-table-row>
                    <div class="text-sm font-bold text-navy-900">{{ $teacher->name }}</div>
                    <div class="text-sm text-slate-600">{{ $teacher->email }}</div>
                    <div class="text-sm font-semibold text-slate-600">{{ optional($teacher->teacherProfile)->position?->label() }}</div>
                    <div class="text-sm text-slate-600">{{ $teacher->taughtClasses->pluck('name')->join(', ') ?: '—' }}</div>
                    <div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $teacher->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="text-right flex justify-end gap-2">
                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="text-xs font-bold text-navy-800 px-3 py-1.5 rounded-lg border border-slate-200">Ubah</a>
                        <form method="POST" action="{{ route('admin.teachers.reset-password', $teacher) }}" data-reset-password-form data-teacher-name="{{ $teacher->name }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-amber-700 px-3 py-1.5 rounded-lg border border-amber-200">Reset Password</button>
                        </form>
                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Hapus akun guru ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 px-3 py-1.5 rounded-lg border border-red-200">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-sm text-slate-400 py-14">Belum ada akun guru.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $teachers->links() }}</div>
    </div>

    <div id="reset-password-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
            <h2 class="text-base font-extrabold text-navy-900 mb-2">Reset Password Guru?</h2>
            <p class="text-sm text-slate-600 mb-5">
                Password baru akan dibuat untuk <span id="reset-password-modal-name" class="font-bold text-navy-900"></span>.
                Password lama tidak akan berlaku lagi dan guru wajib membuat password baru saat login berikutnya.
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" id="reset-password-modal-cancel" class="text-sm font-bold text-slate-500 px-4 py-2 rounded-xl border border-slate-200">Batal</button>
                <button type="button" id="reset-password-modal-confirm" class="text-sm font-bold text-white bg-amber-600 px-4 py-2 rounded-xl">Ya, Reset Password</button>
            </div>
        </div>
    </div>

    @if (session('credential'))
        <div id="credential-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 px-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h2 class="text-base font-extrabold text-navy-900 mb-1">Kredensial Akun Guru</h2>
                <p class="text-xs font-semibold text-red-600 mb-4">Salin sekarang — kredensial ini tidak akan ditampilkan lagi.</p>

                <dl class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-900 space-y-2 mb-4">
                    <div><dt class="text-xs font-bold text-amber-800/70">Nama</dt><dd class="font-semibold">{{ session('credential')['name'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Login (email)</dt><dd class="font-mono font-bold">{{ session('credential')['login'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Password sementara</dt><dd class="font-mono font-bold">{{ session('credential')['password'] }}</dd></div>
                </dl>

                <p class="text-xs text-slate-500 mb-5">Guru wajib ganti password saat login berikutnya.</p>

                <div class="flex justify-end">
                    <button type="button" id="credential-modal-close" class="text-sm font-bold text-white bg-navy-800 px-4 py-2 rounded-xl">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        @vite('resources/js/admin-teachers-table.js')
    @endpush
@endsection
