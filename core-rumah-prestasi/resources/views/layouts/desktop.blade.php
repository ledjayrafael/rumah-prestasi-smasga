<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Rumah Prestasi</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F4F5FA] text-navy-900">
    <div class="flex min-h-screen">
        <aside class="desktop-sidebar w-60 bg-navy-800 text-white flex flex-col shrink-0">
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center p-1.5 shrink-0">
                    <img src="{{ asset('images/logo-sman1.png') }}" alt="Logo" class="h-full w-auto">
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-extrabold truncate">SMAN 1 Tenggarang</div>
                    <div class="text-[11px] text-white/60">Rumah Prestasi</div>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                @foreach ($navItems ?? [] as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold
                              {{ $item['active'] ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/5' }}">
                        <span class="w-5 h-5 shrink-0">{!! $item['icon'] !!}</span>
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @isset($item['badge'])
                            @if ($item['badge'] > 0)
                                <span class="bg-gold-500 text-navy-950 text-[11px] font-extrabold px-2 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                            @endif
                        @endisset
                    </a>
                @endforeach
            </nav>

            <div class="px-4 py-4 mx-3 mb-3 border-t border-white/10 flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-gold-500 text-[#3a2a00] flex items-center justify-center text-sm font-extrabold shrink-0">
                    {{ Str::of(auth()->user()->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-white/60 truncate">
                        @if (auth()->user()->isGuru())
                            {{ auth()->user()->isWaliKelas() ? 'Wali Kelas' : 'Guru' }}
                        @elseif (auth()->user()->isDeveloper())
                            Developer
                        @else
                            Admin Sekolah
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" data-confirm-logout>
                    @csrf
                    <button type="submit" title="Keluar" class="text-white/50 hover:text-red-300">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 min-w-0 flex flex-col">
            @yield('content')
        </main>
    </div>

    <x-logout-confirm-modal />

    @stack('scripts')
</body>
</html>
