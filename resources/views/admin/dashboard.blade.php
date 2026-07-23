@extends('layouts.desktop')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Dashboard Admin</div>
    </div>

    <div class="flex-1 p-8">
        <x-flash />

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Total Prestasi</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['total_achievements'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Menunggu Verifikasi</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Total Siswa</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['total_students'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Total Kelas</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['total_classes'] }}</div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('admin.classes.index') }}" class="bg-white border border-slate-200 text-navy-900 text-sm font-bold px-5 py-3 rounded-xl">Kelola Kelas</a>
            <a href="{{ route('admin.teachers.index') }}" class="bg-white border border-slate-200 text-navy-900 text-sm font-bold px-5 py-3 rounded-xl">Kelola Guru</a>
            <a href="{{ route('admin.students.index') }}" class="bg-white border border-slate-200 text-navy-900 text-sm font-bold px-5 py-3 rounded-xl">Kelola Siswa</a>
        </div>
    </div>
@endsection
