@php
    /** @var \App\Models\Achievement|null $achievement */
    $achievement = $achievement ?? null;
    $progressTitle = $achievement ? 'Menyimpan perbaikan…' : 'Mengirim prestasi…';
@endphp

<div id="achievement-progress-toast" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-[60] w-[calc(100%-2rem)] max-w-md rounded-2xl bg-white border border-slate-200 shadow-xl px-4 py-3.5" role="status" aria-live="polite">
    <div id="achievement-progress-title" class="text-sm font-extrabold text-navy-900 mb-2">{{ $progressTitle }}</div>
    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mb-1">
        <span id="achievement-progress-label">Memproses…</span>
        <span id="achievement-progress-text" class="font-mono tabular-nums">0%</span>
    </div>
    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
        <div id="achievement-progress-bar" class="h-full bg-gradient-to-r from-gold-600 to-gold-400 transition-[width] duration-150 ease-out" style="width:0%"></div>
    </div>
</div>

<div id="achievement-error-toast" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-[60] w-[calc(100%-2rem)] max-w-md rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3 shadow-lg" role="alert"></div>

<div id="achievement-form-errors" class="hidden mx-4 mb-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3">
    <ul class="list-disc list-inside space-y-1"></ul>
</div>

<form id="achievement-submit-form" method="POST" action="{{ $action }}" enctype="multipart/form-data" class="px-4 flex flex-col gap-3.5 mt-1">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    @if ($achievement && $achievement->reviewer_notes)
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl p-3.5">
            <div class="font-bold text-xs mb-1">Catatan revisi dari guru:</div>
            {{ $achievement->reviewer_notes }}
        </div>
    @endif

    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Prestasi</label>
        <input type="text" name="title" value="{{ old('title', $achievement?->title) }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kategori</label>
        <div class="flex gap-2">
            @foreach (\App\Enums\AchievementCategory::cases() as $option)
                @php $selectedCategory = old('category', $achievement?->category?->value); @endphp
                <label class="flex-1 text-center rounded-xl border border-slate-300 py-2 text-xs font-semibold cursor-pointer has-[:checked]:bg-navy-800 has-[:checked]:text-white has-[:checked]:border-navy-800">
                    <input type="radio" name="category" value="{{ $option->value }}" class="sr-only" {{ $selectedCategory === $option->value ? 'checked' : '' }} required>
                    {{ $option->label() }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex gap-3">
        <div class="flex-1">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Juara / Penghargaan</label>
            <input type="text" name="rank_label" value="{{ old('rank_label', $achievement?->rank_label) }}" placeholder="mis. Juara 2" required
                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis</label>
            <div class="relative">
                @php $selectedParticipation = old('participation_type', $achievement?->participation_type?->value); @endphp
                <select name="participation_type" required
                        class="w-full appearance-none rounded-xl border border-slate-300 pl-3.5 pr-8 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                    @foreach (\App\Enums\ParticipationType::cases() as $option)
                        <option value="{{ $option->value }}" @selected($selectedParticipation === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <div class="flex-1">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Tingkat</label>
            <div class="relative">
                @php $selectedLevel = old('level', $achievement?->level?->value); @endphp
                <select name="level" required
                        class="w-full appearance-none rounded-xl border border-slate-300 pl-3.5 pr-8 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                    @foreach (\App\Enums\AchievementLevel::cases() as $option)
                        <option value="{{ $option->value }}" @selected($selectedLevel === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </div>
        <div class="flex-1">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal</label>
            <input type="date" name="event_date" value="{{ old('event_date', $achievement?->event_date?->toDateString()) }}" required max="{{ now()->toDateString() }}"
                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Penyelenggara</label>
        <input type="text" name="organizer" value="{{ old('organizer', $achievement?->organizer) }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Deskripsi (opsional)</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">{{ old('description', $achievement?->description) }}</textarea>
    </div>

    @if ($achievement && $achievement->files->isNotEmpty())
        <div>
            <div class="text-xs font-bold text-slate-600 mb-2">Bukti saat ini (tetap dipakai jika tidak mengunggah baru)</div>
            <div class="flex flex-col gap-2">
                @foreach ($achievement->files as $file)
                    <div class="bg-white border border-slate-200 rounded-xl p-3 text-sm font-semibold text-navy-900 truncate">{{ $file->original_name }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="text-xs font-bold text-slate-600">Foto Prestasi</label>
            <span class="text-[11px] font-semibold text-slate-400">Opsional &middot; maks 3 foto</span>
        </div>
        <div class="grid grid-cols-3 gap-3">
            @for ($i = 1; $i <= 3; $i++)
                <div class="photo-slot relative aspect-square rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 overflow-hidden cursor-pointer transition-colors hover:border-navy-400">
                    <input type="file" name="files[]" accept="image/jpeg,image/png,image/jpg" class="photo-slot-input hidden">
                    <div class="photo-slot-empty absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                        <span class="text-[10px] font-bold">Foto {{ $i }}</span>
                    </div>
                    <img class="photo-slot-preview hidden absolute inset-0 w-full h-full object-cover" alt="Pratinjau foto {{ $i }}">
                    <button type="button" class="photo-slot-remove hidden absolute top-1.5 right-1.5 w-5.5 h-5.5 rounded-full bg-black/60 text-white flex items-center justify-center" aria-label="Hapus foto {{ $i }}">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            @endfor
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="text-xs font-bold text-slate-600">Bukti Prestasi</label>
            <span class="text-[11px] font-semibold text-slate-400">
                Foto atau PDF &middot; maks 10MB, hingga 5 berkas
                @if ($achievement)
                    &middot; kosongkan jika bukti lama masih dipakai
                @endif
            </span>
        </div>
        <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf"
               class="w-full rounded-xl border border-dashed border-slate-300 px-3.5 py-4 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-navy-800 file:text-white file:px-3 file:py-1.5 file:text-xs file:font-bold">
        @error('files')
            <p class="text-xs text-red-600 font-medium mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" id="achievement-submit-btn"
            class="mt-2 h-13 rounded-2xl bg-navy-800 text-white text-sm font-bold shadow-lg shadow-navy-800/30 py-3.5 disabled:opacity-60 disabled:pointer-events-none">
        {{ $submitLabel }}
    </button>
</form>

<script>
    document.querySelectorAll('.photo-slot').forEach(function (slot) {
        const input = slot.querySelector('.photo-slot-input');
        const empty = slot.querySelector('.photo-slot-empty');
        const preview = slot.querySelector('.photo-slot-preview');
        const removeBtn = slot.querySelector('.photo-slot-remove');

        slot.addEventListener('click', function () {
            input.click();
        });

        input.addEventListener('change', function () {
            const file = input.files[0];
            if (!file) return;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            empty.classList.add('hidden');
            removeBtn.classList.remove('hidden');
        });

        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            input.value = '';
            preview.removeAttribute('src');
            preview.classList.add('hidden');
            empty.classList.remove('hidden');
            removeBtn.classList.add('hidden');
        });
    });

    (function () {
        const form = document.getElementById('achievement-submit-form');
        const submitBtn = document.getElementById('achievement-submit-btn');
        const progressToast = document.getElementById('achievement-progress-toast');
        const progressBar = document.getElementById('achievement-progress-bar');
        const progressText = document.getElementById('achievement-progress-text');
        const progressLabel = document.getElementById('achievement-progress-label');
        const errorToast = document.getElementById('achievement-error-toast');
        const formErrors = document.getElementById('achievement-form-errors');
        const formErrorsList = formErrors.querySelector('ul');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const GIMMICK_MAX = 30;
        const REAL_RANGE = 70;
        const GIMMICK_DURATION_MS = 1200;

        let gimmickRaf = null;
        let fallbackInterval = null;
        let displayedProgress = 0;
        let gimmickProgress = 0;
        let uploadProgress = 0;

        function hideProgressToast() {
            progressToast.classList.add('hidden');
            if (gimmickRaf) {
                cancelAnimationFrame(gimmickRaf);
                gimmickRaf = null;
            }
            if (fallbackInterval) {
                clearInterval(fallbackInterval);
                fallbackInterval = null;
            }
        }

        function showErrorToast(message) {
            errorToast.textContent = message;
            errorToast.classList.remove('hidden');
            setTimeout(function () {
                errorToast.classList.add('hidden');
            }, 5000);
        }

        function clearFormErrors() {
            formErrorsList.innerHTML = '';
            formErrors.classList.add('hidden');
        }

        function showFormErrors(errors) {
            clearFormErrors();
            const messages = [];
            if (errors && typeof errors === 'object') {
                Object.values(errors).forEach(function (fieldMessages) {
                    (fieldMessages || []).forEach(function (msg) {
                        messages.push(msg);
                    });
                });
            }
            if (errors && typeof errors === 'string') {
                messages.push(errors);
            }
            if (messages.length === 0) {
                messages.push('Data tidak valid. Periksa kembali formulir.');
            }
            messages.forEach(function (msg) {
                const li = document.createElement('li');
                li.textContent = msg;
                formErrorsList.appendChild(li);
            });
            formErrors.classList.remove('hidden');
            formErrors.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function renderProgress() {
            let next;
            if (uploadProgress > 0) {
                next = Math.max(gimmickProgress, GIMMICK_MAX + uploadProgress);
            } else {
                next = gimmickProgress;
            }
            displayedProgress = Math.min(99, Math.max(displayedProgress, next));
            progressBar.style.width = displayedProgress + '%';
            progressText.textContent = Math.round(displayedProgress) + '%';
        }

        function setCompleteProgress() {
            displayedProgress = 100;
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            progressLabel.textContent = 'Selesai';
        }

        function startGimmickProgress() {
            const start = performance.now();
            function tick(now) {
                const elapsed = now - start;
                const t = Math.min(1, elapsed / GIMMICK_DURATION_MS);
                const eased = 1 - Math.pow(1 - t, 3);
                gimmickProgress = eased * GIMMICK_MAX;
                renderProgress();
                if (t < 1) {
                    gimmickRaf = requestAnimationFrame(tick);
                }
            }
            gimmickRaf = requestAnimationFrame(tick);
        }

        function startFallbackProgress() {
            fallbackInterval = setInterval(function () {
                if (uploadProgress > 0) {
                    return;
                }
                uploadProgress = Math.min(REAL_RANGE - 5, uploadProgress + 1.5);
                renderProgress();
            }, 200);
        }

        function finishSubmitError(message, validationErrors) {
            hideProgressToast();
            submitBtn.disabled = false;
            if (validationErrors) {
                showFormErrors(validationErrors);
            } else if (message) {
                showErrorToast(message);
            }
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearFormErrors();
            errorToast.classList.add('hidden');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            displayedProgress = 0;
            gimmickProgress = 0;
            uploadProgress = 0;
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            progressLabel.textContent = 'Memproses…';
            progressToast.classList.remove('hidden');
            submitBtn.disabled = true;

            startGimmickProgress();
            startFallbackProgress();

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action);
            xhr.timeout = 300000;

            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.addEventListener('progress', function (e) {
                if (!e.lengthComputable) {
                    return;
                }
                uploadProgress = (e.loaded / e.total) * REAL_RANGE;
                renderProgress();
            });

            xhr.addEventListener('load', function () {
                if (fallbackInterval) {
                    clearInterval(fallbackInterval);
                    fallbackInterval = null;
                }

                if (xhr.status === 422) {
                    let payload = {};
                    try {
                        payload = JSON.parse(xhr.responseText);
                    } catch (e) {}
                    finishSubmitError(null, payload.errors || payload.message);
                    return;
                }

                if (xhr.status === 419) {
                    finishSubmitError('Sesi habis. Muat ulang halaman lalu coba lagi.');
                    return;
                }

                if (xhr.status >= 200 && xhr.status < 300) {
                    setCompleteProgress();
                    window.location.href = xhr.responseURL || form.action;
                    return;
                }

                let message = 'Gagal mengirim prestasi. Coba lagi.';
                try {
                    const payload = JSON.parse(xhr.responseText);
                    if (payload.message) {
                        message = payload.message;
                    }
                } catch (e) {}
                finishSubmitError(message);
            });

            xhr.addEventListener('error', function () {
                finishSubmitError('Koneksi gagal. Periksa jaringan Anda dan coba lagi.');
            });

            xhr.addEventListener('timeout', function () {
                finishSubmitError('Waktu habis. Periksa koneksi atau ukuran berkas, lalu coba lagi.');
            });

            xhr.addEventListener('abort', function () {
                finishSubmitError('Pengiriman dibatalkan.');
            });

            try {
                xhr.send(formData);
            } catch (e) {
                finishSubmitError('Terjadi kesalahan saat mengirim data. Coba lagi.');
            }
        });
    })();
</script>
