export function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value ?? 0));
}

export function formatDate(value) {
    if (!value) return '-';
    const [year, month, day] = String(value).substring(0, 10).split('-');
    return `${day}/${month}/${year}`;
}

export function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}

export function weekPeriod(dateValue) {
    if (!dateValue) return '-';
    const date = new Date(`${dateValue}T00:00:00`);
    const day = date.getDay() || 7;
    date.setDate(date.getDate() - day + 1);
    const end = new Date(date);
    end.setDate(end.getDate() + 6);
    const localized = (dateItem) => dateItem.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });

    return `${localized(date)} - ${localized(end)}`;
}
