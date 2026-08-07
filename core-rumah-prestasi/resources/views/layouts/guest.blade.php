<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Rumah Prestasi') — SMAN 1 Tenggarang</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    @stack('preloader')

    <div class="w-full max-w-sm">
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center p-2">
                <img src="{{ asset('images/logo-sman1.png') }}" alt="Logo SMAN 1 Tenggarang" class="h-12 w-auto">
            </div>
            <div class="mt-3 text-sm font-semibold text-navy-800 tracking-wide">SMAN 1 Tenggarang</div>
            <div class="text-xs text-slate-500">Rumah Prestasi Siswa</div>
        </div>

        @yield('content')
    </div>
</body>
</html>
