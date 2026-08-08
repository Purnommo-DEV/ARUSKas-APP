import './bootstrap';
import '../css/app.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'toastr/build/toastr.min.css';
import $ from 'jquery';
import { initializeAjax } from './utils/ajax';
import { initializeCategories } from './modules/categories';
import { initializeTransactions } from './modules/transactions';
import { initializeUsers } from './modules/users';
import { initializeSettings } from './modules/settings';
import { initializeOpeningBalances } from './modules/opening-balances';
import { initializeLogin } from './modules/login';
import { initializePublicReport } from './modules/public-report';
import { initializePwa } from './pwa';

window.$ = window.jQuery = $;
initializeAjax();

function initializeDialogs() {
    const syncScrollLock = () => {
        document.documentElement.classList.toggle('overflow-hidden', document.querySelectorAll('dialog[open]').length > 0);
    };

    document.querySelectorAll('dialog').forEach((dialog) => {
        if (typeof dialog.showModal !== 'function') {
            dialog.showModal = () => {
                dialog.setAttribute('open', '');
                dialog.classList.add('modal-open');
                syncScrollLock();
            };
            dialog.close = () => {
                dialog.removeAttribute('open');
                dialog.classList.remove('modal-open');
                syncScrollLock();
            };
        } else if (typeof dialog.close !== 'function') {
            dialog.close = () => {
                dialog.removeAttribute('open');
                syncScrollLock();
            };
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeDialogs();
    const sidebar = document.querySelector('#app-sidebar');
    const overlay = document.querySelector('#sidebar-overlay');
    const toggleSidebar = (show) => {
        sidebar?.classList.toggle('-translate-x-full', !show);
        overlay?.classList.toggle('hidden', !show);
    };

    document.querySelector('#sidebar-toggle')?.addEventListener('click', () => toggleSidebar(true));
    document.querySelector('#sidebar-close')?.addEventListener('click', () => toggleSidebar(false));
    overlay?.addEventListener('click', () => toggleSidebar(false));

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });

    initializeLogin();
    initializeCategories();
    initializeTransactions();
    initializeUsers();
    initializeSettings();
    initializeOpeningBalances();
    initializePublicReport();
    initializePwa();
});
