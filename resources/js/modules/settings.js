import { submitAjax } from '../utils/ajax';

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

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitAjax(form, {
            url: form.action,
            onSuccess: ({ data }) => {
                if (data.logo_url) document.querySelector('#logo-preview').src = data.logo_url;
                if (data.qris_url) document.querySelector('#qris-preview').src = data.qris_url;
                form.querySelector('[name="logo"]').value = '';
                form.querySelector('[name="qris_image"]').value = '';
            },
        });
    });
}
