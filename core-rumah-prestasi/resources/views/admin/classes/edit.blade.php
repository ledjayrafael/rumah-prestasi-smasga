@extends('layouts.desktop')

@section('title', 'Ubah Kelas')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center gap-3.5 px-8 shrink-0">
        <a href="{{ route('admin.classes.index') }}" class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4B4A66" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div class="text-lg font-extrabold text-navy-900">Ubah Kelas — {{ $class->name }}</div>
    </div>

    <div class="flex-1 p-8">
        <x-flash />
        <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-2xl">
            <form method="POST" action="{{ route('admin.classes.update', $class) }}">
                @include('admin.classes._form')
            </form>
        </div>
    </div>
@endsection
