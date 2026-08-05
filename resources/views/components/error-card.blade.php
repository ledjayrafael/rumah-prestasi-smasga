@props(['title', 'message'])

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center">
    <img src="{{ asset('images/logo-cst.png') }}" alt="CV Satria Teknologi"
        class="mx-auto mb-5 w-24 h-24 rounded-full object-cover">

    <h1 class="text-lg font-extrabold text-navy-900 mb-1">{{ $title }}</h1>
    <p class="text-sm text-slate-500 mb-8">{{ $message }}</p>

    <a href="{{ route('home') }}"
        class="inline-flex w-full items-center justify-center rounded-xl bg-navy-800 text-white text-sm font-bold py-3 shadow-lg shadow-navy-800/25 hover:bg-navy-700 transition">
        Kembali ke Beranda
    </a>
</div>

<p class="text-center text-xs text-slate-400 mt-6">Rumah Prestasi SMAN 1 Tenggarang &middot; v1.0</p>
<p class="text-center text-xs text-slate-400 mt-1">
    Copyright &copy; {{ date('Y') }}
    <a href="https://cvsatriateknologi.com/" target="_blank" rel="noopener noreferrer" class="text-slate-500 hover:text-navy-700 underline">CV SATRIA TEKNOLOGI</a>
</p>
