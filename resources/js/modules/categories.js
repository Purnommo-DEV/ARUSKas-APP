import { $, confirmAction, requestAction, submitAjax } from '../utils/ajax';
import { createDataTable } from '../utils/datatable';
import { escapeHtml } from '../utils/format';

export function initializeCategories() {
    const module = document.querySelector('#categories-module');
    if (!module) return;

    const form = document.querySelector('#category-form');
    const modal = document.querySelector('#category-modal');
    const modalTitle = document.querySelector('#category-modal-title');
    const table = createDataTable('#categories-table', {
        ajax: module.dataset.dataUrl,
        order: [[0, 'asc']],
        columns: [
            { data: 'name', name: 'name', render: (data) => `<span class="font-semibold text-slate-700">${escapeHtml(data)}</span>` },
            {
                data: 'type',
                name: 'type',
                render: (data) => data === 'income'
                    ? '<span class="badge border-emerald-100 bg-emerald-50 text-emerald-700">Pemasukan</span>'
                    : '<span class="badge border-red-100 bg-red-50 text-red-700">Pengeluaran</span>',
            },
            {
                data: 'is_active',
                name: 'is_active',
                render: (data) => data
                    ? '<span class="inline-flex items-center gap-1.5 text-emerald-700"><span class="size-2 rounded-full bg-emerald-500"></span>Aktif</span>'
                    : '<span class="inline-flex items-center gap-1.5 text-slate-400"><span class="size-2 rounded-full bg-slate-300"></span>Nonaktif</span>',
            },
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (_, __, row) => `
                    <div class="flex items-center justify-end gap-1">
                        <button class="btn btn-ghost btn-sm text-blue-600" data-category-edit="${row.id}" title="Edit">Edit</button>
                        <button class="btn btn-ghost btn-sm ${row.is_active ? 'text-amber-600' : 'text-emerald-600'}" data-category-toggle="${row.id}" data-active="${row.is_active ? 1 : 0}">${row.is_active ? 'Nonaktifkan' : 'Aktifkan'}</button>
                        <button class="btn btn-ghost btn-sm text-red-500" data-category-delete="${row.id}">Hapus</button>
                    </div>`,
            },
        ],
    });

    const openCreate = () => {
        form.reset();
        form.dataset.id = '';
        form.querySelector('[name="is_active"]').value = '1';
        modalTitle.textContent = 'Tambah Kategori';
        modal.showModal();
    };

    document.querySelector('#add-category')?.addEventListener('click', openCreate);

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
        const editButton = event.target.closest('[data-category-edit]');
        if (editButton) {
            $.get(`${module.dataset.baseUrl}/${editButton.dataset.categoryEdit}`).done(({ data }) => {
                form.reset();
                form.dataset.id = data.id;
                form.querySelector('[name="name"]').value = data.name;
                form.querySelector('[name="type"]').value = data.type;
                form.querySelector('[name="is_active"]').value = data.is_active ? '1' : '0';
                modalTitle.textContent = 'Edit Kategori';
                modal.showModal();
            });
            return;
        }

        const toggleButton = event.target.closest('[data-category-toggle]');
        if (toggleButton) {
            const active = toggleButton.dataset.active === '1';
            if (await confirmAction({
                title: active ? 'Nonaktifkan kategori?' : 'Aktifkan kategori?',
                text: active ? 'Kategori tidak akan muncul pada pilihan transaksi baru.' : 'Kategori akan tersedia kembali.',
                confirmText: active ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
            })) {
                requestAction({
                    url: `${module.dataset.baseUrl}/${toggleButton.dataset.categoryToggle}/toggle`,
                    method: 'PATCH',
                    success: () => table.ajax.reload(null, false),
                });
            }
            return;
        }

        const deleteButton = event.target.closest('[data-category-delete]');
        if (deleteButton && await confirmAction({
            title: 'Hapus kategori?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            confirmText: 'Ya, hapus',
        })) {
            requestAction({
                url: `${module.dataset.baseUrl}/${deleteButton.dataset.categoryDelete}`,
                success: () => table.ajax.reload(null, false),
            });
        }
    });
}
