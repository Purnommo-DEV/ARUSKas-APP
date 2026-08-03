import AutoNumeric from 'autonumeric';
import { confirmAction, submitAjax } from '../utils/ajax';

export function initializeSettings() {
    const form = document.querySelector('#settings-form');
    if (!form) return;

    const bindPreview = (inputName, targetId) => {
        const input = form.querySelector(`[name="${inputName}"]`);
        const target = document.querySelector(targetId);
        input?.addEventListener('change', () => {
            const file = input.files?.[0];
            if (file && target) {
                target.src = URL.createObjectURL(file);
                target.classList.remove('hidden');
                target.previousElementSibling?.classList.add('hidden');
            }
        });
    };

    bindPreview('logo', '#logo-preview');
    bindPreview('qris_image', '#qris-preview');

    const openingBalanceDisplay = document.querySelector('#opening_balance_display');
    const openingBalance = document.querySelector('#opening_balance');
    const openingBalanceChangeConfirmation = document.querySelector('#confirm_opening_balance_change');
    const changeOpeningBalanceButton = document.querySelector('#change-opening-balance');
    const openingBalanceHint = document.querySelector('#opening-balance-hint');
    const openingBalanceFormatter = openingBalanceDisplay ? new AutoNumeric(openingBalanceDisplay, {
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        decimalPlaces: 0,
        minimumValue: '0',
        maximumValue: '9000000000000000000',
        emptyInputBehavior: 'zero',
        leadingZero: 'deny',
        modifyValueOnWheel: false,
        selectOnFocus: false,
    }) : null;

    const syncOpeningBalance = () => {
        if (openingBalanceFormatter && openingBalance) {
            openingBalance.value = openingBalanceFormatter.getNumericString() || '0';
        }
    };

    if (openingBalanceFormatter && openingBalanceDisplay && openingBalance) {
        openingBalanceFormatter.set(String(openingBalanceDisplay.dataset.rawValue ?? openingBalance.value ?? '0'));
        syncOpeningBalance();
    }

    openingBalanceDisplay?.addEventListener('input', syncOpeningBalance);
    openingBalanceDisplay?.addEventListener('change', syncOpeningBalance);

    changeOpeningBalanceButton?.addEventListener('click', async () => {
        const confirmed = await confirmAction({
            title: 'Ubah Saldo Awal Kas?',
            text: 'Perubahan ini akan memengaruhi seluruh perhitungan kas, termasuk running balance dan saldo akhir.',
            confirmText: 'Ya, ubah',
        });

        if (!confirmed || !openingBalanceDisplay || !openingBalanceChangeConfirmation) return;

        openingBalanceDisplay.readOnly = false;
        openingBalanceChangeConfirmation.value = '1';
        openingBalanceDisplay.focus();
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        syncOpeningBalance();
        submitAjax(form, {
            url: form.action,
            onSuccess: ({ data }) => {
                if (data.logo_url) document.querySelector('#logo-preview').src = data.logo_url;
                if (data.qris_url) document.querySelector('#qris-preview').src = data.qris_url;
                form.querySelector('[name="logo"]').value = '';
                form.querySelector('[name="qris_image"]').value = '';
                if (openingBalanceFormatter && openingBalanceDisplay && openingBalance && openingBalanceChangeConfirmation) {
                    openingBalance.value = String(data.opening_balance);
                    openingBalanceDisplay.dataset.rawValue = String(data.opening_balance);
                    openingBalanceFormatter.set(String(data.opening_balance));
                    openingBalanceDisplay.readOnly = Boolean(data.opening_balance_set);
                    openingBalanceChangeConfirmation.value = '0';
                    changeOpeningBalanceButton?.classList.toggle('hidden', !data.opening_balance_set);
                    if (openingBalanceHint) {
                        openingBalanceHint.textContent = data.opening_balance_set
                            ? 'Saldo Awal Kas telah dikunci sebagai dasar seluruh perhitungan.'
                            : 'Isi Saldo Awal Kas untuk menetapkan dasar seluruh pembukuan.';
                    }
                }
            },
        });
    });
}
