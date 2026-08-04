@extends('layouts.desktop')

@section('title', 'Kelola Siswa')

@section('content')
    @php
        $isFiltering = $search !== '' || $classFilter !== '';
        $classesByGrade = $classes->groupBy('grade_level');
        $activeClassName = $classFilter !== '' && $classFilter !== 'unassigned'
            ? optional($classes->firstWhere('id', (int) $classFilter))->name
            : null;
    @endphp

    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div>
            <div class="text-lg font-extrabold text-navy-900 tracking-tight">Kelola Akun Siswa</div>
            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">
                {{ number_format($totalStudents, 0, ',', '.') }} siswa terdaftar · {{ $classes->count() }} kelas
            </p>
        </div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        @if ($totalStudents === 0)
            <div class="bg-white border border-slate-200 rounded-2xl py-16 flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-navy-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-navy-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <p class="text-sm font-bold text-navy-900">Belum ada akun siswa</p>
                <p class="text-xs text-slate-400 max-w-xs text-center leading-relaxed">Akun siswa dibuat oleh wali kelas melalui menu Kelola Siswa pada akun guru.</p>
            </div>
        @else
            <div class="flex items-start gap-6">
                {{-- Class roster rail --}}
                <aside class="w-60 shrink-0 sticky top-0">
                    <div class="bg-white border border-slate-200 rounded-2xl p-3">
                        <div class="px-2 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wide">Roster Kelas</div>

                        <nav class="mt-1 space-y-0.5">
                            <a href="{{ route('admin.students.index', array_filter(['search' => $search ?: null])) }}"
                               class="flex items-center justify-between gap-2 px-2.5 py-2 rounded-xl text-sm font-bold transition-colors
                                      {{ $classFilter === '' ? 'bg-navy-800 text-white' : 'text-navy-900 hover:bg-slate-50' }}">
                                <span>Semua Siswa</span>
                                <span class="font-mono text-xs font-bold {{ $classFilter === '' ? 'text-white/70' : 'text-slate-400' }}">{{ $totalStudents }}</span>
                            </a>

                            @if ($unassignedCount > 0)
                                <a href="{{ route('admin.students.index', array_filter(['kelas' => 'unassigned', 'search' => $search ?: null])) }}"
                                   class="flex items-center justify-between gap-2 px-2.5 py-2 rounded-xl text-sm font-bold transition-colors
                                          {{ $classFilter === 'unassigned' ? 'bg-gold-500 text-navy-950' : 'text-gold-600 bg-gold-100/60 hover:bg-gold-100' }}">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $classFilter === 'unassigned' ? 'bg-navy-950' : 'bg-gold-500' }}"></span>
                                        Belum ada kelas
                                    </span>
                                    <span class="font-mono text-xs font-bold">{{ $unassignedCount }}</span>
                                </a>
                            @endif

                            @foreach ($classesByGrade as $grade => $group)
                                <div class="px-2.5 pt-3 pb-1 text-[10px] font-bold text-slate-300 uppercase tracking-wider">Kelas {{ $grade }}</div>
                                @foreach ($group as $class)
                                    <a href="{{ route('admin.students.index', array_filter(['kelas' => $class->id, 'search' => $search ?: null])) }}"
                                       class="flex items-center justify-between gap-2 px-2.5 py-2 rounded-xl text-sm font-semibold transition-colors
                                              {{ (string) $classFilter === (string) $class->id ? 'bg-navy-800 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span class="truncate">{{ $class->name }}</span>
                                        <span class="font-mono text-xs font-bold shrink-0 {{ (string) $classFilter === (string) $class->id ? 'text-white/70' : 'text-slate-400' }}">{{ $class->students_count }}</span>
                                    </a>
                                @endforeach
                            @endforeach
                        </nav>
                    </div>
                </aside>

                {{-- Roster --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-4">
                        <form method="GET" action="{{ route('admin.students.index') }}" class="relative flex-1 max-w-sm">
                            @if ($classFilter !== '')
                                <input type="hidden" name="kelas" value="{{ $classFilter }}">
                            @endif
                            <button type="submit" aria-label="Cari" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-navy-800">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </button>
                            <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama atau NIS…"
                                   class="w-full rounded-xl border border-slate-200 bg-white pl-9.5 pr-3 py-2.5 text-sm font-semibold text-navy-900 placeholder:text-slate-400 placeholder:font-medium focus:outline-none focus:ring-2 focus:ring-navy-700 focus:border-navy-700">
                        </form>

                        @if ($activeClassName || $classFilter === 'unassigned')
                            <span class="text-xs font-bold text-slate-400">
                                Kelas: <span class="text-navy-900">{{ $activeClassName ?? 'Belum ada kelas' }}</span>
                            </span>
                        @endif

                        @if ($isFiltering)
                            <a href="{{ route('admin.students.index') }}" class="text-xs font-bold text-slate-400 hover:text-navy-800 flex items-center gap-1">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                Reset
                            </a>
                        @endif

                        <div class="flex-1"></div>
                        <p class="text-[11px] font-semibold text-slate-400 shrink-0">{{ $students->total() }} hasil</p>
                    </div>

                    @if ($students->isEmpty())
                        <div class="bg-white border border-slate-200 rounded-2xl py-16 flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                            <p class="text-sm font-bold text-navy-900">Tidak ada siswa yang cocok</p>
                            <p class="text-xs text-slate-400 max-w-xs text-center leading-relaxed">Coba kata kunci lain atau lihat semua siswa.</p>
                            <a href="{{ route('admin.students.index') }}" class="mt-1 text-xs font-bold text-navy-800 border border-slate-200 rounded-lg px-3 py-1.5 hover:bg-slate-50">Lihat semua siswa</a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.students.bulk-move') }}" id="bulk-move-form">
                            @csrf

                            <div id="bulk-bar" class="hidden mb-4 bg-navy-800 text-white rounded-2xl px-5 py-3.5 flex items-center gap-4 flex-wrap">
                                <div class="text-sm font-bold">
                                    <span id="bulk-count">0</span> siswa dipilih <span class="text-white/50 font-semibold">(maks. 50)</span>
                                </div>
                                <span id="bulk-limit-note" class="hidden text-[11px] font-bold text-gold-400">Batas maksimal tercapai</span>
                                <span id="bulk-empty-note" class="hidden text-[11px] font-bold text-red-300">Pilih kelas tujuan terlebih dahulu</span>
                                <div class="flex-1"></div>
                                <div class="relative">
                                    <select name="school_class_id" id="bulk-class-select" required
                                            class="appearance-none rounded-xl bg-white/10 border border-white/20 text-white text-sm font-semibold pl-3.5 pr-8 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                                        <option value="" class="text-navy-900">Pilih kelas tujuan…</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" class="text-navy-900">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-white/60" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </div>
                                <button type="submit" class="bg-gold-500 text-navy-950 text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-gold-400 transition-colors">
                                    Pindah Kelas
                                </button>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                                <div class="overflow-x-auto">
                                    <div class="min-w-[720px]">
                                        <div class="grid grid-cols-[0.4fr_1.6fr_1fr_1.2fr_0.8fr] gap-3.5 px-6 py-3 bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wide items-center">
                                            <div>
                                                <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-700">
                                            </div>
                                            <div>Siswa</div><div>NIS</div><div>Kelas</div><div>Status</div>
                                        </div>

                                        @foreach ($students as $student)
                                            @php $schoolClass = optional($student->studentProfile)->schoolClass; @endphp
                                            <div class="grid grid-cols-[0.4fr_1.6fr_1fr_1.2fr_0.8fr] gap-3.5 px-6 py-3.5 items-center border-t border-slate-100 hover:bg-slate-50/60 transition-colors">
                                                <div>
                                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                           class="student-checkbox w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-700">
                                                </div>
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-9 h-9 rounded-lg bg-navy-100 text-navy-800 flex items-center justify-center text-xs font-extrabold shrink-0">
                                                        {{ Str::of($student->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                                                    </div>
                                                    <div class="text-sm font-bold text-navy-900 truncate">{{ $student->name }}</div>
                                                </div>
                                                <div class="text-sm font-mono font-semibold text-slate-600">{{ optional($student->studentProfile)->nis }}</div>
                                                <div>
                                                    @if ($schoolClass)
                                                        <span class="text-sm font-semibold text-slate-600">{{ $schoolClass->name }}</span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-gold-100 text-gold-600">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                                                            Belum ada kelas
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $student->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                                                        {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-4">{{ $students->links() }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const MAX_SELECTED = 50;
            const checkboxes = Array.from(document.querySelectorAll('.student-checkbox'));
            const selectAll = document.getElementById('select-all');
            const bulkBar = document.getElementById('bulk-bar');
            const bulkCount = document.getElementById('bulk-count');
            const bulkLimitNote = document.getElementById('bulk-limit-note');
            const bulkEmptyNote = document.getElementById('bulk-empty-note');
            const bulkForm = document.getElementById('bulk-move-form');
            const bulkClassSelect = document.getElementById('bulk-class-select');

            if (!bulkForm || checkboxes.length === 0) {
                return;
            }

            function selectedCount() {
                return checkboxes.filter((checkbox) => checkbox.checked).length;
            }

            function refreshUi() {
                const count = selectedCount();

                bulkCount.textContent = count;
                bulkBar.classList.toggle('hidden', count === 0);
                bulkLimitNote.classList.toggle('hidden', count < MAX_SELECTED);

                checkboxes.forEach((checkbox) => {
                    if (!checkbox.checked) {
                        checkbox.disabled = count >= MAX_SELECTED;
                    }
                });

                selectAll.checked = count > 0 && count === Math.min(checkboxes.length, MAX_SELECTED);
            }

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refreshUi));

            selectAll.addEventListener('change', function () {
                if (selectAll.checked) {
                    checkboxes.forEach((checkbox, index) => {
                        checkbox.checked = index < MAX_SELECTED;
                    });
                } else {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                }

                refreshUi();
            });

            bulkClassSelect.addEventListener('change', function () {
                bulkEmptyNote.classList.add('hidden');
            });

            bulkForm.addEventListener('submit', function (event) {
                const count = selectedCount();

                if (count === 0) {
                    event.preventDefault();

                    return;
                }

                if (count > MAX_SELECTED) {
                    event.preventDefault();
                    alert('Maksimal 50 siswa dapat dipindahkan sekaligus.');

                    return;
                }

                if (!bulkClassSelect.value) {
                    event.preventDefault();
                    bulkEmptyNote.classList.remove('hidden');
                    bulkClassSelect.focus();

                    return;
                }

                if (!confirm(`Pindahkan ${count} siswa ke kelas terpilih?`)) {
                    event.preventDefault();
                }
            });

            refreshUi();
        });
    </script>
@endpush
