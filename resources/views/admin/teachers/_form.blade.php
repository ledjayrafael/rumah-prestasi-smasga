@csrf
@if(isset($teacher)) @method('PUT') @endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $teacher->name ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">No. Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}"
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Email (dipakai untuk login)</label>
    <input type="email" name="email" value="{{ old('email', $teacher->email ?? '') }}" required
           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
</div>

<div class="grid grid-cols-2 gap-4 mt-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Mata Pelajaran</label>
        <input type="text" name="subject" value="{{ old('subject', $teacher->teacherProfile->subject ?? '') }}"
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Peran</label>
        <select name="position" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            @foreach (\App\Enums\TeacherPosition::cases() as $option)
                <option value="{{ $option->value }}" @selected(old('position', $teacher->teacherProfile->position->value ?? '') === $option->value)>{{ $option->label() }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Kelas Binaan</label>
    <div class="flex flex-wrap gap-2">
        @php $selectedClasses = old('class_ids', isset($teacher) ? $teacher->taughtClasses->pluck('id')->all() : []); @endphp
        @foreach ($classes as $class)
            <label class="text-xs font-semibold px-3 py-1.5 rounded-full border border-slate-300 cursor-pointer has-[:checked]:bg-navy-800 has-[:checked]:text-white has-[:checked]:border-navy-800">
                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" class="sr-only" {{ in_array($class->id, $selectedClasses) ? 'checked' : '' }}>
                {{ $class->name }}
            </label>
        @endforeach
    </div>
</div>

@if(isset($teacher))
    <label class="flex items-center gap-2 mt-4 text-sm font-semibold text-slate-600">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
        Akun aktif
    </label>
@endif

<button type="submit" class="mt-6 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl">
    {{ isset($teacher) ? 'Simpan Perubahan' : 'Tambah Guru' }}
</button>
