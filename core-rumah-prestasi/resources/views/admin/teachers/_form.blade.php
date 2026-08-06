@csrf
@if(isset($teacher)) @method('PUT') @endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $teacher->name ?? '') }}" required
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nomor WhatsApp</label>
        <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}"
               inputmode="numeric" pattern="[0-9]*" maxlength="20"
               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
    </div>
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Email (dipakai untuk login)</label>
    <input type="email" name="email" value="{{ old('email', $teacher->email ?? '') }}" required
           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
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
