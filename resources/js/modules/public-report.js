import { $, notifyError } from '../utils/ajax';
import { escapeHtml, formatDate, formatRupiah } from '../utils/format';

export function initializePublicReport() {
    const module = document.querySelector('#public-report-module');
    if (!module) return;

    const monthFilter = module.querySelector('#public-month-filter');
    const yearFilter = module.querySelector('#public-year-filter');
    const incomeEmpty = module.querySelector('[data-public-income-empty]');
    const incomeContent = module.querySelector('[data-public-income-content]');
    const incomeTable = module.querySelector('[data-public-income-table]');
    const incomeMobile = module.querySelector('[data-public-income-mobile]');
    const proofModal = document.querySelector('#proof-modal');
    const proofImage = document.querySelector('#proof-modal-image');

    const proofButton = (url) => url
        ? `<button type="button" class="btn btn-ghost btn-xs text-blue-600" data-public-income-proof-url="${escapeHtml(url)}">Lihat Bukti</button>`
        : '<span class="text-slate-300">—</span>';

    const renderIncomeTransactions = (transactions = []) => {
        const hasIncome = transactions.length > 0;
        incomeEmpty?.classList.toggle('hidden', hasIncome);
        incomeContent?.classList.toggle('hidden', !hasIncome);

        if (!hasIncome) {
            if (incomeTable) incomeTable.innerHTML = '';
            if (incomeMobile) incomeMobile.innerHTML = '';
            return;
        }

        if (incomeTable) {
            incomeTable.innerHTML = transactions.map((transaction) => `
                <tr class="border-b border-gray-100 transition hover:bg-emerald-50/40">
                    <td class="whitespace-nowrap px-5 py-3.5 font-semibold text-slate-700">${formatDate(transaction.transaction_date)}</td>
                    <td class="whitespace-nowrap px-5 py-3.5">${escapeHtml(transaction.category_name)}</td>
                    <td class="whitespace-nowrap px-5 py-3.5"><span class="badge border-blue-100 bg-blue-50 text-blue-700">${escapeHtml(transaction.payment_method)}</span></td>
                    <td class="px-5 py-3.5 text-slate-500">${transaction.notes ? escapeHtml(transaction.notes) : '—'}</td>
                    <td class="whitespace-nowrap px-5 py-3.5 text-right font-black text-emerald-700">${formatRupiah(transaction.amount)}</td>
                    <td class="whitespace-nowrap px-5 py-3.5">${proofButton(transaction.proof_url)}</td>
                </tr>`).join('');
        }

        if (incomeMobile) {
            incomeMobile.innerHTML = transactions.map((transaction) => `
                <article class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-400">${formatDate(transaction.transaction_date)}</p>
                            <p class="mt-1 font-bold text-slate-800">${escapeHtml(transaction.category_name)}</p>
                        </div>
                        <p class="whitespace-nowrap text-base font-black text-emerald-700">${formatRupiah(transaction.amount)}</p>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                        <span class="badge border-blue-100 bg-blue-50 text-blue-700">${escapeHtml(transaction.payment_method)}</span>
                        ${proofButton(transaction.proof_url)}
                    </div>
                    ${transaction.notes ? `<p class="mt-3 text-xs leading-5 text-slate-500">${escapeHtml(transaction.notes)}</p>` : ''}
                </article>`).join('');
        }
    };

    const refreshPublicReport = () => {
        if (!monthFilter?.value || !yearFilter?.value) return;

        $.get(module.dataset.summaryUrl, {
            month: monthFilter.value,
            year: yearFilter.value,
        })
            .done(({ data }) => {
                module.querySelector('[data-summary="period"]')?.replaceChildren(data.period_label);
                document.querySelector('[data-public-period]')?.replaceChildren(`Periode ${data.period_label}`);
                module.querySelector('[data-public-income-period]')?.replaceChildren(data.period_label);
                module.querySelector('[data-summary="opening"]')?.replaceChildren(formatRupiah(data.opening_balance));
                module.querySelector('[data-summary="cash-in"]')?.replaceChildren(formatRupiah(data.income_total ?? data.cash_in));
                module.querySelector('[data-summary="cash-out"]')?.replaceChildren(formatRupiah(data.cash_out));
                module.querySelector('[data-summary="closing"]')?.replaceChildren(formatRupiah(data.closing_balance));
                renderIncomeTransactions(data.income_transactions);
            })
            .fail((xhr) => notifyError(xhr));
    };

    [monthFilter, yearFilter].forEach((filter) => filter?.addEventListener('change', refreshPublicReport));

    module.addEventListener('click', (event) => {
        const proofButtonElement = event.target.closest('[data-public-income-proof-url]');
        if (!proofButtonElement || !proofModal || !proofImage) return;

        proofImage.src = proofButtonElement.dataset.publicIncomeProofUrl;
        proofModal.showModal();
    });
}
