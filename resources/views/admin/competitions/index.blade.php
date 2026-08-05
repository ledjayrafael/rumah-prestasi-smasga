@extends('layouts.desktop')

@section('title', 'Kelola Info Lomba')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Kelola Info Lomba</div>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.competitions.create') }}" class="bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl">+ Tambah Lomba</a>
        @endif
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <div class="min-w-[760px]">
                    <div class="grid grid-cols-[1.8fr_1fr_1fr_1fr_1fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                        <div>Nama Lomba</div><div>Kategori</div><div>Tingkat</div><div>Tutup Pendaftaran</div><div class="text-right">{{ auth()->user()->isAdmin() ? 'Aksi' : '' }}</div>
                    </div>

                    @forelse ($competitions as $competition)
                        <div class="grid grid-cols-[1.8fr_1fr_1fr_1fr_1fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-navy-900 truncate">{{ $competition->title }}</div>
                                <div class="text-xs text-slate-400">{{ $competition->organizer }}</div>
                            </div>
                            <div class="text-sm font-semibold text-slate-600">{{ $competition->category->label() }}</div>
                            <div class="text-sm font-semibold text-slate-600">{{ $competition->level->label() }}</div>
                            <div class="text-sm font-semibold text-slate-600">{{ $competition->registration_deadline->translatedFormat('d M Y') }}</div>
                            <div class="text-right flex justify-end gap-2">
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.competitions.edit', $competition) }}" class="text-xs font-bold text-navy-800 px-3 py-1.5 rounded-lg border border-slate-200">Ubah</a>
                                    <form method="POST" action="{{ route('admin.competitions.destroy', $competition) }}"
                                          data-delete-competition-form
                                          data-competition-title="{{ $competition->title }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-600 px-3 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-1">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-slate-400 py-14">Belum ada info lomba.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4">{{ $competitions->links() }}</div>
    </div>

    <div id="delete-competition-modal"
         class="delete-competition-modal hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-6"
         role="dialog"
         aria-modal="true"
         aria-labelledby="delete-competition-modal-title"
         aria-describedby="delete-competition-modal-desc">
        <div class="delete-competition-backdrop absolute inset-0 bg-navy-950/55" data-delete-competition-backdrop aria-hidden="true"></div>

        <div id="delete-competition-panel" class="delete-competition-panel relative w-full max-w-[22rem] mx-auto">
            <div class="rounded-[1.35rem] bg-navy-950/[0.04] p-1.5 ring-1 ring-navy-950/5 shadow-xl">
                <div class="relative overflow-hidden rounded-[1.1rem] bg-white">
                    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-red-500 via-red-600 to-navy-800" aria-hidden="true"></div>

                    <div class="pl-6 pr-5 pt-6 pb-5">
                        <div class="flex items-start gap-4">
                            <div class="relative shrink-0" aria-hidden="true">
                                <div class="w-12 h-12 rounded-full bg-red-50 ring-1 ring-red-200 flex items-center justify-center">
                                    <svg class="w-[22px] h-[22px] text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 7h16"/>
                                        <path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/>
                                        <path d="M18.5 7l-.7 12.2A2 2 0 0 1 15.8 21H8.2a2 2 0 0 1-2-1.8L5.5 7"/>
                                        <path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-navy-800 text-gold-400 flex items-center justify-center ring-2 ring-white">
                                    <svg class="w-2.5 h-2.5" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true">
                                        <path d="M2.5 2.5h7v1H2.5zm0 3h7v1H2.5zm0 3h4.5v1H2.5z"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="min-w-0 flex-1 pt-0.5">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-red-600 mb-1.5">Tindakan permanen</p>
                                <h2 id="delete-competition-modal-title" class="text-[1.05rem] font-extrabold text-navy-900 leading-snug tracking-tight">
                                    Hapus info lomba?
                                </h2>
                            </div>
                        </div>

                        <p id="delete-competition-modal-desc" class="mt-4 text-sm text-slate-600 leading-relaxed">
                            Pengumuman ini akan dihapus dari portal dan tidak bisa dikembalikan.
                        </p>

                        <div class="mt-4 rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3.5 py-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-1">Info lomba</p>
                            <p id="delete-competition-modal-name" class="text-sm font-bold text-navy-900 leading-snug break-words"></p>
                        </div>

                        <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                            <button type="button"
                                    id="delete-competition-modal-cancel"
                                    class="inline-flex items-center justify-center min-h-11 px-4 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-navy-900 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2">
                                Batal
                            </button>
                            <button type="button"
                                    id="delete-competition-modal-confirm"
                                    class="inline-flex items-center justify-center min-h-11 px-4 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                Hapus permanen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .delete-competition-modal:not(.hidden) {
                animation: delete-competition-fade-in 200ms cubic-bezier(0.32, 0.72, 0, 1) both;
            }
            .delete-competition-modal:not(.hidden) .delete-competition-panel {
                animation: delete-competition-panel-in 280ms cubic-bezier(0.32, 0.72, 0, 1) both;
            }
            .delete-competition-backdrop {
                backdrop-filter: blur(2px);
            }
            @keyframes delete-competition-fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes delete-competition-panel-in {
                from { opacity: 0; transform: translateY(10px) scale(0.98); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            @media (prefers-reduced-motion: reduce) {
                .delete-competition-modal:not(.hidden),
                .delete-competition-modal:not(.hidden) .delete-competition-panel {
                    animation: none;
                }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('delete-competition-modal');
                if (!modal) return;

                const nameEl = document.getElementById('delete-competition-modal-name');
                const confirmBtn = document.getElementById('delete-competition-modal-confirm');
                const cancelBtn = document.getElementById('delete-competition-modal-cancel');
                const backdrop = modal.querySelector('[data-delete-competition-backdrop]');
                let pendingForm = null;
                let lastFocus = null;

                const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

                const trapFocus = (event) => {
                    if (event.key !== 'Tab') return;
                    const focusable = Array.from(modal.querySelectorAll(focusableSelector));
                    if (!focusable.length) return;
                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];
                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                };

                const openModal = () => {
                    lastFocus = document.activeElement;
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    cancelBtn.focus();
                    modal.addEventListener('keydown', trapFocus);
                };

                const closeModal = () => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                    modal.removeEventListener('keydown', trapFocus);
                    pendingForm = null;
                    lastFocus?.focus?.();
                };

                document.querySelectorAll('[data-delete-competition-form]').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        event.preventDefault();
                        pendingForm = form;
                        nameEl.textContent = form.dataset.competitionTitle ?? '';
                        openModal();
                    });
                });

                cancelBtn.addEventListener('click', closeModal);
                backdrop?.addEventListener('click', closeModal);
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        event.preventDefault();
                        closeModal();
                    }
                });
                confirmBtn.addEventListener('click', () => {
                    if (!pendingForm) return;
                    // Native submit bypasses the submit listener (so preventDefault won't re-block).
                    HTMLFormElement.prototype.submit.call(pendingForm);
                });
            });
        </script>
    @endpush
@endsection
