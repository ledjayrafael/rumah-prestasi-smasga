@csrf
@if(isset($student)) @method('PUT') @endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">NIS (dipakai untuk login)</label>
        <input type="text" name="nis" value="{{ old('nis', $student->studentProfile->nis ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Kelas</label>
    <div class="relative">
        <select name="school_class_id" required class="w-full appearance-none rounded-xl border border-slate-300 pl-3.5 pr-8 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            <option value="">— Pilih kelas —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $student->studentProfile->school_class_id ?? '') == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </div>
</div>

@if(isset($student))
    <label class="flex items-center gap-2 mt-4 text-sm font-semibold text-slate-600">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
        Akun aktif
    </label>
@endif

<button type="submit" class="mt-6 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl">
    {{ isset($student) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
</button>
