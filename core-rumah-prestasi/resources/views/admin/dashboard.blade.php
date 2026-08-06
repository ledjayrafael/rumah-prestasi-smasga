@extends('layouts.desktop')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $kpiMeta = [
            'total_achievements' => ['label' => 'Total Prestasi', 'suffix' => ''],
            'pending' => ['label' => 'Menunggu Verifikasi', 'suffix' => ''],
            'approved_this_month' => ['label' => 'Disetujui Bulan Ini', 'suffix' => ''],
            'total_students' => ['label' => 'Total Siswa', 'suffix' => ''],
            'total_classes' => ['label' => 'Total Kelas', 'suffix' => ''],
            'coverage_pct' => ['label' => 'Cakupan Berprestasi', 'suffix' => '%'],
        ];
    @endphp

    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
        <div>
            <div class="text-lg font-extrabold text-navy-900">{{ $dashboard['header']['greeting'] }}, {{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $dashboard['header']['date_label'] }}</div>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-full pl-2.5 pr-3.5 py-1.5">
            <span class="relative flex h-2 w-2">
                @if ($dashboard['header']['pending_today'] > 0)
                    <span class="motion-reduce:hidden animate-ping absolute inline-flex h-full w-full rounded-full bg-gold-500 opacity-75"></span>
                @endif
                <span class="relative inline-flex rounded-full h-2 w-2 {{ $dashboard['header']['pending_today'] > 0 ? 'bg-gold-500' : 'bg-emerald-500' }}"></span>
            </span>
            <span class="text-xs font-bold text-navy-900">
                {{ $dashboard['header']['pending_today'] }} menunggu verifikasi
            </span>
        </div>
    </div>

    <div class="flex-1 p-8">
        <x-flash />

        <div class="grid grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($kpiMeta as $key => $meta)
                @php $kpi = $dashboard['kpis'][$key]; @endphp
                <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                    <div class="text-xs font-semibold text-slate-500">{{ $meta['label'] }}</div>
                    <div class="flex items-end justify-between mt-2 gap-2">
                        <div class="text-2xl font-extrabold font-mono text-navy-900">{{ $kpi['value'] }}{{ $meta['suffix'] }}</div>
                        <div class="text-[11px] font-mono font-bold text-slate-400 pb-0.5">
                            {{ $kpi['delta'] > 0 ? '+' : ($kpi['delta'] < 0 ? '-' : '±') }}{{ abs($kpi['delta']) }}{{ $meta['suffix'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <div class="text-sm font-extrabold text-navy-900 mb-3">Tren &amp; Distribusi Prestasi</div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                    <div class="text-xs font-semibold text-slate-500 mb-3">Tren Disetujui (6 Bulan Terakhir)</div>
                    <div class="h-56">
                        <canvas id="trend-chart"></canvas>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                    <div class="text-xs font-semibold text-slate-500 mb-3">Cakupan Siswa Berprestasi</div>
                    @if ($dashboard['charts']['coverage']['total_students'] === 0)
                        <div class="h-56 flex items-center justify-center text-sm text-slate-400">Belum ada siswa.</div>
                    @else
                        <div class="h-56">
                            <canvas id="coverage-chart"></canvas>
                        </div>
                        <div class="text-xs text-slate-500 mt-3 text-center">
                            <span class="font-extrabold font-mono text-navy-900">{{ $dashboard['charts']['coverage']['with_approved'] }}</span>
                            dari
                            <span class="font-extrabold font-mono text-navy-900">{{ $dashboard['charts']['coverage']['total_students'] }}</span>
                            siswa punya prestasi disetujui
                        </div>
                    @endif
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                    <div class="text-xs font-semibold text-slate-500 mb-3">Prestasi Disetujui per Kategori</div>
                    <div class="h-56">
                        <canvas id="category-chart"></canvas>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                    <div class="text-xs font-semibold text-slate-500 mb-3">Prestasi Disetujui per Jenjang</div>
                    <div class="h-56">
                        <canvas id="level-chart"></canvas>
                    </div>
                </div>
            </div>

            <script type="application/json" id="admin-dashboard-chart-data">@json($dashboard['charts'])</script>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 items-start">
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500 mb-3">Perlu Perhatian</div>
                @if (empty($dashboard['attention']))
                    <div class="text-sm text-slate-400">Tidak ada yang perlu perhatian khusus saat ini.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($dashboard['attention'] as $item)
                            <div class="flex items-start justify-between gap-3 border border-gold-400/40 bg-gold-100/50 rounded-xl px-3.5 py-2.5">
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-navy-900">{{ $item['label'] }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $item['hint'] }}</div>
                                </div>
                                <div class="text-lg font-extrabold font-mono text-gold-600 shrink-0">{{ $item['count'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500 mb-3">Aktivitas Terbaru</div>
                @if (empty($dashboard['activity']))
                    <div class="text-sm text-slate-400">Belum ada aktivitas prestasi.</div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($dashboard['activity'] as $item)
                            <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0 gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm text-navy-900 truncate">
                                        <span class="font-bold">{{ $item['student_name'] }}</span>
                                        — {{ $item['title'] }}
                                    </div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $item['created_at_human'] }}</div>
                                </div>
                                <span @class([
                                    'text-[11px] font-bold px-2 py-1 rounded-full shrink-0',
                                    'bg-amber-100 text-amber-700' => $item['status'] === 'pending',
                                    'bg-emerald-100 text-emerald-700' => $item['status'] === 'approved',
                                    'bg-red-100 text-red-700' => $item['status'] === 'revision',
                                ])>{{ $item['status_label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 items-start">
            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500 mb-3">Highlight Prestasi</div>
                @if (empty($dashboard['highlights']))
                    <div class="text-sm text-slate-400">Belum ada prestasi disetujui.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($dashboard['highlights'] as $item)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-navy-800 text-gold-400 flex items-center justify-center text-xs font-mono font-extrabold shrink-0">
                                    {{ mb_substr($item['level'], 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-bold text-navy-900 truncate">{{ $item['title'] }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 truncate">
                                        {{ $item['student_name'] }} · {{ $item['rank_label'] }} · {{ $item['level'] }}
                                    </div>
                                </div>
                                <div class="text-xs text-slate-400 font-mono shrink-0">{{ $item['event_date'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4.5">
                <div class="text-xs font-semibold text-slate-500 mb-3">Leaderboard Siswa (Bulan Ini)</div>
                @if (empty($dashboard['leaderboard']))
                    <div class="text-sm text-slate-400">Belum ada prestasi disetujui bulan ini.</div>
                @else
                    <div class="space-y-2.5">
                        @foreach ($dashboard['leaderboard'] as $i => $item)
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-gold-100 text-gold-600 font-mono text-xs font-extrabold flex items-center justify-center shrink-0">{{ $i + 1 }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-bold text-navy-900 truncate">{{ $item['name'] }}</div>
                                    <div class="text-xs text-slate-400">{{ $item['class_name'] }}</div>
                                </div>
                                <div class="text-sm font-extrabold font-mono text-navy-900 shrink-0">{{ $item['count'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-4.5">
            <div class="text-xs font-semibold text-slate-500 mb-3">Lomba Terbanyak Diikuti</div>
            @if (empty($dashboard['top_competitions']))
                <div class="text-sm text-slate-400">Belum ada data lomba.</div>
            @else
                @php $maxCompetitionCount = collect($dashboard['top_competitions'])->max('count') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach ($dashboard['top_competitions'] as $i => $item)
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-navy-100 text-navy-700 font-mono text-xs font-extrabold flex items-center justify-center shrink-0">{{ $i + 1 }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-bold text-navy-900 truncate">{{ $item['name'] }}</div>
                                    <div class="text-sm font-extrabold font-mono text-navy-900 shrink-0">{{ $item['count'] }}</div>
                                </div>
                                <div class="h-1.5 bg-slate-100 rounded-full mt-1.5 overflow-hidden">
                                    <div class="h-full bg-gold-500 rounded-full" style="width: {{ round($item['count'] / $maxCompetitionCount * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-6 flex gap-3">
            @foreach ($dashboard['actions'] as $action)
                <a href="{{ $action['url'] }}" class="inline-flex items-center gap-2 bg-navy-800 text-white text-sm font-bold px-5 py-3 rounded-xl">
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin-dashboard-charts.js')
    @endpush
@endsection
