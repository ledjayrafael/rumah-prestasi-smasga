@extends('layouts.siswa')

@section('title', 'Profil')

@section('content')
    <div class="bg-navy-800 text-white px-5 pt-6 pb-32 rounded-b-[28px] relative overflow-hidden">
        <div class="absolute -right-10 -top-12 w-44 h-44 rounded-full bg-white/5"></div>

        <div class="relative flex items-center justify-between">
            <div class="text-lg font-extrabold">Profil</div>
            <div class="flex items-center gap-1.5 text-[11px] font-bold {{ $user->is_active ? 'text-emerald-300' : 'text-white/40' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-300' : 'bg-white/40' }}"></span>
                {{ $user->is_active ? 'Akun Aktif' : 'Nonaktif' }}
            </div>
        </div>
    </div>

    {{-- Kartu pelajar digital, ditumpuk seperti dompet kartu — ditarik naik agar overlap dengan box navy --}}
    <div class="relative z-10 -mt-28 px-5 pb-8">
        <div aria-hidden="true" class="absolute left-1/2 top-4 w-[91%] h-full -translate-x-1/2 rotate-[-3deg] rounded-[20px] bg-gold-100 shadow-sm"></div>
        <div aria-hidden="true" class="absolute left-1/2 top-2 w-[96%] h-full -translate-x-1/2 rotate-[2deg] rounded-[21px] bg-white shadow-sm"></div>

        <div id="id-card" class="relative rounded-[22px] bg-white text-navy-900 shadow-[0_20px_36px_-14px_rgba(15,13,40,0.5)] overflow-hidden">
            <div class="h-[3px] bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>
            <div id="id-card-sheen" class="pointer-events-none absolute inset-0"></div>

            <div class="relative px-5 pt-4 pb-3">
                <div class="flex items-center justify-between">
                    <div class="text-[10px] font-bold tracking-[0.16em] text-slate-400 uppercase">Kartu Pelajar Digital</div>
                    <img src="{{ asset('images/logo-sman1.png') }}" alt="" class="h-8 w-auto opacity-70">
                </div>

                <div class="flex items-center gap-4 mt-3">
                    <div class="relative shrink-0">
                        <div id="avatar-frame" class="w-24 h-24 rounded-full bg-gold-500 text-[#3a2a00] flex items-center justify-center text-2xl font-extrabold ring-4 ring-gold-100 overflow-hidden">
                            @if ($user->avatarUrl())
                                <img id="avatar-image" src="{{ $user->avatarUrl() }}" alt="Foto profil" class="w-full h-full object-cover">
                            @else
                                <span id="avatar-initials">{{ Str::of($user->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('') }}</span>
                            @endif
                        </div>
                        <button type="button" id="avatar-edit-btn" aria-label="Ubah foto profil"
                                class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-gold-500 text-[#3a2a00] flex items-center justify-center border-[3px] border-white shadow-lg transition-transform hover:scale-110 active:scale-95">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        </button>
                        <input type="file" id="avatar-input" accept="image/*" class="hidden">
                    </div>
                    <div class="min-w-0">
                        <div class="text-[15px] font-extrabold truncate">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500 mt-0.5 truncate">{{ optional($user->studentProfile->schoolClass)->name ?? '—' }}</div>
                        <div class="inline-flex items-center gap-1.5 mt-1.5 rounded-md bg-navy-100 px-2 py-1 font-mono text-[11px] font-bold tracking-wider text-navy-800">
                            NIS {{ $user->studentProfile->nis }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- strip barcode, menegaskan kartu ini adalah "pass" digital --}}
            <div class="relative flex items-end gap-[3px] h-5 px-5 pb-3 opacity-60" aria-hidden="true">
                @php $bars = [4,2,6,3,1,5,2,7,3,2,5,1,4,6,2,3,5,1,2,4,6,3,1,5,2,4,3,1]; @endphp
                @foreach ($bars as $h)
                    <span class="w-[2px] bg-navy-900/70 rounded-full" style="height: {{ $h * 2 }}px"></span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="px-4 mt-4">
        <x-flash />
    </div>

    <div id="avatar-toast" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-[60] max-w-[90%] rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium px-4 py-3 shadow-lg"></div>

    <div id="avatar-modal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center px-4">
        <div class="bg-white rounded-[28px] p-4 w-full max-w-sm">
            <div class="text-sm font-extrabold text-navy-900 mb-3">Sesuaikan Foto</div>

            <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-slate-900 touch-none select-none">
                <canvas id="crop-canvas" width="320" height="320" class="w-full h-full cursor-move"></canvas>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Geser untuk memindah, gunakan slider untuk memperbesar.</p>

            <div class="flex items-center gap-2 mt-2">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"><circle cx="10" cy="10" r="6"/><path d="m20 20-4.3-4.3"/></svg>
                <input type="range" id="zoom-range" min="1" max="3" step="0.01" value="1" class="flex-1 accent-gold-500">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"><circle cx="10" cy="10" r="6"/><path d="m20 20-4.3-4.3"/><path d="M10 7v6M7 10h6"/></svg>
            </div>

            <div id="avatar-progress-wrap" class="hidden mt-4">
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mb-1">
                    <span>Menyimpan foto&hellip;</span>
                    <span id="avatar-progress-text">0%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                    <div id="avatar-progress-bar" class="h-full bg-gradient-to-r from-gold-600 to-gold-400 transition-[width] duration-150" style="width:0%"></div>
                </div>
            </div>

            <p id="avatar-error" class="hidden text-xs text-red-600 font-medium mt-3"></p>

            <div class="flex gap-2 mt-4">
                <button type="button" id="avatar-cancel-btn" class="flex-1 rounded-xl border border-slate-300 text-slate-600 text-sm font-bold py-2.5 transition-colors hover:bg-slate-50 active:scale-[0.98]">Batal</button>
                <button type="button" id="avatar-save-btn" class="flex-1 rounded-xl bg-navy-800 text-white text-sm font-bold py-2.5 transition-transform active:scale-[0.98]">Simpan</button>
            </div>
        </div>
    </div>

    <div class="px-4 mt-12">
        <div class="flex items-center gap-1.5 mb-2 px-1">
            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Pengaturan Akun</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl divide-y divide-slate-100 overflow-hidden">
            <details class="group" @if ($errors->hasAny(['name', 'phone'])) open @endif>
                <summary class="flex items-center justify-between gap-3 px-4 py-3.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-navy-100 flex items-center justify-center shrink-0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#231E52" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <span class="text-sm font-bold text-navy-900">Data Siswa</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </summary>
                <form method="POST" action="{{ route('siswa.profile.update') }}" class="px-4 pb-4 pt-1 flex flex-col gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm transition-colors focus:outline-none focus:bg-white focus:border-navy-700 focus:ring-4 focus:ring-navy-700/10">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">No. Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm transition-colors focus:outline-none focus:bg-white focus:border-navy-700 focus:ring-4 focus:ring-navy-700/10">
                    </div>
                    <button type="submit" class="mt-1 rounded-xl bg-navy-800 text-white text-sm font-bold py-2.5 transition-transform active:scale-[0.98]">Simpan Data Diri</button>
                </form>
            </details>

            <details class="group" @if ($errors->hasAny(['current_password', 'password'])) open @endif>
                <summary class="flex items-center justify-between gap-3 px-4 py-3.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-navy-100 flex items-center justify-center shrink-0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#231E52" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <span class="text-sm font-bold text-navy-900">Keamanan</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </summary>
                <form method="POST" action="{{ route('siswa.profile.password') }}" class="px-4 pb-4 pt-1 flex flex-col gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Saat Ini</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-10 text-sm transition-colors focus:outline-none focus:bg-white focus:border-navy-700 focus:ring-4 focus:ring-navy-700/10">
                            <button type="button" class="toggle-password absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg border-0 bg-transparent p-0 leading-none text-slate-400 hover:text-slate-600 active:scale-95 transition-colors" data-target="current_password" aria-label="Tampilkan kata sandi">
                                <svg class="eye-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off-icon w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-3.11 2.6A9.12 9.12 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 4.22-5.94"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="new_password" required minlength="8"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-10 text-sm transition-colors focus:outline-none focus:bg-white focus:border-navy-700 focus:ring-4 focus:ring-navy-700/10">
                            <button type="button" class="toggle-password absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg border-0 bg-transparent p-0 leading-none text-slate-400 hover:text-slate-600 active:scale-95 transition-colors" data-target="new_password" aria-label="Tampilkan kata sandi">
                                <svg class="eye-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off-icon w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-3.11 2.6A9.12 9.12 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 4.22-5.94"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Ulangi Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="new_password_confirmation" required minlength="8"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-10 text-sm transition-colors focus:outline-none focus:bg-white focus:border-navy-700 focus:ring-4 focus:ring-navy-700/10">
                            <button type="button" class="toggle-password absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg border-0 bg-transparent p-0 leading-none text-slate-400 hover:text-slate-600 active:scale-95 transition-colors" data-target="new_password_confirmation" aria-label="Tampilkan kata sandi">
                                <svg class="eye-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off-icon w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-3.11 2.6A9.12 9.12 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 4.22-5.94"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="mt-1 rounded-xl bg-navy-800 text-white text-sm font-bold py-2.5 transition-transform active:scale-[0.98]">Ganti Kata Sandi</button>
                </form>
            </details>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="px-4 mt-5" data-confirm-logout>
        @csrf
        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-2xl py-3 transition-colors hover:bg-red-50 active:scale-[0.98]">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
            Keluar
        </button>
    </form>

    <div class="text-center text-[11px] text-slate-400 font-semibold mt-5">
        Rumah Prestasi SMAN 1 Tenggarang &middot; v1.0
    </div>
    <div class="text-center text-[11px] text-slate-400 mt-1 pb-1">
        Copyright &copy; {{ date('Y') }}
        <a href="https://cvsatriateknologi.com/" target="_blank" rel="noopener noreferrer" class="text-slate-500 hover:text-navy-700 underline">CV SATRIA TEKNOLOGI</a>
    </div>

    <x-logout-confirm-modal />

    <style>
        @keyframes id-card-in {
            from { opacity: 0; transform: translateY(10px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes id-card-sheen-sweep {
            0% { transform: translateX(-120%) skewX(-12deg); opacity: 0; }
            15% { opacity: .55; }
            55% { opacity: .55; }
            100% { transform: translateX(220%) skewX(-12deg); opacity: 0; }
        }
        #id-card {
            animation: id-card-in .5s cubic-bezier(.16, 1, .3, 1) both;
        }
        #id-card-sheen {
            background: linear-gradient(75deg, transparent 42%, rgba(255, 255, 255, .65) 50%, transparent 58%);
            animation: id-card-sheen-sweep 1.5s ease-out .55s 1 both;
        }
        @media (prefers-reduced-motion: reduce) {
            #id-card, #id-card-sheen { animation: none; }
        }
    </style>

    <script>
        (function () {
            document.querySelectorAll('.toggle-password').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.dataset.target);
                    if (!input) return;
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.querySelector('.eye-icon').classList.toggle('hidden', show);
                    btn.querySelector('.eye-off-icon').classList.toggle('hidden', !show);
                    btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                });
            });
        })();

        (function () {
            const fileInput = document.getElementById('avatar-input');
            const editBtn = document.getElementById('avatar-edit-btn');
            const modal = document.getElementById('avatar-modal');
            const cancelBtn = document.getElementById('avatar-cancel-btn');
            const saveBtn = document.getElementById('avatar-save-btn');
            const canvas = document.getElementById('crop-canvas');
            const ctx = canvas.getContext('2d');
            const zoomRange = document.getElementById('zoom-range');
            const progressWrap = document.getElementById('avatar-progress-wrap');
            const progressBar = document.getElementById('avatar-progress-bar');
            const progressText = document.getElementById('avatar-progress-text');
            const errorEl = document.getElementById('avatar-error');
            const toast = document.getElementById('avatar-toast');
            const avatarFrame = document.getElementById('avatar-frame');

            const VIEWPORT_SIZE = canvas.width;
            const OUTPUT_SIZE = 512;
            const MAX_ZOOM = 3;
            const WEBP_QUALITY = 0.7; // "medium" compression

            let img = null;
            let minScale = 1;
            let scale = 1;
            let cx = 0;
            let cy = 0;
            let dragging = false;
            let dragStartX = 0;
            let dragStartY = 0;
            let dragStartCx = 0;
            let dragStartCy = 0;

            function clamp(val, min, max) {
                return Math.min(Math.max(val, min), max);
            }

            function clampCenter() {
                const half = (VIEWPORT_SIZE / scale) / 2;
                cx = clamp(cx, half, img.naturalWidth - half);
                cy = clamp(cy, half, img.naturalHeight - half);
            }

            function draw(targetCtx, size) {
                const sSize = VIEWPORT_SIZE / scale;
                const sx = cx - sSize / 2;
                const sy = cy - sSize / 2;
                targetCtx.clearRect(0, 0, size, size);
                targetCtx.drawImage(img, sx, sy, sSize, sSize, 0, 0, size, size);
            }

            function render() {
                draw(ctx, VIEWPORT_SIZE);
            }

            function resetState() {
                minScale = VIEWPORT_SIZE / Math.min(img.naturalWidth, img.naturalHeight);
                scale = minScale;
                cx = img.naturalWidth / 2;
                cy = img.naturalHeight / 2;
                zoomRange.value = '1';
                clampCenter();
                render();
            }

            function openModal() {
                errorEl.classList.add('hidden');
                progressWrap.classList.add('hidden');
                progressBar.style.width = '0%';
                progressText.textContent = '0%';
                saveBtn.disabled = false;
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                fileInput.value = '';
                img = null;
            }

            editBtn.addEventListener('click', () => fileInput.click());
            cancelBtn.addEventListener('click', closeModal);

            fileInput.addEventListener('change', function () {
                const file = fileInput.files[0];
                if (!file || !file.type.startsWith('image/')) {
                    fileInput.value = '';
                    return;
                }
                const url = URL.createObjectURL(file);
                const image = new Image();
                image.onload = function () {
                    img = image;
                    resetState();
                    openModal();
                    URL.revokeObjectURL(url);
                };
                image.src = url;
            });

            zoomRange.addEventListener('input', function () {
                if (!img) return;
                scale = minScale * parseFloat(zoomRange.value);
                clampCenter();
                render();
            });

            canvas.addEventListener('pointerdown', function (e) {
                if (!img) return;
                dragging = true;
                canvas.setPointerCapture(e.pointerId);
                dragStartX = e.clientX;
                dragStartY = e.clientY;
                dragStartCx = cx;
                dragStartCy = cy;
            });

            canvas.addEventListener('pointermove', function (e) {
                if (!dragging || !img) return;
                const rectScale = VIEWPORT_SIZE / canvas.getBoundingClientRect().width;
                const dx = (e.clientX - dragStartX) * rectScale;
                const dy = (e.clientY - dragStartY) * rectScale;
                cx = dragStartCx - dx / scale;
                cy = dragStartCy - dy / scale;
                clampCenter();
                render();
            });

            function endDrag() {
                dragging = false;
            }
            canvas.addEventListener('pointerup', endDrag);
            canvas.addEventListener('pointercancel', endDrag);
            canvas.addEventListener('pointerleave', endDrag);

            canvas.addEventListener('wheel', function (e) {
                if (!img) return;
                e.preventDefault();
                const step = e.deltaY < 0 ? 0.05 : -0.05;
                const next = clamp(parseFloat(zoomRange.value) + step, 1, MAX_ZOOM);
                zoomRange.value = String(next);
                scale = minScale * next;
                clampCenter();
                render();
            }, { passive: false });

            saveBtn.addEventListener('click', function () {
                if (!img) return;
                errorEl.classList.add('hidden');
                saveBtn.disabled = true;

                const outputCanvas = document.createElement('canvas');
                outputCanvas.width = OUTPUT_SIZE;
                outputCanvas.height = OUTPUT_SIZE;
                draw(outputCanvas.getContext('2d'), OUTPUT_SIZE);

                outputCanvas.toBlob(function (blob) {
                    if (!blob) {
                        errorEl.textContent = 'Gagal memproses foto. Coba lagi.';
                        errorEl.classList.remove('hidden');
                        saveBtn.disabled = false;
                        return;
                    }
                    uploadAvatar(blob);
                }, 'image/webp', WEBP_QUALITY);
            });

            function uploadAvatar(blob) {
                const formData = new FormData();
                formData.append('avatar', blob, 'avatar.webp');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('siswa.profile.avatar') }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');

                progressWrap.classList.remove('hidden');

                xhr.upload.addEventListener('progress', function (e) {
                    if (!e.lengthComputable) return;
                    const pct = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = pct + '%';
                    progressText.textContent = pct + '%';
                });

                xhr.addEventListener('load', function () {
                    saveBtn.disabled = false;
                    if (xhr.status >= 200 && xhr.status < 300) {
                        let data = {};
                        try { data = JSON.parse(xhr.responseText); } catch (e) {}
                        applyAvatar(data.avatar_url);
                        closeModal();
                        showToast(data.message || 'Foto profil berhasil diperbarui.');
                    } else {
                        let message = 'Gagal menyimpan foto. Coba lagi.';
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.message) message = data.message;
                        } catch (e) {}
                        errorEl.textContent = message;
                        errorEl.classList.remove('hidden');
                        progressWrap.classList.add('hidden');
                    }
                });

                xhr.addEventListener('error', function () {
                    saveBtn.disabled = false;
                    errorEl.textContent = 'Koneksi gagal. Periksa jaringan Anda dan coba lagi.';
                    errorEl.classList.remove('hidden');
                    progressWrap.classList.add('hidden');
                });

                xhr.send(formData);
            }

            function applyAvatar(url) {
                if (!url) return;
                avatarFrame.innerHTML = '';
                const image = document.createElement('img');
                image.id = 'avatar-image';
                image.alt = 'Foto profil';
                image.className = 'w-full h-full object-cover';
                image.src = url + '?t=' + Date.now();
                avatarFrame.appendChild(image);
            }

            function showToast(message) {
                toast.textContent = message;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }
        })();
    </script>
@endsection
