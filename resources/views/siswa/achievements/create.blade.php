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

    <form method="POST" action="{{ route('siswa.achievements.store') }}" enctype="multipart/form-data" class="px-4 flex flex-col gap-3.5 mt-1">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Prestasi</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Kategori</label>
            <div class="flex gap-2">
                @foreach (\App\Enums\AchievementCategory::cases() as $option)
                    <label class="flex-1 text-center rounded-xl border border-slate-300 py-2 text-xs font-semibold cursor-pointer has-[:checked]:bg-navy-800 has-[:checked]:text-white has-[:checked]:border-navy-800">
                        <input type="radio" name="category" value="{{ $option->value }}" class="sr-only" {{ old('category') === $option->value ? 'checked' : '' }} required>
                        {{ $option->label() }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Juara / Penghargaan</label>
                <input type="text" name="rank_label" value="{{ old('rank_label') }}" placeholder="mis. Juara 2" required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis</label>
                <select name="participation_type" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                    @foreach (\App\Enums\ParticipationType::cases() as $option)
                        <option value="{{ $option->value }}" @selected(old('participation_type') === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tingkat</label>
                <select name="level" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                    @foreach (\App\Enums\AchievementLevel::cases() as $option)
                        <option value="{{ $option->value }}" @selected(old('level') === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal</label>
                <input type="date" name="event_date" value="{{ old('event_date') }}" required max="{{ now()->toDateString() }}"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Penyelenggara</label>
            <input type="text" name="organizer" value="{{ old('organizer') }}" required
                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1.5">Deskripsi (opsional)</label>
            <textarea name="description" rows="3"
                      class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">{{ old('description') }}</textarea>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-bold text-slate-600">Bukti Prestasi</label>
                <span class="text-[11px] font-semibold text-slate-400">Foto atau PDF &middot; maks 10MB, hingga 5 berkas</span>
            </div>
            <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf" required
                   class="w-full rounded-xl border border-dashed border-slate-300 px-3.5 py-4 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-navy-800 file:text-white file:px-3 file:py-1.5 file:text-xs file:font-bold">
        </div>

        <button type="submit"
                class="mt-2 h-13 rounded-2xl bg-navy-800 text-white text-sm font-bold shadow-lg shadow-navy-800/30 py-3.5">
            Kirim untuk Diverifikasi
        </button>
    </form>
@endsection
