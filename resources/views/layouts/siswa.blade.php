<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — Portal Prestasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,500;0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F4F5FA] text-navy-900">
    <div class="max-w-md mx-auto min-h-screen pb-28 relative">
        @yield('content')
    </div>

    @unless(request()->routeIs('siswa.achievements.create'))
    <nav class="fixed left-1/2 -translate-x-1/2 bottom-4 w-[calc(100%-2rem)] max-w-md h-[70px] rounded-[26px]
                bg-gradient-to-br from-navy-700 to-navy-950 border border-white/10 shadow-2xl flex items-center px-2 z-40">
        <div class="flex-1 flex items-center justify-around">
            <a href="{{ route('siswa.dashboard') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl {{ request()->routeIs('siswa.dashboard') ? 'bg-gold-500/20 ring-1 ring-gold-500/40' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ request()->routeIs('siswa.dashboard') ? '#EBC15A' : 'rgba(255,255,255,.55)' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
                <span class="text-[10px] font-bold {{ request()->routeIs('siswa.dashboard') ? 'text-gold-400' : 'text-white/60' }}">Beranda</span>
            </a>
            <a href="{{ route('siswa.achievements.index') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl {{ request()->routeIs('siswa.achievements.*') ? 'bg-gold-500/20 ring-1 ring-gold-500/40' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ request()->routeIs('siswa.achievements.*') ? '#EBC15A' : 'rgba(255,255,255,.55)' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4zM17 5h2a2 2 0 0 1 0 4h-2M7 5H5a2 2 0 0 0 0 4h2"/></svg>
                <span class="text-[10px] font-bold {{ request()->routeIs('siswa.achievements.*') ? 'text-gold-400' : 'text-white/60' }}">Prestasi</span>
            </a>
        </div>
        <div class="w-[74px] shrink-0"></div>
        <div class="flex-1 flex items-center justify-around">
            <a href="{{ route('siswa.competitions.index') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl {{ request()->routeIs('siswa.competitions.*') ? 'bg-gold-500/20 ring-1 ring-gold-500/40' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ request()->routeIs('siswa.competitions.*') ? '#EBC15A' : 'rgba(255,255,255,.55)' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                <span class="text-[10px] font-bold {{ request()->routeIs('siswa.competitions.*') ? 'text-gold-400' : 'text-white/60' }}">Info Lomba</span>
            </a>
            <a href="{{ route('siswa.profile.edit') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-2 rounded-2xl {{ request()->routeIs('siswa.profile.*') ? 'bg-gold-500/20 ring-1 ring-gold-500/40' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ request()->routeIs('siswa.profile.*') ? '#EBC15A' : 'rgba(255,255,255,.55)' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>
                <span class="text-[10px] font-bold {{ request()->routeIs('siswa.profile.*') ? 'text-gold-400' : 'text-white/60' }}">Profil</span>
            </a>
        </div>

        <a href="{{ route('siswa.achievements.create') }}"
           class="absolute left-1/2 -translate-x-1/2 -top-[26px] w-[58px] h-[58px] rounded-full
                  bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center
                  shadow-[0_12px_24px_rgba(198,135,31,0.5),0_0_0_6px_#F4F5FA]">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="#231E52" stroke-width="2.8" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        </a>
    </nav>
    @endunless
</body>
</html>
