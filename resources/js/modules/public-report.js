import { $, notifyError } from '../utils/ajax';
import { formatRupiah } from '../utils/format';

export function initializePublicReport() {
    const module = document.querySelector('#public-report-module');
    if (!module) return;

    const monthFilter = module.querySelector('#public-month-filter');
    const yearFilter = module.querySelector('#public-year-filter');

    const refreshSummary = () => {
        if (!monthFilter?.value || !yearFilter?.value) return;

        $.get(module.dataset.summaryUrl, {
            month: monthFilter.value,
            year: yearFilter.value,
        })
            .done(({ data }) => {
                module.querySelector('[data-summary="period"]')?.replaceChildren(data.period_label);
                document.querySelector('[data-public-period]')?.replaceChildren(`Periode ${data.period_label}`);
                module.querySelector('[data-summary="opening"]')?.replaceChildren(formatRupiah(data.opening_balance));
                module.querySelector('[data-summary="cash-in"]')?.replaceChildren(formatRupiah(data.cash_in));
                module.querySelector('[data-summary="cash-out"]')?.replaceChildren(formatRupiah(data.cash_out));
                module.querySelector('[data-summary="closing"]')?.replaceChildren(formatRupiah(data.closing_balance));
            })
            .fail((xhr) => notifyError(xhr));
    };

    [monthFilter, yearFilter].forEach((filter) => filter?.addEventListener('change', refreshSummary));
}
