@extends('layouts.desktop')

@section('title', 'Kredensial Akun Siswa')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center gap-3.5 px-8 shrink-0">
        <a href="{{ route('guru.students.index') }}"
           class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center hover:bg-slate-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-2">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4B4A66" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <div class="text-lg font-extrabold text-navy-900 tracking-tight">Kredensial Akun Baru</div>
            <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Tampil sekali — tidak bisa dibuka ulang</p>
        </div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <div class="max-w-lg">
            @if ($credential)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl overflow-hidden">
                    <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-amber-200/70">
                        <svg class="w-4 h-4 text-amber-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                        <p class="text-sm font-extrabold text-amber-900">Salin sekarang — halaman ini hanya tersedia beberapa menit</p>
                    </div>

                    <div class="p-5 flex flex-col gap-3">
                        <div>
                            <p class="text-[11px] font-bold text-amber-800/70 mb-1">Nama</p>
                            <p class="text-sm font-bold text-navy-900">{{ $credential['name'] }}</p>
                        </div>

                        <div>
                            <p class="text-[11px] font-bold text-amber-800/70 mb-1">Login (NIS)</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 bg-white border border-amber-200 rounded-lg px-3 py-2 text-sm font-mono font-bold text-navy-900 select-all">{{ $credential['login'] }}</code>
                                <button type="button" data-copy="{{ $credential['login'] }}"
                                        class="js-copy-btn shrink-0 w-9 h-9 rounded-lg border border-amber-200 bg-white flex items-center justify-center text-amber-700 hover:bg-amber-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-1"
                                        aria-label="Salin login">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-bold text-amber-800/70 mb-1">Password sementara</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 bg-white border border-amber-200 rounded-lg px-3 py-2 text-sm font-mono font-bold text-navy-900 select-all">{{ $credential['password'] }}</code>
                                <button type="button" data-copy="{{ $credential['password'] }}"
                                        class="js-copy-btn shrink-0 w-9 h-9 rounded-lg border border-amber-200 bg-white flex items-center justify-center text-amber-700 hover:bg-amber-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-700 focus-visible:ring-offset-1"
                                        aria-label="Salin password">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-4">Siswa wajib ganti password saat login pertama.</p>
            @else
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-sm text-slate-600">
                    Kredensial tidak lagi tersedia. Buat ulang password lewat edit akun atau hubungi admin sistem.
                </div>
            @endif

            <a href="{{ route('guru.students.index') }}" class="inline-flex items-center gap-1.5 mt-6 text-sm font-bold text-navy-800 hover:text-navy-700 transition-colors">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                Kembali ke daftar siswa
            </a>
        </div>
    </div>

    @if ($credential)
        <script>
            (function () {
                document.querySelectorAll('.js-copy-btn').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        navigator.clipboard.writeText(btn.dataset.copy).then(function () {
                            var icon = btn.innerHTML;
                            btn.innerHTML = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
                            btn.classList.add('!text-green-700', '!border-green-200');
                            setTimeout(function () {
                                btn.innerHTML = icon;
                                btn.classList.remove('!text-green-700', '!border-green-200');
                            }, 1500);
                        });
                    });
                });
            })();
        </script>
    @endif
@endsection
