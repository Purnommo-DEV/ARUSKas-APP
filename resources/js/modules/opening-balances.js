import AutoNumeric from 'autonumeric';
import { $, confirmAction, notifyError, requestAction, submitAjax } from '../utils/ajax';
import { createDataTable } from '../utils/datatable';
import { escapeHtml, formatRupiah } from '../utils/format';

export function initializeOpeningBalances() {
    const module = document.querySelector('#opening-balances-module');
    if (!module) return;

    const form = document.querySelector('#opening-balance-form');
    const modal = document.querySelector('#opening-balance-modal');
    const modalTitle = document.querySelector('#opening-balance-modal-title');
    const monthInput = document.querySelector('#opening-balance-month');
    const yearInput = document.querySelector('#opening-balance-year');
    const amountInput = document.querySelector('#opening-balance-amount');
    const amountDisplay = document.querySelector('#opening-balance-display');
    const amountFormatter = new AutoNumeric(amountDisplay, {
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        decimalPlaces: 0,
        minimumValue: '0',
        maximumValue: '9000000000000000000',
        emptyInputBehavior: 'null',
        leadingZero: 'deny',
        modifyValueOnWheel: false,
        selectOnFocus: false,
    });

    const syncAmount = () => {
        amountInput.value = amountFormatter.getNumericString() || '';
    };

    amountDisplay.addEventListener('input', syncAmount);
    amountDisplay.addEventListener('change', syncAmount);

    const table = createDataTable('#opening-balances-table', {
        ajax: module.dataset.dataUrl,
        order: [[0, 'desc']],
        columns: [
            { data: 'period_label', name: 'period_label', render: (data) => `<span class="font-semibold text-slate-700">${escapeHtml(data)}</span>` },
            { data: 'opening_balance', name: 'opening_balance', className: 'text-right', render: formatRupiah },
            { data: 'notes', name: 'notes', render: (data) => data ? escapeHtml(data) : '<span class="text-slate-300">−</span>' },
            { data: 'creator_name', name: 'creator.name', render: escapeHtml },
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (_, __, row) => `
                    <div class="flex items-center justify-end gap-1">
                        <button class="btn btn-ghost btn-sm text-blue-600" data-opening-balance-edit="${row.id}">Edit</button>
                        <button class="btn btn-ghost btn-sm text-red-500" data-opening-balance-delete="${row.id}">Hapus</button>
                    </div>`,
            },
        ],
    });

    const openCreate = () => {
        form.reset();
        form.dataset.id = '';
        monthInput.value = module.dataset.currentMonth;
        yearInput.value = module.dataset.currentYear;
        amountFormatter.clear();
        amountInput.value = '';
        modalTitle.textContent = 'Tambah Kas Awal';
        modal.showModal();
    };

    document.querySelector('#add-opening-balance')?.addEventListener('click', openCreate);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        syncAmount();
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
        const editButton = event.target.closest('[data-opening-balance-edit]');
        if (editButton) {
            $.get(`${module.dataset.baseUrl}/${editButton.dataset.openingBalanceEdit}`)
                .done(({ data }) => {
                    form.reset();
                    form.dataset.id = data.id;
                    monthInput.value = data.period_month;
                    yearInput.value = data.period_year;
                    form.querySelector('[name="notes"]').value = data.notes ?? '';
                    amountFormatter.set(String(data.opening_balance));
                    syncAmount();
                    modalTitle.textContent = 'Edit Kas Awal';
                    modal.showModal();
                })
                .fail((xhr) => notifyError(xhr));
            return;
        }

        const deleteButton = event.target.closest('[data-opening-balance-delete]');
        if (deleteButton && await confirmAction({
            title: 'Hapus Kas Awal?',
            text: 'Saldo pada periode ini dan periode setelahnya akan dihitung ulang.',
            confirmText: 'Ya, hapus',
        })) {
            requestAction({
                url: `${module.dataset.baseUrl}/${deleteButton.dataset.openingBalanceDelete}`,
                success: () => table.ajax.reload(null, false),
            });
        }
    });
}
