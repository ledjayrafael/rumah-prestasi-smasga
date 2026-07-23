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
        <select name="grade_level" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            @foreach (['X', 'XI', 'XII'] as $level)
                <option value="{{ $level }}" @selected(old('grade_level', $class->grade_level ?? '') === $level)>Kelas {{ $level }}</option>
            @endforeach
        </select>
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
        <select name="homeroom_teacher_id" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            <option value="">— Belum ditentukan —</option>
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id ?? '') == $teacher->id)>{{ $teacher->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<button type="submit" class="mt-6 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl">
    {{ isset($class) ? 'Simpan Perubahan' : 'Tambah Kelas' }}
</button>
