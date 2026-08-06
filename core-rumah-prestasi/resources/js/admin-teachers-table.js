document.addEventListener('DOMContentLoaded', () => {
    initResetPasswordModal();
    initCredentialModal();
    initSortableTeacherTable();
});

function initCredentialModal() {
    const modal = document.getElementById('credential-modal');
    if (!modal) return;

    const closeBtn = document.getElementById('credential-modal-close');
    const closeModal = () => modal.remove();

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.contains(modal)) closeModal();
    });
}

function initResetPasswordModal() {
    const modal = document.getElementById('reset-password-modal');
    if (!modal) return;

    const nameEl = document.getElementById('reset-password-modal-name');
    const confirmBtn = document.getElementById('reset-password-modal-confirm');
    const cancelBtn = document.getElementById('reset-password-modal-cancel');
    let pendingForm = null;

    const closeModal = () => {
        modal.classList.add('hidden');
        pendingForm = null;
    };

    document.querySelectorAll('[data-reset-password-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            pendingForm = form;
            nameEl.textContent = form.dataset.teacherName ?? '';
            modal.classList.remove('hidden');
        });
    });

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
    confirmBtn.addEventListener('click', () => {
        pendingForm?.submit();
    });
}

function initSortableTeacherTable() {
    const table = document.querySelector('[data-sortable-table]');
    if (!table) return;

    const sortButtons = table.querySelectorAll('[data-sort-col]');
    let activeCol = null;
    let direction = 'asc';

    sortButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const col = Number(button.dataset.sortCol);
            direction = activeCol === col && direction === 'asc' ? 'desc' : 'asc';
            activeCol = col;

            const rows = Array.from(table.querySelectorAll('[data-table-row]'));
            rows.sort((a, b) => {
                const aText = a.children[col].textContent.trim().toLowerCase();
                const bText = b.children[col].textContent.trim().toLowerCase();
                return direction === 'asc'
                    ? aText.localeCompare(bText, 'id')
                    : bText.localeCompare(aText, 'id');
            });
            rows.forEach((row) => table.appendChild(row));

            sortButtons.forEach((btn) => {
                const icon = btn.querySelector('[data-sort-icon]');
                if (!icon) return;
                if (Number(btn.dataset.sortCol) === activeCol) {
                    icon.textContent = direction === 'asc' ? '▲' : '▼';
                    icon.classList.remove('opacity-0');
                } else {
                    icon.classList.add('opacity-0');
                }
            });
        });
    });
}
