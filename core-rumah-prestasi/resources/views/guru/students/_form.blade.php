@csrf
@if(isset($student)) @method('PUT') @endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="name" class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
        <input type="text" name="name" id="name" value="{{ old('name', $student->name ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm transition-colors hover:border-slate-400 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20">
        @error('name')
            <p class="text-xs font-semibold text-red-600 mt-1.5" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="nis" class="block text-xs font-bold text-slate-600 mb-1.5">NIS (dipakai untuk login)</label>
        <input type="text" name="nis" id="nis" value="{{ old('nis', $student->studentProfile->nis ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono font-semibold transition-colors hover:border-slate-400 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20">
        @error('nis')
            <p class="text-xs font-semibold text-red-600 mt-1.5" role="alert">{{ $message }}</p>
        @else
            <p class="text-[11px] text-slate-400 mt-1.5">Siswa memakai NIS ini sebagai username saat masuk.</p>
        @enderror
    </div>
</div>

<div class="mt-4">
    <label for="school_class_id" class="block text-xs font-bold text-slate-600 mb-1.5">Kelas</label>
    <div class="relative">
        <select name="school_class_id" id="school_class_id" required
                class="w-full appearance-none rounded-xl border border-slate-300 pl-3.5 pr-8 py-2.5 text-sm transition-colors hover:border-slate-400 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20">
            <option value="">— Pilih kelas —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $student->studentProfile->school_class_id ?? '') == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
    </div>
    @error('school_class_id')
        <p class="text-xs font-semibold text-red-600 mt-1.5" role="alert">{{ $message }}</p>
    @enderror
</div>

@if(isset($student))
    <label class="flex items-start gap-2.5 mt-5 pt-5 border-t border-slate-100 cursor-pointer">
        <input type="checkbox" name="is_active" value="1"
               class="mt-0.5 rounded border-slate-300 text-navy-700 focus:ring-navy-700/40"
               {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
        <span>
            <span class="block text-sm font-bold text-navy-900">Akun aktif</span>
            <span class="block text-xs text-slate-400 mt-0.5">Nonaktifkan untuk mengunci login siswa tanpa menghapus data.</span>
        </span>
    </label>
@endif

<div class="mt-6 flex items-center gap-4">
    <button type="submit"
            class="bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-navy-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2">
        {{ isset($student) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
    </button>
    <a href="{{ route('guru.students.index') }}" class="text-sm font-bold text-slate-500 hover:text-navy-800 transition-colors">Batal</a>
</div>
