@csrf
@if(isset($competition)) @method('PUT') @endif

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lomba</label>
    <input type="text" name="title" value="{{ old('title', $competition->title ?? '') }}" required
           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
</div>

<div class="grid grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kategori</label>
        <select name="category" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            @foreach (\App\Enums\AchievementCategory::cases() as $option)
                <option value="{{ $option->value }}" @selected(old('category', $competition->category->value ?? '') === $option->value)>{{ $option->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tingkat</label>
        <select name="level" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            @foreach (\App\Enums\AchievementLevel::cases() as $option)
                <option value="{{ $option->value }}" @selected(old('level', $competition->level->value ?? '') === $option->value)>{{ $option->label() }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Penyelenggara</label>
        <input type="text" name="organizer" value="{{ old('organizer', $competition->organizer ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tutup Pendaftaran</label>
        <input type="date" name="registration_deadline" value="{{ old('registration_deadline', isset($competition) ? $competition->registration_deadline->toDateString() : '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Link Pendaftaran (opsional)</label>
    <input type="url" name="registration_url" value="{{ old('registration_url', $competition->registration_url ?? '') }}" placeholder="https://..."
           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Deskripsi (opsional)</label>
    <textarea name="description" rows="3"
              class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">{{ old('description', $competition->description ?? '') }}</textarea>
</div>

<button type="submit" class="mt-6 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl">
    {{ isset($competition) ? 'Simpan Perubahan' : 'Tambah Lomba' }}
</button>
