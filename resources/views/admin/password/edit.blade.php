@extends('layouts.desktop')

@section('title', 'Ganti Password')

@section('content')
    <div class="h-[74px] bg-white border-b border-slate-200 flex items-center px-8 shrink-0">
        <div class="text-lg font-extrabold text-navy-900">Ganti Password</div>
    </div>

    <div class="flex-1 p-8 overflow-auto">
        <x-flash />

        <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-lg">
            <p class="text-sm text-slate-500 mb-5">Masukkan kata sandi saat ini, lalu buat kata sandi baru (minimal 8 karakter).</p>

            <form method="POST" action="{{ route('admin.password.update') }}" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                        <button type="button" class="toggle-password absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg border-0 bg-transparent p-0 leading-none text-slate-400 hover:text-slate-600" data-target="current_password" aria-label="Tampilkan kata sandi">
                            <svg class="eye-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off-icon w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-3.11 2.6A9.12 9.12 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 4.22-5.94"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="8"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                        <button type="button" class="toggle-password absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg border-0 bg-transparent p-0 leading-none text-slate-400 hover:text-slate-600" data-target="password" aria-label="Tampilkan kata sandi">
                            <svg class="eye-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off-icon w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-3.11 2.6A9.12 9.12 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 4.22-5.94"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-600 mb-1.5">Ulangi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-navy-700">
                        <button type="button" class="toggle-password absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg border-0 bg-transparent p-0 leading-none text-slate-400 hover:text-slate-600" data-target="password_confirmation" aria-label="Tampilkan kata sandi">
                            <svg class="eye-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off-icon w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-3.11 2.6A9.12 9.12 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 4.22-5.94"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="mt-2 bg-navy-800 text-white text-sm font-bold px-6 py-3 rounded-xl self-start">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = document.getElementById(btn.dataset.target);
                if (!input) return;
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.querySelector('.eye-icon').classList.toggle('hidden', show);
                btn.querySelector('.eye-off-icon').classList.toggle('hidden', !show);
            });
        });
    </script>
@endpush
