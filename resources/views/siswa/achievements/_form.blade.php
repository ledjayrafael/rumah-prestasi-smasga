@php
    /** @var \App\Models\Achievement|null $achievement */
    $achievement = $achievement ?? null;
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="px-4 flex flex-col gap-3.5 mt-1">
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

    <button type="submit"
            class="mt-2 h-13 rounded-2xl bg-navy-800 text-white text-sm font-bold shadow-lg shadow-navy-800/30 py-3.5">
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
</script>
