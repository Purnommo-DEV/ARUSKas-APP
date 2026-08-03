import { $, clearErrors, displayErrors, responseMessage, toastr } from '../utils/ajax';

export function initializeLogin() {
    const form = document.querySelector('#login-form');
    if (!form) return;

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        clearErrors(form);
        const button = form.querySelector('[type="submit"]');
        button.disabled = true;
        button.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Memproses...';

        $.post(form.action, $(form).serialize())
            .done((response) => {
                toastr.success(response.message);
                window.location.href = response.redirect;
            })
            .fail((xhr) => {
                displayErrors(form, xhr.responseJSON?.errors);
                toastr.error(responseMessage(xhr, 'Login gagal.'));
                button.disabled = false;
                button.textContent = 'Masuk ke ARUSKas';
            });
    });
}
