@extends('layouts.desktop')

@section('title', 'Kredensial Akun Guru')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Kredensial Akun Baru</div>
    </div>

    <div class="flex-1 p-8 overflow-auto max-w-lg">
        @if ($credential)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-900">
                <p class="font-bold mb-3">Salin sekarang — halaman ini hanya tersedia beberapa menit.</p>
                <dl class="space-y-2">
                    <div><dt class="text-xs font-bold text-amber-800/70">Nama</dt><dd class="font-semibold">{{ $credential['name'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Login (email)</dt><dd class="font-mono font-bold">{{ $credential['login'] }}</dd></div>
                    <div><dt class="text-xs font-bold text-amber-800/70">Password sementara</dt><dd class="font-mono font-bold">{{ $credential['password'] }}</dd></div>
                </dl>
            </div>
            <p class="text-xs text-slate-500 mt-4">Guru wajib ganti password saat login pertama.</p>
        @else
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-sm text-slate-600">
                Kredensial tidak lagi tersedia. Buat ulang password lewat edit akun atau hubungi admin sistem.
            </div>
        @endif

        <a href="{{ route('admin.teachers.index') }}" class="inline-block mt-6 text-sm font-bold text-navy-800">← Kembali ke daftar guru</a>
    </div>
@endsection
