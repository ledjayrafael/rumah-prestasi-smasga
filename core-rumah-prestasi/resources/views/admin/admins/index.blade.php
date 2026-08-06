@extends('layouts.desktop')

@section('title', 'Kelola Admin')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Kelola Akun Admin</div>
        <a href="{{ route('admin.admins.create') }}" class="bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl">+ Tambah Admin</a>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden" data-sortable-table>
            <div class="overflow-x-auto">
                <div class="min-w-[720px]">
                    <div class="grid grid-cols-[1.5fr_1.5fr_0.7fr_1.9fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                        <button type="button" data-sort-col="0" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Nama <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                        <button type="button" data-sort-col="1" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Email <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                        <button type="button" data-sort-col="2" class="flex items-center gap-1 text-left uppercase tracking-wide hover:text-slate-600">Status <span data-sort-icon class="opacity-0 text-[9px]">▲</span></button>
                        <div class="text-right">Aksi</div>
                    </div>

                    @forelse ($admins as $admin)
                        <div class="grid grid-cols-[1.5fr_1.5fr_0.7fr_1.9fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100" data-table-row>
                            <div class="text-sm font-bold text-navy-900 truncate min-w-0" title="{{ $admin->name }}">{{ $admin->name }}</div>
                            <div class="text-sm text-slate-600 truncate min-w-0" title="{{ $admin->email }}">{{ $admin->email }}</div>
                            <div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $admin->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $admin->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="text-right flex justify-end gap-2">
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="text-xs font-bold text-navy-800 px-3 py-1.5 rounded-lg border border-slate-200">Ubah</a>
                                <form method="POST" action="{{ route('admin.admins.reset-password', $admin) }}" data-reset-password-form data-admin-name="{{ $admin->name }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-amber-700 px-3 py-1.5 rounded-lg border border-amber-200">Reset Password</button>
                                </form>
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" onsubmit="return confirm('Hapus akun admin ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Hapus" title="Hapus"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-1">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-slate-400 py-14">Belum ada akun admin.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4">{{ $admins->links() }}</div>
    </div>

    <div id="reset-password-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
            <h2 class="text-base font-extrabold text-navy-900 mb-2">Reset Password Admin?</h2>
            <p class="text-sm text-slate-600 mb-5">
                Password baru akan dibuat untuk <span id="reset-password-modal-name" class="font-bold text-navy-900"></span>.
                Password lama tidak akan berlaku lagi dan admin wajib membuat password baru saat login berikutnya.
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" id="reset-password-modal-cancel" class="text-sm font-bold text-slate-500 px-4 py-2 rounded-xl border border-slate-200">Batal</button>
                <button type="button" id="reset-password-modal-confirm" class="text-sm font-bold text-white bg-amber-600 px-4 py-2 rounded-xl">Ya, Reset Password</button>
            </div>
        </div>
    </div>

    @if (session('credential'))
        <div id="credential-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 px-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                <h2 class="text-base font-extrabold text-navy-900 mb-1">Kredensial Akun Admin</h2>
                <p class="text-xs text-slate-500 mb-4">Simpan password sementara ini. Hanya ditampilkan sekali.</p>
                <dl class="space-y-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-navy-900 mb-5">
                    <div><dt class="text-xs font-bold text-amber-800/70">Nama</dt><dd class="font-semibold">{{ session('credential')['name'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Login (email)</dt><dd class="font-mono font-bold">{{ session('credential')['login'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Password sementara</dt><dd class="font-mono font-bold">{{ session('credential')['password'] }}</dd></div>
                </dl>
                <div class="flex justify-end">
                    <button type="button" id="credential-modal-close" class="text-sm font-bold text-white bg-navy-800 px-4 py-2 rounded-xl">Tutup</button>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pendingForm = null;
            const modal = document.getElementById('reset-password-modal');
            const modalName = document.getElementById('reset-password-modal-name');
            const cancelBtn = document.getElementById('reset-password-modal-cancel');
            const confirmBtn = document.getElementById('reset-password-modal-confirm');

            document.querySelectorAll('[data-reset-password-form]').forEach((form) => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    pendingForm = form;
                    modalName.textContent = form.dataset.adminName || 'admin ini';
                    modal.classList.remove('hidden');
                });
            });

            cancelBtn.addEventListener('click', function () {
                pendingForm = null;
                modal.classList.add('hidden');
            });

            confirmBtn.addEventListener('click', function () {
                if (pendingForm) {
                    pendingForm.submit();
                }
            });

            const credentialModal = document.getElementById('credential-modal');
            const credentialClose = document.getElementById('credential-modal-close');
            if (credentialModal && credentialClose) {
                credentialClose.addEventListener('click', function () {
                    credentialModal.classList.add('hidden');
                });
            }
        });
    </script>
@endpush
