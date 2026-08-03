export function initializePwa() {
    const installButton = document.querySelector('#pwa-install');
    const iosInstallDialog = document.querySelector('#pwa-ios-install-dialog');
    let deferredPrompt = null;
    const userAgent = window.navigator.userAgent.toLowerCase();
    const isIos = /iphone|ipad|ipod/.test(userAgent)
        || (window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1);
    const isIosSafari = isIos
        && /safari/.test(userAgent)
        && !/crios|fxios|edgios|opios/.test(userAgent);
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;

    const toggleInstallButton = (visible) => installButton?.classList.toggle('hidden', !visible);

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {
                // PWA registration can fail on non-secure local environments without affecting the app.
            });
        });
    }

    if (isStandalone) {
        toggleInstallButton(false);

        return;
    }

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;
        toggleInstallButton(true);
    });

    installButton?.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            toggleInstallButton(false);

            return;
        }

        if (isIosSafari) iosInstallDialog?.showModal();
    });

    if (isIosSafari) toggleInstallButton(true);

    window.addEventListener('appinstalled', () => toggleInstallButton(false));
}
