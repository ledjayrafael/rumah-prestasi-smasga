@extends('layouts.desktop')

@section('title', 'Import Siswa')

@php
    $previewClass = $homeroomClasses->first() ?? 'XI MIPA 2';
@endphp

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center gap-3.5 px-8 shrink-0">
        <a href="{{ route('guru.students.index') }}" class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center hover:bg-slate-100 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4B4A66" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <div class="text-lg font-extrabold text-navy-900 tracking-tight">Import data siswa</div>
            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Excel atau CSV · maks. 500 baris</p>
        </div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <div class="max-w-3xl mx-auto flex flex-col gap-5">
            <x-flash />

            @if (session('import_success') !== null || session('import_errors'))
                <div class="rounded-2xl border px-5 py-4 {{ (int) session('import_success', 0) > 0 ? 'border-emerald-200 bg-emerald-50/70' : 'border-red-200 bg-red-50/70' }}">
                    <div class="flex flex-col gap-3">
                        @if ((int) session('import_success', 0) > 0)
                            <p class="text-sm font-extrabold text-emerald-800">
                                {{ session('import_success') }} akun siswa siap dipakai.
                            </p>
                        @endif
                        @if (session('import_credentials_available'))
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('guru.students.import.credentials') }}"
                                   class="inline-flex self-start items-center gap-2 text-sm font-bold text-navy-800 bg-white border border-slate-200 px-3.5 py-2 rounded-xl hover:border-navy-800/30 transition-colors">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                                    Unduh kredensial login
                                </a>
                                <p class="text-[11px] font-medium text-slate-500">Tersedia sekali, dalam 10 menit.</p>
                            </div>
                        @endif
                        @if ($importErrors = session('import_errors'))
                            <ul class="flex flex-col gap-1 text-sm text-red-800">
                                @foreach ($importErrors as $message)
                                    <li class="flex gap-2"><span class="text-red-400 shrink-0">—</span><span>{{ $message }}</span></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Satu permukaan: strip konteks + specimen + unggah --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                {{-- Strip kelas & template --}}
                <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0">
                        @if ($homeroomClasses->isNotEmpty())
                            <p class="text-[11px] font-bold text-slate-400 mb-1">Kelas binaan</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($homeroomClasses as $className)
                                    <span class="text-xs font-bold text-navy-800 bg-navy-100 px-2.5 py-1 rounded-lg">{{ $className }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-amber-800 font-semibold leading-snug">
                                Belum ada kelas binaan — minta admin menetapkan Anda sebagai wali kelas.
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('guru.students.import.template') }}"
                       class="inline-flex items-center gap-2 shrink-0 text-sm font-bold text-navy-900 bg-gold-100 border border-gold-400/40 px-3.5 py-2 rounded-xl hover:bg-gold-400/25 transition-colors">
                        <svg class="w-4 h-4 text-gold-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                        Unduh template
                    </a>
                </div>

                <div class="grid sm:grid-cols-[1fr_1.15fr] sm:items-start divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                    {{-- Specimen daftar absensi (signature) --}}
                    <div class="p-5 flex flex-col gap-3">
                        <p class="text-[11px] font-bold text-slate-400">Contoh isi file</p>
                        <div class="rounded-lg border border-slate-200 overflow-hidden text-[11px] leading-none">
                            <div class="grid grid-cols-[1.4fr_0.7fr_1fr] bg-navy-800 text-white font-bold text-[10px] uppercase tracking-wider">
                                <div class="px-2.5 py-2 border-r border-white/10">nama</div>
                                <div class="px-2.5 py-2 border-r border-white/10">nis</div>
                                <div class="px-2.5 py-2">kelas</div>
                            </div>
                            <div class="grid grid-cols-[1.4fr_0.7fr_1fr] bg-white text-slate-700 font-mono">
                                <div class="px-2.5 py-2.5 border-r border-t border-slate-100 truncate">Ahmad Fadli</div>
                                <div class="px-2.5 py-2.5 border-r border-t border-slate-100 font-semibold text-navy-700">22001</div>
                                <div class="px-2.5 py-2.5 border-t border-slate-100 truncate">{{ $previewClass }}</div>
                            </div>
                            <div class="grid grid-cols-[1.4fr_0.7fr_1fr] bg-slate-50 text-slate-400 font-mono">
                                <div class="px-2.5 py-2 border-r border-t border-slate-100">Siti Nurhaliza</div>
                                <div class="px-2.5 py-2 border-r border-t border-slate-100">22002</div>
                                <div class="px-2.5 py-2 border-t border-slate-100 truncate">{{ $previewClass }}</div>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Baris pertama = header. Nama kelas harus sama persis dengan data sekolah.
                        </p>
                    </div>

                    {{-- Unggah --}}
                    <form method="POST" action="{{ route('guru.students.import.store') }}" enctype="multipart/form-data" class="p-5 flex flex-col gap-3" id="import-form">
                        @csrf
                        <p class="text-[11px] font-bold text-slate-400">Unggah file</p>
                        <label id="import-dropzone"
                               class="relative flex h-28 shrink-0 flex-row items-center justify-center gap-3 rounded-xl border border-dashed border-slate-300 bg-[#F8F8FC] px-4 text-left cursor-pointer transition-colors hover:border-navy-600 hover:bg-navy-100/40 focus-within:ring-2 focus-within:ring-navy-700 focus-within:ring-offset-2 motion-reduce:transition-none">
                            <input type="file" name="file" id="import-file" accept=".csv,.xlsx,.xls" required class="sr-only">
                            <svg class="w-6 h-6 text-navy-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5M12 3v12"/></svg>
                            <span class="flex flex-col min-w-0">
                                <span class="text-sm font-bold text-navy-900 truncate" id="import-file-label">Pilih atau letakkan file</span>
                                <span class="text-[11px] font-medium text-slate-400">.xlsx · .xls · .csv · maks. 2 MB</span>
                            </span>
                        </label>
                        @error('file')
                            <p class="text-xs font-semibold text-red-600" role="alert">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                class="w-full bg-navy-800 text-white text-sm font-extrabold px-5 py-3 rounded-xl hover:bg-navy-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2 disabled:opacity-45 disabled:pointer-events-none"
                                @disabled($homeroomClasses->isEmpty())>
                            Mulai import
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('import-file');
            var label = document.getElementById('import-file-label');
            var zone = document.getElementById('import-dropzone');
            if (!input || !label || !zone) return;

            function setName(file) {
                label.textContent = file ? file.name : 'Pilih atau letakkan file';
                zone.classList.toggle('border-navy-600', !!file);
                zone.classList.toggle('bg-navy-100/40', !!file);
            }

            input.addEventListener('change', function () {
                setName(input.files && input.files[0]);
            });

            ['dragenter', 'dragover'].forEach(function (ev) {
                zone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    zone.classList.add('border-navy-600', 'bg-navy-100/40');
                });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                zone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    if (ev === 'drop' && e.dataTransfer.files.length) {
                        input.files = e.dataTransfer.files;
                        setName(e.dataTransfer.files[0]);
                    } else if (ev === 'dragleave' && !input.files.length) {
                        zone.classList.remove('border-navy-600', 'bg-navy-100/40');
                    }
                });
            });
        })();
    </script>
@endsection
