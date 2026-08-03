import $ from 'jquery';
import Swal from 'sweetalert2';
import toastr from 'toastr';

export function initializeAjax() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            Accept: 'application/json',
        },
    });

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3500,
    };
}

export function clearErrors(form) {
    form.querySelectorAll('[data-error-for]').forEach((element) => {
        element.textContent = '';
    });

    form.querySelectorAll('.input-error').forEach((element) => {
        element.classList.remove('input-error', 'border-red-400');
    });
}

export function displayErrors(form, errors = {}) {
    clearErrors(form);

    Object.entries(errors).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        const error = form.querySelector(`[data-error-for="${field}"]`);
        input?.classList.add('input-error', 'border-red-400');
        if (error) error.textContent = Array.isArray(messages) ? messages[0] : messages;
    });
}

export function responseMessage(xhr, fallback = 'Terjadi kesalahan. Silakan coba kembali.') {
    const errors = xhr.responseJSON?.errors;
    if (errors) return Object.values(errors).flat()[0] ?? fallback;

    return xhr.responseJSON?.message ?? fallback;
}

export function notifyError(xhr, form = null) {
    if (xhr.status === 422 && form) displayErrors(form, xhr.responseJSON?.errors);
    toastr.error(responseMessage(xhr));
}

export function submitAjax(form, { url, method = 'POST', onSuccess }) {
    clearErrors(form);
    const button = form.querySelector('[type="submit"]');
    const original = button?.innerHTML;
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Menyimpan...';
    }

    const data = new FormData(form);
    if (!['GET', 'POST'].includes(method.toUpperCase())) data.append('_method', method.toUpperCase());

    $.ajax({
        url,
        method: 'POST',
        data,
        processData: false,
        contentType: false,
    })
        .done((response) => {
            toastr.success(response.message);
            onSuccess?.(response);
        })
        .fail((xhr) => notifyError(xhr, form))
        .always(() => {
            if (button) {
                button.disabled = false;
                button.innerHTML = original;
            }
        });
}

export async function confirmAction({ title, text, confirmText = 'Ya, lanjutkan', icon = 'warning' }) {
    const result = await Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'btn btn-primary ml-2',
            cancelButton: 'btn border-gray-200 bg-white text-slate-600',
        },
        buttonsStyling: false,
    });

    return result.isConfirmed;
}

export function requestAction({ url, method = 'DELETE', success }) {
    $.ajax({ url, method, data: method === 'POST' ? {} : undefined })
        .done((response) => {
            toastr.success(response.message);
            success?.(response);
        })
        .fail((xhr) => notifyError(xhr));
}

export { $, Swal, toastr };
