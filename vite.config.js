import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        VitePWA({
            injectRegister: false,
            registerType: 'autoUpdate',
            buildBase: '/build/',
            includeAssets: [
                'favicon.svg',
                'icons/icon-192.png',
                'icons/icon-512.png',
                'icons/icon-maskable-512.png',
                'icons/splash-1170x2532.png',
                'offline.html',
            ],
            manifest: {
                name: 'ARUSKas — Keuangan Kajian',
                short_name: 'ARUSKas',
                description: 'Pencatatan dan laporan keuangan kajian yang transparan.',
                lang: 'id',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                orientation: 'portrait',
                theme_color: '#0f5fa9',
                background_color: '#eff6ff',
                icons: [
                    { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
                    { src: '/icons/icon-maskable-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' },
                ],
            },
            workbox: {
                navigateFallback: '/offline.html',
                navigateFallbackDenylist: [/^\/admin\//, /^\/user\//, /^\/login/, /^\/logout/, /^\/storage\//],
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                inlineWorkboxRuntime: true,
                globPatterns: ['**/*.{js,css,html,svg,png,webp,woff2}'],
                additionalManifestEntries: [
                    { url: '/offline.html', revision: null },
                    { url: '/manifest.webmanifest', revision: null },
                    { url: '/icons/icon-192.png', revision: null },
                    { url: '/icons/icon-512.png', revision: null },
                    { url: '/icons/icon-maskable-512.png', revision: null },
                    { url: '/icons/splash-1170x2532.png', revision: null },
                ],
            },
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
