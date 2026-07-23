@extends('layouts.siswa')

@section('title', 'Profil')

@section('content')
    <div class="bg-navy-800 text-white px-6 pt-6 pb-7 rounded-b-[28px] relative overflow-hidden">
        <div class="absolute -right-10 -top-12 w-44 h-44 rounded-full bg-white/5"></div>
        <div class="text-lg font-extrabold relative">Profil</div>
        <div class="flex items-center gap-3.5 mt-4 relative">
            <div class="w-16 h-16 rounded-full bg-gold-500 text-[#3a2a00] flex items-center justify-center text-xl font-extrabold border-2 border-white/40 shrink-0">
                {{ Str::of($user->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
            </div>
            <div>
                <div class="text-base font-extrabold">{{ $user->name }}</div>
                <div class="text-xs opacity-75 mt-0.5">{{ optional($user->studentProfile->schoolClass)->name ?? '—' }} &middot; NIS {{ $user->studentProfile->nis }}</div>
            </div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <x-flash />
    </div>

    <div class="px-4 mt-1">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2 px-1">Data Diri</div>
        <form method="POST" action="{{ route('siswa.profile.update') }}" class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
            <button type="submit" class="mt-1 rounded-xl bg-navy-800 text-white text-sm font-bold py-2.5">Simpan Data Diri</button>
        </form>
    </div>

    <div class="px-4 mt-5">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2 px-1">Ganti Kata Sandi</div>
        <form method="POST" action="{{ route('siswa.profile.password') }}" class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Baru</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Ulangi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" required minlength="8"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
            <button type="submit" class="mt-1 rounded-xl bg-navy-800 text-white text-sm font-bold py-2.5">Ganti Kata Sandi</button>
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="px-4 mt-5">
        @csrf
        <button type="submit" class="w-full bg-red-50 border border-red-200 text-red-600 text-sm font-bold rounded-2xl py-3">Keluar</button>
    </form>

    <div class="text-center text-[11px] text-slate-400 font-semibold mt-4">Portal Prestasi SMAN 1 Tenggarang &middot; v1.0</div>
@endsection
