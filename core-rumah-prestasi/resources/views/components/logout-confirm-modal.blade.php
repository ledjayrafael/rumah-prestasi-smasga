<div id="logout-confirm-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-navy-950/60 backdrop-blur-sm px-4" role="dialog" aria-modal="true" aria-labelledby="logout-confirm-title">
    <div class="w-full max-w-xs rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        </div>
        <h2 id="logout-confirm-title" class="text-center text-base font-extrabold text-navy-900">Keluar dari akun?</h2>
        <p class="mt-1 text-center text-sm text-slate-500">Anda perlu masuk kembali untuk mengakses halaman ini.</p>
        <div class="mt-5 flex gap-3">
            <button type="button" id="logout-confirm-cancel" class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-transform">Tidak</button>
            <button type="button" id="logout-confirm-submit" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-bold text-white hover:bg-red-700 active:scale-[0.98] transition-transform">Ya, Keluar</button>
        </div>
    </div>
</div>

<script>
    (function () {
        var modal = document.getElementById('logout-confirm-modal');
        if (!modal || modal.dataset.bound === '1') return;
        modal.dataset.bound = '1';

        var cancelBtn = document.getElementById('logout-confirm-cancel');
        var submitBtn = document.getElementById('logout-confirm-submit');
        var pendingForm = null;

        function openModal(form) {
            pendingForm = form;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            pendingForm = null;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('form[data-confirm-logout]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.confirmed === '1') return;
                e.preventDefault();
                openModal(form);
            });
        });

        cancelBtn.addEventListener('click', closeModal);
        submitBtn.addEventListener('click', function () {
            if (pendingForm) {
                pendingForm.dataset.confirmed = '1';
                pendingForm.submit();
            }
            closeModal();
        });
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    })();
</script>
