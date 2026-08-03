<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f5fa9">
    <meta name="description" content="Laporan keuangan {{ $setting->study_name }} di {{ $setting->mosque_name }}. Transparansi pemasukan, pengeluaran, dan saldo kas kajian.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="ARUSKas">
    <meta property="og:url" content="{{ route('public.report') }}">
    <meta property="og:title" content="Laporan Keuangan {{ $setting->study_name }} | ARUSKas">
    <meta property="og:description" content="Transparansi keuangan {{ $setting->study_name }} di {{ $setting->mosque_name }}.">
    <meta property="og:image" content="{{ asset('icons/icon-512.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Laporan Keuangan {{ $setting->study_name }} | ARUSKas">
    <meta name="twitter:description" content="Transparansi keuangan kajian yang dapat diakses semua jamaah.">
    <meta name="twitter:image" content="{{ asset('icons/icon-512.png') }}">
    <title>Laporan Keuangan {{ $setting->study_name }} | ARUSKas</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.pwa-meta')
    @vite(['resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
    @php($months = config('finance.months'))
    <header class="border-b border-blue-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('public.report') }}" class="flex min-w-0 items-center gap-3">
                @if($setting->logo_path)
                    <img src="{{ asset('storage/'.$setting->logo_path) }}" alt="Logo {{ $setting->study_name }}" class="size-10 rounded-xl object-contain">
                @else
                    <img src="{{ asset('icons/icon-192.png') }}" alt="Logo ARUSKas" class="size-10 rounded-xl shadow-sm">
                @endif
                <span class="min-w-0">
                    <span class="block truncate text-base font-extrabold text-slate-800 sm:text-lg">{{ $setting->study_name }}</span>
                    <span class="block truncate text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">Laporan Keuangan Kajian</span>
                </span>
            </a>
            @auth
                <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-ghost btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
            @endauth
        </div>
    </header>

    <main id="public-report-module"
          data-summary-url="{{ route('public.report.summary') }}"
          class="mx-auto max-w-7xl space-y-7 px-4 py-7 pb-28 sm:px-6 lg:px-8 lg:py-10">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-500 px-5 py-8 text-white shadow-lg sm:px-8 sm:py-10">
            <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[.16em] text-blue-100">Laporan Keuangan Kajian</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $setting->study_name }}</h1>
                    <p class="mt-2 text-sm text-blue-100 sm:text-base">{{ $setting->mosque_name }}@if($setting->address) · {{ $setting->address }}@endif</p>
                </div>
                <div class="grid grid-cols-2 gap-3 rounded-2xl bg-white/10 p-3 backdrop-blur sm:flex">
                    <div>
                        <label for="public-month-filter" class="mb-1 block text-xs font-bold text-blue-100">Filter Bulan</label>
                        <select id="public-month-filter" class="select select-sm border-0 bg-white text-slate-700" aria-label="Filter bulan laporan publik">
                            @foreach($months as $number => $label)
                                <option value="{{ $number }}" @selected($number === now()->month)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="public-year-filter" class="mb-1 block text-xs font-bold text-blue-100">Filter Tahun</label>
                        <select id="public-year-filter" class="select select-sm border-0 bg-white text-slate-700" aria-label="Filter tahun laporan publik">
                            @foreach($years as $item)
                                <option value="{{ $item }}" @selected($item === now()->year)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start">
            <div class="space-y-5">
                <div>
                    <p class="text-sm font-semibold text-blue-600">Ringkasan periode</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-800" data-summary="period">{{ $summary['period_label'] }}</h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <article class="surface-card p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kas Awal</p>
                        <p class="mt-2 text-xl font-black text-slate-800" data-summary="opening">Rp {{ number_format($summary['opening_balance'], 0, ',', '.') }}</p>
                    </article>
                    <article class="surface-card p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Cash In</p>
                        <p class="mt-2 text-xl font-black text-emerald-600" data-summary="cash-in">Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
                    </article>
                    <article class="surface-card p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Cash Out</p>
                        <p class="mt-2 text-xl font-black text-red-500" data-summary="cash-out">Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
                    </article>
                    <article class="rounded-2xl bg-gradient-to-br from-blue-700 to-blue-900 p-5 text-white shadow-lg">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-100">Sisa Saldo Kas</p>
                        <p class="mt-2 text-2xl font-black sm:text-3xl" data-summary="closing">Rp {{ number_format($summary['closing_balance'], 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs text-blue-100">Kas Awal + Cash In − Cash Out</p>
                    </article>
                </div>
            </div>

            <aside id="donasi" class="surface-card overflow-hidden p-5 sm:p-6">
                <p class="text-sm font-bold text-blue-600">Dukung Kajian</p>
                <h2 class="mt-1 text-xl font-black text-slate-800">Donasi melalui QRIS</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Scan QRIS berikut untuk ikut mendukung kegiatan kajian. Semoga menjadi amal jariyah.</p>
                <div class="mt-5 grid min-h-56 place-items-center rounded-2xl bg-slate-50 p-4">
                    @if($setting->qris_image_path)
                        <img src="{{ asset('storage/'.$setting->qris_image_path) }}" alt="QRIS donasi {{ $setting->study_name }}" class="max-h-72 max-w-full rounded-xl object-contain">
                    @else
                        <p class="text-center text-sm text-slate-400">QRIS donasi akan ditampilkan di sini.</p>
                    @endif
                </div>
            </aside>
        </section>

        <section class="grid gap-5 sm:grid-cols-2">
            <article class="surface-card p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Ucapan</p>
                <p class="mt-3 text-xl font-black text-slate-800">{{ $setting->thanks_message ?: 'Jazakumullahu Khairan' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $setting->blessing_message ?: 'Baarakallahu Fiikum' }}</p>
            </article>
            <article class="surface-card p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Konfirmasi Donasi</p>
                @if($setting->confirmation_phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->confirmation_phone) }}" class="mt-3 inline-block text-xl font-black text-blue-700">{{ $setting->confirmation_phone }}</a>
                @else
                    <p class="mt-3 text-sm text-slate-400">Nomor konfirmasi belum ditentukan.</p>
                @endif
            </article>
        </section>
    </main>

    <button id="pwa-install" type="button" class="public-install-button hidden" aria-label="Install aplikasi ARUSKas">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Install Aplikasi</span>
    </button>

    <dialog id="pwa-ios-install-dialog" class="modal">
        <div class="modal-box max-w-md overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-4">
                <p class="text-sm font-bold text-blue-600">Install di iPhone</p>
                <h2 class="mt-1 text-xl font-black text-slate-800">Tambahkan ARUSKas ke Home Screen</h2>
            </div>
            <div class="space-y-4 px-5 py-5 text-sm leading-6 text-slate-600">
                <p>Untuk menginstal aplikasi di iPhone, tekan tombol <strong>Share</strong> di Safari, lalu pilih <strong>Add to Home Screen</strong>.</p>
                <p class="rounded-xl bg-blue-50 p-3 text-xs text-blue-700">Setelah ditambahkan, ARUSKas dapat dibuka seperti aplikasi biasa tanpa address bar.</p>
            </div>
            <div class="flex justify-end border-t border-gray-100 px-5 py-4">
                <button type="button" class="btn btn-primary" data-modal-close>Mengerti</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
    </dialog>
</body>
</html>
