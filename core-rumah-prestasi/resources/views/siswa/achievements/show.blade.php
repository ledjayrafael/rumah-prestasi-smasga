@extends('layouts.siswa')

@section('title', $achievement->title)

@section('content')
    <div class="bg-navy-800 text-white px-5 py-4 flex items-center gap-3.5">
        <a href="{{ route('siswa.achievements.index') }}" class="w-9.5 h-9.5 rounded-xl bg-white/15 flex items-center justify-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div class="min-w-0">
            <div class="text-base font-extrabold truncate">{{ $achievement->title }}</div>
            <div class="text-xs opacity-70">{{ $achievement->category->label() }} &middot; Tingkat {{ $achievement->level->label() }}</div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <div class="mb-3">
            <x-status-badge :status="$achievement->status" class="text-xs" />
        </div>

        @if ($achievement->status->value === 'revision')
            @if ($achievement->reviewer_notes)
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl p-3.5 mb-3">
                    <div class="font-bold text-xs mb-1">Catatan revisi dari guru:</div>
                    {{ $achievement->reviewer_notes }}
                </div>
            @endif
            <a href="{{ route('siswa.achievements.edit', $achievement) }}"
               class="mb-3 flex items-center justify-center gap-2 h-12 rounded-2xl bg-navy-800 text-white text-sm font-bold shadow-lg shadow-navy-800/30">
                Perbaiki &amp; Kirim Ulang
            </a>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3">
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Juara</span><span class="font-bold text-navy-900">{{ $achievement->rank_label }}</span></div>
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Jenis</span><span class="font-bold text-navy-900">{{ $achievement->participation_type->label() }}</span></div>
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Tanggal</span><span class="font-bold text-navy-900">{{ $achievement->event_date->translatedFormat('d M Y') }}</span></div>
            <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Penyelenggara</span><span class="font-bold text-navy-900 text-right">{{ $achievement->organizer }}</span></div>
            @if ($achievement->description)
                <div class="border-t border-slate-100 pt-3 text-sm">
                    <div class="text-slate-500 mb-1">Deskripsi</div>
                    <div class="text-slate-600 leading-relaxed">{{ $achievement->description }}</div>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <div class="text-xs font-bold text-slate-600 mb-2">Berkas Bukti</div>
            <div class="flex flex-col gap-2">
                @foreach ($achievement->files as $file)
                    @if ($file->isImage())
                        <button type="button"
                                data-preview-type="image" data-preview-src="{{ $file->url() }}" data-preview-name="{{ $file->original_name }}"
                                class="file-preview-trigger bg-white border border-slate-200 rounded-xl p-3 flex items-center gap-3 text-left w-full">
                            <img src="{{ $file->url() }}" alt="{{ $file->original_name }}"
                                 class="w-11 h-11 rounded-lg object-cover shrink-0 bg-slate-100">
                            <div class="min-w-0 flex-1 text-sm font-semibold text-navy-900 truncate">{{ $file->original_name }}</div>
                            <div class="text-[11px] text-slate-400 shrink-0">{{ number_format($file->size / 1024, 0) }} KB</div>
                        </button>
                    @else
                        <button type="button"
                                data-preview-type="file" data-preview-src="{{ $file->url() }}" data-preview-name="{{ $file->original_name }}"
                                class="file-preview-trigger bg-white border border-slate-200 rounded-xl p-3 flex items-center gap-3 text-left w-full">
                            <div class="w-11 h-11 rounded-lg bg-navy-100 flex items-center justify-center shrink-0">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#232168" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            </div>
                            <div class="min-w-0 flex-1 text-sm font-semibold text-navy-900 truncate">{{ $file->original_name }}</div>
                            <div class="text-[11px] text-slate-400 shrink-0">{{ number_format($file->size / 1024, 0) }} KB</div>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div id="image-preview-modal" class="fixed inset-0 z-[100] hidden items-end justify-center bg-navy-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="image-preview-name">
        <div id="image-preview-panel" class="w-full max-w-md mx-auto rounded-t-3xl bg-navy-950 p-4 pb-[calc(1rem+env(safe-area-inset-bottom))] translate-y-full transition-transform duration-[250ms] ease-out">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div id="image-preview-name" class="text-xs font-semibold text-white/80 truncate"></div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="image-preview-download" href="#" download class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center active:scale-95 transition-transform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                    </a>
                    <button type="button" id="image-preview-close" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center active:scale-95 transition-transform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden bg-slate-900">
                <img id="image-preview-img" src="" alt="" class="w-full max-h-[75vh] object-contain">
                <iframe id="image-preview-frame" src="about:blank" class="w-full h-[80vh] hidden" title="Pratinjau berkas"></iframe>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('image-preview-modal');
            if (!modal || modal.dataset.bound === '1') return;
            modal.dataset.bound = '1';

            var panel = document.getElementById('image-preview-panel');
            var img = document.getElementById('image-preview-img');
            var frame = document.getElementById('image-preview-frame');
            var name = document.getElementById('image-preview-name');
            var downloadBtn = document.getElementById('image-preview-download');
            var closeBtn = document.getElementById('image-preview-close');
            var closeTimer = null;

            function openModal(type, src, filename) {
                clearTimeout(closeTimer);
                name.textContent = filename;
                downloadBtn.href = src;
                downloadBtn.setAttribute('download', filename);

                if (type === 'image') {
                    img.src = src;
                    img.alt = filename;
                    img.classList.remove('hidden');
                    frame.classList.add('hidden');
                    frame.src = 'about:blank';
                } else {
                    frame.src = src;
                    frame.classList.remove('hidden');
                    img.classList.add('hidden');
                    img.removeAttribute('src');
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        modal.classList.remove('opacity-0');
                        modal.classList.add('opacity-100');
                        panel.classList.remove('translate-y-full');
                        panel.classList.add('translate-y-0');
                    });
                });
            }

            function closeModal() {
                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0');
                panel.classList.remove('translate-y-0');
                panel.classList.add('translate-y-full');

                // 250ms must match panel's duration-[250ms] class (line 77) to ensure cleanup after transition completes
                closeTimer = setTimeout(function () {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    img.removeAttribute('src');
                    frame.src = 'about:blank';
                }, 250);
            }

            document.querySelectorAll('.file-preview-trigger').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openModal(btn.dataset.previewType, btn.dataset.previewSrc, btn.dataset.previewName);
                });
            });

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
    </script>
@endsection
