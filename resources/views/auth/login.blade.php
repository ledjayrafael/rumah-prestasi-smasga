@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-lg font-extrabold text-navy-900 mb-1">Masuk</h1>
        <p class="text-sm text-slate-500 mb-5">Gunakan NIS (siswa) atau email (guru/admin).</p>

        <x-flash />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-xs font-bold text-slate-600 mb-1.5">NIS / Email</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi</label>
                <input id="password" name="password" type="password" required
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-500">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Ingat saya
            </label>

            <button type="submit"
                class="w-full rounded-xl bg-navy-800 text-white text-sm font-bold py-3 shadow-lg shadow-navy-800/25 hover:bg-navy-700 transition">
                Masuk
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">Portal Prestasi SMAN 1 Tenggarang &middot; v1.0</p>
@endsection
