import AutoNumeric from 'autonumeric';
import { $, confirmAction, notifyError, requestAction, submitAjax } from '../utils/ajax';
import { createDataTable } from '../utils/datatable';
import { escapeHtml, formatDate, formatRupiah, weekPeriod } from '../utils/format';

const methodLabel = { transfer: 'Transfer', qris: 'QRIS', cash: 'Cash' };

export function initializeTransactions() {
    const module = document.querySelector('#transactions-module');
    if (!module) return;

    const readOnly = module.dataset.readOnly === 'true';
    const form = document.querySelector('#transaction-form');
    const modal = document.querySelector('#transaction-modal');
    const modalTitle = document.querySelector('#transaction-modal-title');
    const monthFilter = document.querySelector('#transaction-month-filter');
    const yearFilter = document.querySelector('#transaction-year-filter');
    const proofModal = document.querySelector('#proof-modal');
    const proofModalImage = document.querySelector('#proof-modal-image');
    let openingBalance = 0;

    const columns = [
        { data: 'transaction_date', name: 'transaction_date', render: (data) => `<span class="font-semibold text-slate-700">${formatDate(data)}</span>` },
        { data: 'payment_method', name: 'payment_method', render: (data) => `<span class="badge border-blue-100 bg-blue-50 text-blue-700">${methodLabel[data] ?? escapeHtml(data)}</span>` },
        { data: 'category_name', name: 'category_name', render: escapeHtml },
        { data: 'party_name', name: 'party_name', render: escapeHtml },
        {
            data: 'amount',
            name: 'amount',
            className: 'text-right',
            render: (data, type, row) => row.transaction_type === 'income'
                ? `<span class="font-bold text-emerald-600">${formatRupiah(data)}</span>`
                : '<span class="text-slate-300">−</span>',
        },
        {
            data: 'amount',
            name: 'amount',
            className: 'text-right',
            render: (data, type, row) => row.transaction_type === 'expense'
                ? `<span class="font-bold text-red-600">${formatRupiah(data)}</span>`
                : '<span class="text-slate-300">−</span>',
        },
        {
            data: 'running_balance',
            searchable: false,
            orderable: false,
            className: 'text-right',
            render: (data) => `<span class="font-black ${Number(data) >= 0 ? 'text-slate-700' : 'text-red-600'}">${formatRupiah(data)}</span>`,
        },
        {
            data: 'proof_url',
            searchable: false,
            orderable: false,
            render: (url) => url
                ? `<button type="button" class="btn btn-ghost btn-xs text-blue-600" data-proof-url="${escapeHtml(url)}">Lihat</button>`
                : '<span class="text-slate-300">−</span>',
        },
    ];

    if (!readOnly) {
        columns.push({
            data: null,
            searchable: false,
            orderable: false,
            render: (_, __, row) => `
                <div class="flex items-center justify-end gap-1">
                    <button class="btn btn-ghost btn-sm text-blue-600" data-transaction-edit="${row.id}">Edit</button>
                    <button class="btn btn-ghost btn-sm text-red-500" data-transaction-delete="${row.id}">Hapus</button>
                </div>`,
        });
    }

    const table = createDataTable('#transactions-table', {
        ajax: {
            url: module.dataset.dataUrl,
            data: (data) => {
                if (monthFilter?.value && yearFilter?.value) {
                    data.month = monthFilter.value;
                    data.year = yearFilter.value;
                }
            },
            dataSrc: (json) => {
                openingBalance = Number(json.opening_balance ?? 0);

                return json.data;
            },
        },
        ordering: false,
        order: [],
        columns,
        drawCallback() {
            const api = this.api();
            const tableElement = api.table().node();
            const body = tableElement.tBodies[0];
            const pageInfo = api.page.info();

            body?.querySelectorAll('[data-opening-balance-row]').forEach((row) => row.remove());
            if (!body || pageInfo.page !== 0 || api.rows({ page: 'current' }).count() === 0) return;

            const actionCell = readOnly ? '' : '<td class="text-right text-slate-300">−</td>';
            body.insertAdjacentHTML('afterbegin', `
                <tr data-opening-balance-row class="!bg-blue-50/70 hover:!bg-blue-50">
                    <td class="text-slate-400">−</td>
                    <td class="text-slate-400">−</td>
                    <td><span class="font-bold text-blue-700">Saldo Awal Periode</span></td>
                    <td class="text-slate-400">−</td>
                    <td class="text-right text-slate-300">−</td>
                    <td class="text-right text-slate-300">−</td>
                    <td class="text-right"><span class="font-black text-blue-700">${formatRupiah(openingBalance)}</span></td>
                    <td class="text-slate-300">−</td>
                    ${actionCell}
                </tr>
            `);
        },
    });

    const refreshSummary = () => {
        if (!module.dataset.summaryUrl || !monthFilter?.value || !yearFilter?.value) return;

        $.get(module.dataset.summaryUrl, { month: monthFilter.value, year: yearFilter.value })
            .done(({ data }) => {
                document.querySelector('[data-summary="period"]')?.replaceChildren(data.period_label);
                document.querySelector('[data-summary="opening"]')?.replaceChildren(formatRupiah(data.opening_balance));
                document.querySelector('[data-summary="cash-in"]')?.replaceChildren(formatRupiah(data.cash_in));
                document.querySelector('[data-summary="cash-out"]')?.replaceChildren(formatRupiah(data.cash_out));
                document.querySelector('[data-summary="closing"]')?.replaceChildren(formatRupiah(data.closing_balance));
            })
            .fail((xhr) => notifyError(xhr));
    };

    [monthFilter, yearFilter].forEach((filter) => filter?.addEventListener('change', () => {
        table.ajax.reload();
        refreshSummary();
    }));

    module.addEventListener('click', (event) => {
        const proofButton = event.target.closest('[data-proof-url]');
        if (!proofButton) return;

        proofModalImage.src = proofButton.dataset.proofUrl;
        proofModal.showModal();
    });

    if (readOnly || !form || !modal) return;

    const dateInput = form.querySelector('[name="transaction_date"]');
    const categoryInput = form.querySelector('[name="category_id"]');
    const amountInput = form.querySelector('[name="amount"]');
    const amountDisplay = document.querySelector('#amount_display');
    const weekOutput = document.querySelector('#week-period-preview');
    const typeOutput = document.querySelector('#category-type-preview');
    const proofInput = form.querySelector('[name="proof"]');
    const removeProofInput = form.querySelector('[name="remove_proof"]');
    const proofPreview = document.querySelector('#proof-edit-preview');
    const proofImage = document.querySelector('#proof-edit-image');
    const proofLabel = document.querySelector('#proof-edit-label');
    const quickCategoryModal = document.querySelector('#quick-category-modal');
    const quickCategoryForm = document.querySelector('#quick-category-form');
    let originalProofUrl = null;
    let selectedProofUrl = null;

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

    const updatePreview = () => {
        weekOutput.textContent = weekPeriod(dateInput.value);
        const type = categoryInput.selectedOptions[0]?.dataset.type;
        typeOutput.textContent = type === 'income' ? 'Pemasukan' : type === 'expense' ? 'Pengeluaran' : 'Pilih kategori';
        typeOutput.className = `badge ${type === 'income' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : type === 'expense' ? 'border-red-100 bg-red-50 text-red-700' : 'badge-ghost'}`;
    };

    const clearSelectedProofUrl = () => {
        if (selectedProofUrl) URL.revokeObjectURL(selectedProofUrl);
        selectedProofUrl = null;
    };

    const showProofPreview = (url, label = 'Bukti saat ini') => {
        proofImage.src = url;
        proofLabel.textContent = label;
        proofPreview.classList.remove('hidden');
        proofPreview.classList.add('flex');
    };

    const hideProofPreview = () => {
        proofImage.src = '';
        proofPreview.classList.add('hidden');
        proofPreview.classList.remove('flex');
    };

    const resetProof = () => {
        clearSelectedProofUrl();
        originalProofUrl = null;
        proofInput.value = '';
        removeProofInput.value = '0';
        hideProofPreview();
    };

    const openCreate = () => {
        form.reset();
        form.dataset.id = '';
        categoryInput.querySelectorAll('[data-temporary]').forEach((option) => option.remove());
        dateInput.value = module.dataset.today;
        amountFormatter.clear();
        amountInput.value = '';
        resetProof();
        modalTitle.textContent = 'Tambah Transaksi';
        updatePreview();
        modal.showModal();
    };

    document.querySelectorAll('[data-add-transaction]').forEach((button) => button.addEventListener('click', openCreate));
    dateInput.addEventListener('change', updatePreview);
    categoryInput.addEventListener('change', updatePreview);

    proofInput.addEventListener('change', () => {
        clearSelectedProofUrl();
        const file = proofInput.files[0];
        if (!file) {
            if (originalProofUrl) showProofPreview(originalProofUrl);
            else hideProofPreview();
            return;
        }

        removeProofInput.value = '0';
        selectedProofUrl = URL.createObjectURL(file);
        showProofPreview(selectedProofUrl, 'Bukti baru');
    });

    form.querySelector('[data-remove-proof]').addEventListener('click', () => {
        clearSelectedProofUrl();
        proofInput.value = '';
        removeProofInput.value = originalProofUrl ? '1' : '0';
        hideProofPreview();
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        syncAmount();
        const id = form.dataset.id;
        submitAjax(form, {
            url: id ? `${module.dataset.baseUrl}/${id}` : module.dataset.storeUrl,
            method: id ? 'PUT' : 'POST',
            onSuccess: () => {
                clearSelectedProofUrl();
                modal.close();
                table.ajax.reload(null, false);
                refreshSummary();
            },
        });
    });

    document.querySelector('[data-add-category]')?.addEventListener('click', () => {
        quickCategoryForm.reset();
        quickCategoryModal.showModal();
    });

    quickCategoryForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        submitAjax(quickCategoryForm, {
            url: module.dataset.categoryStoreUrl,
            onSuccess: ({ data }) => {
                const option = new Option(`${data.name} · ${data.type_label}`, data.id, true, true);
                option.dataset.type = data.type;
                categoryInput.append(option);
                categoryInput.value = data.id;
                updatePreview();
                quickCategoryModal.close();
            },
        });
    });

    module.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-transaction-edit]');
        if (editButton) {
            $.get(`${module.dataset.baseUrl}/${editButton.dataset.transactionEdit}`)
                .done(({ data }) => {
                    form.reset();
                    resetProof();
                    form.dataset.id = data.id;
                    dateInput.value = data.transaction_date;
                    form.querySelector('[name="payment_method"]').value = data.payment_method;
                    if (!categoryInput.querySelector(`option[value="${data.category_id}"]`)) {
                        const inactiveOption = new Option(
                            `${data.category_name} · ${data.category_type === 'income' ? 'Pemasukan' : 'Pengeluaran'} (nonaktif)`,
                            data.category_id,
                        );
                        inactiveOption.dataset.type = data.category_type;
                        inactiveOption.dataset.temporary = 'true';
                        categoryInput.append(inactiveOption);
                    }
                    categoryInput.value = data.category_id;
                    form.querySelector('[name="party_name"]').value = data.party_name;
                    form.querySelector('[name="notes"]').value = data.notes ?? '';
                    originalProofUrl = data.proof_url;
                    if (originalProofUrl) showProofPreview(originalProofUrl);
                    modalTitle.textContent = 'Edit Transaksi';
                    updatePreview();
                    modal.showModal();
                    setTimeout(() => {
                        if (form.dataset.id === String(data.id)) {
                            amountFormatter.set(String(data.amount));
                            syncAmount();
                        }
                    }, 0);
                })
                .fail((xhr) => notifyError(xhr));
            return;
        }

        const deleteButton = event.target.closest('[data-transaction-delete]');
        if (deleteButton && await confirmAction({
            title: 'Hapus transaksi?',
            text: 'Saldo akan dihitung ulang dan bukti transaksi ikut dihapus.',
            confirmText: 'Ya, hapus',
        })) {
            requestAction({
                url: `${module.dataset.baseUrl}/${deleteButton.dataset.transactionDelete}`,
                success: () => {
                    table.ajax.reload(null, false);
                    refreshSummary();
                },
            });
        }
    });
}
