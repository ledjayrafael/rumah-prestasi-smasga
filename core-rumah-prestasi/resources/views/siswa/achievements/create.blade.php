@extends('layouts.siswa')

@section('title', 'Upload Prestasi')

@section('content')
    <div class="bg-navy-800 text-white px-5 py-4 flex items-center gap-3.5">
        <a href="{{ route('siswa.dashboard') }}" class="w-9.5 h-9.5 rounded-xl bg-white/15 flex items-center justify-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <div class="text-base font-extrabold">Upload Prestasi</div>
            <div class="text-xs opacity-70">Lengkapi data dan unggah bukti</div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <x-flash />
    </div>

    @include('siswa.achievements._form', [
        'achievement' => null,
        'action' => route('siswa.achievements.store'),
        'submitLabel' => 'Kirim untuk Diverifikasi',
    ])
@endsection
