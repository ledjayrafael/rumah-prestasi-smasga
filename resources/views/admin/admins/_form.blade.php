@csrf
@if(isset($admin)) @method('PUT') @endif

<div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
    <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" required
           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
</div>

<div class="mt-4">
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Email (dipakai untuk login)</label>
    <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required
           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
</div>

@if(isset($admin))
    <label class="flex items-center gap-2 mt-4 text-sm font-semibold text-slate-600">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
        Akun aktif
    </label>
@endif

<button type="submit" class="mt-6 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl">
    {{ isset($admin) ? 'Simpan Perubahan' : 'Tambah Admin' }}
</button>
