import { $, confirmAction, notifyError, requestAction, submitAjax } from '../utils/ajax';
import { createDataTable } from '../utils/datatable';
import { escapeHtml, formatDate } from '../utils/format';

export function initializeUsers() {
    const module = document.querySelector('#users-module');
    if (!module) return;

    const form = document.querySelector('#user-form');
    const modal = document.querySelector('#user-modal');
    const modalTitle = document.querySelector('#user-modal-title');
    const passwordHint = document.querySelector('#password-hint');
    const table = createDataTable('#users-table', {
        ajax: module.dataset.dataUrl,
        order: [[0, 'asc']],
        columns: [
            { data: 'name', name: 'name', render: (data) => `<span class="font-semibold text-slate-700">${escapeHtml(data)}</span>` },
            { data: 'email', name: 'email', render: escapeHtml },
            {
                data: 'role',
                searchable: false,
                orderable: false,
                render: (data) => data === 'admin'
                    ? '<span class="badge border-blue-100 bg-blue-50 text-blue-700">Admin</span>'
                    : '<span class="badge border-emerald-100 bg-emerald-50 text-emerald-700">User</span>',
            },
            { data: 'created_at', name: 'created_at', render: formatDate },
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (_, __, row) => `
                    <div class="flex items-center justify-end gap-1">
                        <button class="btn btn-ghost btn-sm text-blue-600" data-user-edit="${row.id}">Edit</button>
                        <button class="btn btn-ghost btn-sm text-red-500" data-user-delete="${row.id}">Hapus</button>
                    </div>`,
            },
        ],
    });

    document.querySelector('#add-user')?.addEventListener('click', () => {
        form.reset();
        form.dataset.id = '';
        modalTitle.textContent = 'Tambah User';
        passwordHint.textContent = 'Minimal 8 karakter.';
        modal.showModal();
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const id = form.dataset.id;
        submitAjax(form, {
            url: id ? `${module.dataset.baseUrl}/${id}` : module.dataset.storeUrl,
            method: id ? 'PUT' : 'POST',
            onSuccess: () => {
                modal.close();
                table.ajax.reload(null, false);
            },
        });
    });

    module.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-user-edit]');
        if (editButton) {
            $.get(`${module.dataset.baseUrl}/${editButton.dataset.userEdit}`)
                .done(({ data }) => {
                    form.reset();
                    form.dataset.id = data.id;
                    form.querySelector('[name="name"]').value = data.name;
                    form.querySelector('[name="email"]').value = data.email;
                    form.querySelector('[name="role"]').value = data.role;
                    modalTitle.textContent = 'Edit User';
                    passwordHint.textContent = 'Kosongkan jika kata sandi tidak diubah.';
                    modal.showModal();
                })
                .fail((xhr) => notifyError(xhr));
            return;
        }

        const deleteButton = event.target.closest('[data-user-delete]');
        if (deleteButton && await confirmAction({
            title: 'Hapus user?',
            text: 'User tidak akan dapat masuk kembali.',
            confirmText: 'Ya, hapus',
        })) {
            requestAction({
                url: `${module.dataset.baseUrl}/${deleteButton.dataset.userDelete}`,
                success: () => table.ajax.reload(null, false),
            });
        }
    });
}
