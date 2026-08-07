@extends('layouts.guest')

@section('title', 'Masuk')

@push('preloader')
    <style>
        @keyframes preloader-shimmer-sweep {
            0% { transform: translateX(-100%); opacity: 0; }
            20% { opacity: 1; }
            100% { transform: translateX(350%); opacity: 0; }
        }
        @keyframes preloader-fill {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        @keyframes preloader-fade-out {
            to { opacity: 0; }
        }
        @keyframes preloader-fade-in {
            to { opacity: 1; }
        }

        #preloader-shimmer {
            animation: preloader-shimmer-sweep .4s cubic-bezier(.4, 0, .2, 1) forwards;
        }
        #preloader-bar {
            transform: scaleX(0);
            animation: preloader-fill 2.6s cubic-bezier(.34, 1.56, .64, 1) .4s forwards;
        }
        #preloader-pending {
            animation: preloader-fade-out .3s ease forwards .4s;
        }
        #preloader-percent-label {
            animation: preloader-fade-in .3s ease forwards .4s;
        }

        @media (prefers-reduced-motion: reduce) {
            #preloader-shimmer, #preloader-bar, #preloader-pending, #preloader-percent-label {
                animation: none !important;
            }
            #preloader-bar { transform: scaleX(1) !important; }
            #preloader-pending { opacity: 0 !important; }
            #preloader-percent-label { opacity: 1 !important; }
        }
    </style>

    <div id="preloader" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-white transition-opacity duration-500">
        <img src="{{ asset('images/logo-sman1.png') }}" alt="Logo SMAN 1 Tenggarang" class="h-12 w-auto animate-pulse">

        <div class="mt-6 w-56">
            <div class="flex items-baseline justify-between gap-3">
                <span id="preloader-label" class="truncate text-[13px] font-medium text-slate-700">Rumah Prestasi</span>

                <span aria-hidden="true" class="grid shrink-0 justify-items-end text-slate-500">
                    <span id="preloader-pending" class="col-start-1 row-start-1 whitespace-nowrap text-[12px] font-medium leading-5 opacity-100">Menyiapkan</span>
                    <span id="preloader-percent-label" class="col-start-1 row-start-1 whitespace-nowrap font-mono text-[12px] font-medium leading-5 tabular-nums opacity-0">0%</span>
                </span>
            </div>

            <div id="preloader-track" role="progressbar" aria-labelledby="preloader-label" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
                class="mt-2 rounded-[4px] bg-slate-200/70 p-[2px] shadow-[inset_0_1px_2px_rgba(51,65,85,0.15),inset_0_0_0_1px_rgba(51,65,85,0.08)]">
                <div class="relative h-[8px] overflow-hidden rounded-[2px]">
                    <span id="preloader-shimmer" class="absolute inset-y-0 left-0 block w-2/5 rounded-[2px] bg-navy-700 shadow-[inset_0_1px_0_rgba(255,255,255,0.35),inset_0_-1px_0_rgba(28,25,23,0.2)]"></span>
                    <span id="preloader-bar" class="absolute inset-0 block origin-left rounded-[2px] bg-navy-700 shadow-[inset_0_1px_0_rgba(255,255,255,0.35),inset_0_-1px_0_rgba(28,25,23,0.2)]"></span>
                </div>
            </div>
        </div>

        <span id="preloader-status" aria-live="polite" class="sr-only"></span>
    </div>

    <script>
        (function () {
            var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var indeterminateMs = reducedMotion ? 0 : 400;
            var fillMs = reducedMotion ? 200 : 2600;
            var totalMs = indeterminateMs + fillMs;

            var percentLabel = document.getElementById('preloader-percent-label');
            var track = document.getElementById('preloader-track');
            var status = document.getElementById('preloader-status');
            var overlay = document.getElementById('preloader');
            var start = Date.now();

            document.documentElement.style.overflow = 'hidden';

            var interval = setInterval(function () {
                var elapsed = Date.now() - start;
                var fillElapsed = Math.max(0, elapsed - indeterminateMs);
                var progress = Math.min(1, fillElapsed / fillMs);
                var value = Math.round(progress * 100);

                percentLabel.textContent = value + '%';
                track.setAttribute('aria-valuenow', value);

                if (elapsed >= totalMs) {
                    clearInterval(interval);
                    status.textContent = 'Selesai';
                }
            }, 30);

            setTimeout(function () {
                overlay.style.opacity = '0';
                document.documentElement.style.overflow = '';
                setTimeout(function () {
                    overlay.remove();
                }, 500);
            }, totalMs);
        })();
    </script>
@endpush

@section('content')
    @if(app()->environment('local'))
        <details class="fixed top-4 right-4 z-50 group">
            <summary class="list-none cursor-pointer select-none flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 px-3 py-1.5 text-xs font-bold shadow-sm hover:bg-amber-200 transition">
                🔑 Demo Login
            </summary>
            <div class="mt-2 w-72 rounded-2xl border border-slate-200 bg-white shadow-lg p-4 text-xs">
                <p class="font-bold text-slate-600 mb-3">Akun demo (sementara)</p>

                <div class="space-y-3">
                    <div>
                        <p class="font-semibold text-navy-800">Admin</p>
                        <p class="text-slate-500">admin@smasga.sch.id</p>
                        <p class="text-slate-500">password</p>
                    </div>
                    <div>
                        <p class="font-semibold text-navy-800">Guru</p>
                        <p class="text-slate-500">siti.rahayu@smasga.sch.id</p>
                        <p class="text-slate-500">password</p>
                    </div>
                    <div>
                        <p class="font-semibold text-navy-800">Siswa</p>
                        <p class="text-slate-500">NIS: 21034</p>
                        <p class="text-slate-500">password</p>
                    </div>
                </div>

                <p class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-400">Hapus panel ini sebelum rilis produksi.</p>
            </div>
        </details>
    @endif

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
                <div class="relative">
                    <input id="password" name="password" type="password" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                    <button type="button" id="toggle-password" aria-label="Lihat kata sandi" aria-pressed="false"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-navy-700">
                        <svg id="icon-eye" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="icon-eye-off" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.9 19.9 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.86 19.86 0 0 1-3.22 4.44M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                            <path d="M1 1l22 22"/>
                        </svg>
                    </button>
                </div>
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

    <p class="text-center text-xs text-slate-400 mt-6">Rumah Prestasi SMAN 1 Tenggarang &middot; v1.0</p>
    <p class="text-center text-xs text-slate-400 mt-1">
        Copyright &copy; {{ date('Y') }}
        <a href="https://cvsatriateknologi.com/" target="_blank" rel="noopener noreferrer" class="text-slate-500 hover:text-navy-700 underline">CV SATRIA TEKNOLOGI</a>
    </p>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function () {
            const input = document.getElementById('password');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
            document.getElementById('icon-eye').classList.toggle('hidden', isHidden);
            document.getElementById('icon-eye-off').classList.toggle('hidden', !isHidden);
        });
    </script>
@endsection
