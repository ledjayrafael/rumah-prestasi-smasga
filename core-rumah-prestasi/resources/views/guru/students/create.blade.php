@extends('layouts.desktop')

@section('title', 'Tambah Siswa')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center gap-3.5 px-8 shrink-0">
        <a href="{{ route('guru.students.index') }}"
           class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center hover:bg-slate-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4B4A66" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <div class="text-lg font-extrabold text-navy-900 tracking-tight">Tambah Akun Siswa</div>
            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Akun dibuat langsung aktif untuk kelas binaan Anda</p>
        </div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />
        <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-2xl">
            <form method="POST" action="{{ route('guru.students.store') }}">
                @include('guru.students._form')
            </form>
        </div>
    </div>
@endsection
