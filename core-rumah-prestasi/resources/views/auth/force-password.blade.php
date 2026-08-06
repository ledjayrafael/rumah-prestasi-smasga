@extends('layouts.guest')

@section('title', 'Ganti Kata Sandi')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-lg font-extrabold text-navy-900 mb-1">Ganti Kata Sandi</h1>
        <p class="text-sm text-slate-500 mb-5">Ini login pertama Anda. Buat kata sandi baru sebelum melanjutkan.</p>

        <x-flash />

        <form method="POST" action="{{ route('password.force.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="password" class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Baru</label>
                <input id="password" name="password" type="password" required minlength="8"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-600 mb-1.5">Ulangi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-navy-800 text-white text-sm font-bold py-3 shadow-lg shadow-navy-800/25 hover:bg-navy-700 transition">
                Simpan &amp; Lanjutkan
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3" data-confirm-logout>
            @csrf
            <button type="submit" class="w-full text-center text-xs font-semibold text-slate-400 hover:text-red-600">Keluar</button>
        </form>
    </div>

    <x-logout-confirm-modal />
@endsection
