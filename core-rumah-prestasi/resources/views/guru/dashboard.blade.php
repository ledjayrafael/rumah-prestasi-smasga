@extends('layouts.desktop')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Dashboard</div>
    </div>

    <div class="flex-1 p-8">
        <x-flash />

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Menunggu Verifikasi</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Disetujui (bln ini)</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['approved_this_month'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Perlu Revisi</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['revision'] }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500">Siswa Binaan</div>
                <div class="text-2xl font-extrabold text-navy-900 mt-2">{{ $stats['active_students'] }}</div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('guru.verification.index') }}" class="inline-flex items-center gap-2 bg-navy-800 text-white text-sm font-bold px-5 py-3 rounded-xl">
                Buka Antrean Verifikasi
            </a>
        </div>

        <div class="mt-6">
            <div class="text-sm font-extrabold text-navy-900 mb-3">Statistik Kelas</div>

            @if ($chartData['coverage']['total_students'] === 0)
                <div class="bg-white border border-slate-200 rounded-2xl p-4.5 text-sm text-slate-500">
                    Belum ada siswa binaan.
                </div>
            @else
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                        <div class="text-xs font-semibold text-slate-500 mb-3">Cakupan Siswa Berprestasi</div>
                        <div class="h-56">
                            <canvas id="coverage-chart"></canvas>
                        </div>
                        <div class="text-xs text-slate-500 mt-3 text-center">
                            <span class="font-extrabold text-navy-900">{{ $chartData['coverage']['with_approved'] }}</span>
                            dari
                            <span class="font-extrabold text-navy-900">{{ $chartData['coverage']['total_students'] }}</span>
                            siswa punya prestasi disetujui
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                        <div class="text-xs font-semibold text-slate-500 mb-3">Prestasi Disetujui per Kategori</div>
                        <div class="h-56">
                            <canvas id="category-chart"></canvas>
                        </div>
                    </div>
                </div>

                <script type="application/json" id="guru-dashboard-chart-data">@json($chartData)</script>
            @endif
        </div>

        @if ($notifications->isNotEmpty())
            <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500 mb-3">Prestasi Baru Diajukan</div>
                <div class="divide-y divide-slate-100">
                    @foreach ($notifications as $notification)
                        <a href="{{ route('guru.notifications.read', $notification->id) }}"
                           class="flex items-center justify-between py-3 first:pt-0 last:pb-0 hover:bg-slate-50 -mx-2 px-2 rounded-lg">
                            <div class="text-sm text-navy-900">
                                <span class="font-bold">{{ $notification->data['student_name'] }}</span>
                                mengajukan prestasi <span class="font-semibold">{{ $notification->data['title'] }}</span>
                            </div>
                            <div class="text-xs text-slate-400 shrink-0 ml-4">{{ $notification->created_at->diffForHumans() }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        @vite('resources/js/guru-dashboard-charts.js')
    @endpush
@endsection
