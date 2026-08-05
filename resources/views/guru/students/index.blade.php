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
                    {{ $students->count() }} akun · {{ $homeroomClasses->count() }} kelas binaan ({{ $homeroomClasses->join(', ') }})
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
            <form id="bulk-destroy-form" method="POST" action="{{ route('guru.students.bulk-destroy') }}">
                @csrf
            </form>

            <div id="bulk-bar" class="hidden mb-4 bg-navy-800 text-white rounded-2xl px-5 py-3.5 flex items-center gap-4 flex-wrap">
                <div class="text-sm font-bold">
                    <span id="bulk-count">0</span> siswa dipilih
                </div>
                <div class="flex-1"></div>
                <button type="button" id="bulk-destroy-btn" class="bg-red-500 text-white text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-red-400 transition-colors">
                    Hapus Terpilih
                </button>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-[680px]">
                        @php $lastClassId = null; @endphp
                        @foreach ($students as $student)
                            @php $classId = optional($student->studentProfile)->school_class_id; @endphp
                            @if ($classId !== $lastClassId)
                                <div class="px-6 py-2 {{ $lastClassId === null ? '' : 'border-t border-slate-100' }} bg-navy-100/50 text-[11px] font-extrabold text-navy-800 uppercase tracking-wide">
                                    {{ optional($student->studentProfile->schoolClass)->name ?? 'Tanpa kelas' }}
                                </div>
                                @if ($lastClassId === null)
                                    <div class="grid grid-cols-[0.3fr_1.6fr_1fr_0.8fr_1fr] gap-3.5 px-6 py-3 bg-slate-50 border-t border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wide items-center">
                                        <div>
                                            <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-700">
                                        </div>
                                        <div>Siswa</div><div>NIS</div><div>Status</div><div class="text-right">Aksi</div>
                                    </div>
                                @endif
                                @php $lastClassId = $classId; @endphp
                            @endif
                            <div class="grid grid-cols-[0.3fr_1.6fr_1fr_0.8fr_1fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100 hover:bg-slate-50/60 transition-colors">
                                <div>
                                    <input type="checkbox" value="{{ $student->id }}" class="student-checkbox w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-700">
                                </div>
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
                                    <form method="POST" action="{{ route('guru.students.reset-password', $student) }}" data-reset-password-form data-student-name="{{ $student->name }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-amber-700 px-3 py-1.5 rounded-lg border border-amber-200 hover:bg-amber-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-1">Reset Password</button>
                                    </form>
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
        @endif
    </div>

    <div id="reset-password-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
            <h2 class="text-base font-extrabold text-navy-900 mb-2">Reset Password Siswa?</h2>
            <p class="text-sm text-slate-600 mb-5">
                Password baru akan dibuat untuk <span id="reset-password-modal-name" class="font-bold text-navy-900"></span>.
                Password lama tidak akan berlaku lagi dan siswa wajib membuat password baru saat login berikutnya.
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
                <h2 class="text-base font-extrabold text-navy-900 mb-1">Kredensial Akun Siswa</h2>
                <p class="text-xs font-semibold text-red-600 mb-4">Salin sekarang — kredensial ini tidak akan ditampilkan lagi.</p>

                <dl class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-900 space-y-2 mb-4">
                    <div><dt class="text-xs font-bold text-amber-800/70">Nama</dt><dd class="font-semibold">{{ session('credential')['name'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Login (NIS)</dt><dd class="font-mono font-bold">{{ session('credential')['login'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Password sementara</dt><dd class="font-mono font-bold">{{ session('credential')['password'] }}</dd></div>
                </dl>

                <p class="text-xs text-slate-500 mb-5">Siswa wajib ganti password saat login berikutnya.</p>

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
            initResetPasswordModal();
            initCredentialModal();

            const MAX_SELECTED = 50;
            const checkboxes = Array.from(document.querySelectorAll('.student-checkbox'));
            const selectAll = document.getElementById('select-all');
            const bulkBar = document.getElementById('bulk-bar');
            const bulkCount = document.getElementById('bulk-count');
            const bulkDestroyBtn = document.getElementById('bulk-destroy-btn');
            const bulkForm = document.getElementById('bulk-destroy-form');

            if (!bulkForm || checkboxes.length === 0) {
                return;
            }

            function selectedCheckboxes() {
                return checkboxes.filter((checkbox) => checkbox.checked);
            }

            function refreshUi() {
                const count = selectedCheckboxes().length;

                bulkCount.textContent = count;
                bulkBar.classList.toggle('hidden', count === 0);

                selectAll.checked = count > 0 && count === checkboxes.length;
                selectAll.indeterminate = count > 0 && count < checkboxes.length;
            }

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refreshUi));

            selectAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });

                refreshUi();
            });

            bulkDestroyBtn.addEventListener('click', function () {
                const selected = selectedCheckboxes();

                if (selected.length === 0) {
                    return;
                }

                if (selected.length > MAX_SELECTED) {
                    alert(`Maksimal ${MAX_SELECTED} siswa dapat dihapus sekaligus.`);

                    return;
                }

                if (!confirm(`Hapus ${selected.length} akun siswa terpilih? Tindakan ini tidak dapat dibatalkan.`)) {
                    return;
                }

                bulkForm.querySelectorAll('input[name="student_ids[]"]').forEach((input) => input.remove());

                selected.forEach((checkbox) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'student_ids[]';
                    input.value = checkbox.value;
                    bulkForm.appendChild(input);
                });

                bulkForm.submit();
            });

            refreshUi();
        });

        function initResetPasswordModal() {
            const modal = document.getElementById('reset-password-modal');
            if (!modal) return;

            const nameEl = document.getElementById('reset-password-modal-name');
            const confirmBtn = document.getElementById('reset-password-modal-confirm');
            const cancelBtn = document.getElementById('reset-password-modal-cancel');
            let pendingForm = null;

            const closeModal = () => {
                modal.classList.add('hidden');
                pendingForm = null;
            };

            document.querySelectorAll('[data-reset-password-form]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    pendingForm = form;
                    nameEl.textContent = form.dataset.studentName ?? '';
                    modal.classList.remove('hidden');
                });
            });

            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
            confirmBtn.addEventListener('click', () => {
                pendingForm?.submit();
            });
        }

        function initCredentialModal() {
            const modal = document.getElementById('credential-modal');
            if (!modal) return;

            const closeBtn = document.getElementById('credential-modal-close');
            const closeModal = () => modal.remove();

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && document.body.contains(modal)) closeModal();
            });
        }
    </script>
@endpush
