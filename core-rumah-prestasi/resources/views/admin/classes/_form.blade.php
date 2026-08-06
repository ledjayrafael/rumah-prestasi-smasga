@csrf
@if(isset($class)) @method('PUT') @endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Kelas</label>
        <input type="text" name="name" value="{{ old('name', $class->name ?? '') }}" placeholder="mis. XI MIPA 2" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tingkat</label>
        <div class="relative">
            <select name="grade_level" required class="w-full appearance-none rounded-xl border border-slate-300 pl-3.5 pr-8 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                @foreach (['X', 'XI', 'XII'] as $level)
                    <option value="{{ $level }}" @selected(old('grade_level', $class->grade_level ?? '') === $level)>Kelas {{ $level }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jurusan (opsional)</label>
        <input type="text" name="major" value="{{ old('major', $class->major ?? '') }}" placeholder="mis. MIPA / IPS"
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Wali Kelas (opsional)</label>
        <div class="relative">
            <select name="homeroom_teacher_id" class="w-full appearance-none rounded-xl border border-slate-300 pl-3.5 pr-8 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                <option value="">— Belum ditentukan —</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id ?? '') == $teacher->id)>{{ $teacher->name }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </div>
    </div>
</div>

<button type="submit" class="mt-6 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl">
    {{ isset($class) ? 'Simpan Perubahan' : 'Tambah Kelas' }}
</button>
