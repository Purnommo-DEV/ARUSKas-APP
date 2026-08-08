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
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-start gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('public.report') }}" class="flex min-w-0 items-start gap-3">
                @if($setting->logo_path)
                    <img src="{{ asset('storage/'.$setting->logo_path) }}" alt="Logo {{ $setting->study_name }}" class="mt-1 size-10 shrink-0 rounded-xl object-contain">
                @else
                    <img src="{{ asset('icons/icon-192.png') }}" alt="Logo ARUSKas" class="mt-1 size-10 shrink-0 rounded-xl shadow-sm">
                @endif
                <span class="min-w-0">
                    <span class="block text-[11px] font-bold uppercase tracking-[.16em] text-blue-600">Laporan Keuangan</span>
                    <span class="mt-0.5 block truncate text-lg font-black tracking-tight text-slate-800 sm:text-xl">{{ $setting->study_name }}</span>
                    <span class="mt-0.5 block text-xs font-semibold text-slate-500" data-public-period>Periode {{ $summary['period_label'] }}</span>
                    <span class="mt-0.5 block truncate text-xs text-slate-400">{{ $setting->mosque_name }}</span>
                </span>
            </a>
        </div>
    </header>

    <main id="public-report-module"
          data-summary-url="{{ route('public.report.summary') }}"
          class="mx-auto max-w-7xl space-y-7 px-4 pb-28 pt-6 sm:px-6 lg:px-8 lg:pt-8">
        <section class="surface-card flex flex-col gap-4 p-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-600">Transparansi keuangan kajian</p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-800">Ringkasan Keuangan</h1>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:w-auto">
                <div>
                    <label for="public-month-filter" class="form-label">Filter Bulan</label>
                    <select id="public-month-filter" class="select" aria-label="Filter bulan laporan publik">
                        @foreach($months as $number => $label)
                            <option value="{{ $number }}" @selected($number === now()->month)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="public-year-filter" class="form-label">Filter Tahun</label>
                    <select id="public-year-filter" class="select" aria-label="Filter tahun laporan publik">
                        @foreach($years as $item)
                            <option value="{{ $item }}" @selected($item === now()->year)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start">
            <div class="space-y-5">
                <div>
                    <p class="text-sm font-semibold text-blue-600">Ringkasan periode</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-800" data-summary="period">{{ $summary['period_label'] }}</h2>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h.01M9 13h.01M9 17h.01M15 15h.01M15 19h.01"/></svg></span>
                            <p class="text-xs font-bold uppercase tracking-wide text-blue-700">Kas Awal</p>
                        </div>
                        <div class="mt-3">
                            <p class="text-xl font-black tracking-tight text-blue-700" data-summary="opening">Rp {{ number_format($summary['opening_balance'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[11px] font-medium text-blue-700/70">Saldo awal periode</p>
                        </div>
                    </article>

                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/><path d="M5 19h14"/></svg></span>
                            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Pemasukan</p>
                        </div>
                        <div class="mt-3">
                            <p class="text-xl font-black tracking-tight text-emerald-700" data-summary="cash-in">Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[11px] font-medium text-emerald-700/70">Total pemasukan</p>
                        </div>
                    </article>

                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-rose-100 text-rose-700"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/><path d="M5 5h14"/></svg></span>
                            <p class="text-xs font-bold uppercase tracking-wide text-rose-700">Pengeluaran</p>
                        </div>
                        <div class="mt-3">
                            <p class="text-xl font-black tracking-tight text-rose-700" data-summary="cash-out">Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[11px] font-medium text-rose-700/70">Total pengeluaran</p>
                        </div>
                    </article>

                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-slate-800 bg-gradient-to-br from-blue-900 to-slate-800 p-4 text-white shadow-sm transition-shadow duration-200 hover:shadow-md">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-white/15 text-white"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 7V6a2 2 0 0 0-2-2H5a3 3 0 0 0 0 6h15v8a2 2 0 0 1-2 2H5a3 3 0 0 1-3-3V7"/><path d="M16 14h.01"/></svg></span>
                            <p class="text-xs font-bold uppercase tracking-wide text-white">Saldo Kas</p>
                        </div>
                        <div class="mt-3">
                            <p class="text-xl font-black tracking-tight text-white" data-summary="closing">Rp {{ number_format($summary['closing_balance'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[11px] font-medium text-blue-100">Kas tersedia saat ini</p>
                        </div>
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

        <section id="detail-pemasukan" class="surface-card overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-700">Transparansi infak dan donasi</p>
                    <h2 class="mt-1 text-xl font-black text-slate-800">Detail Pemasukan</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-400">Catatan pemasukan pada periode yang dipilih, diurutkan dari transaksi terbaru.</p>
                </div>
                <span class="badge border-emerald-200 bg-emerald-50 text-emerald-700" data-public-income-period>{{ $summary['period_label'] }}</span>
            </div>

            <div data-public-income-empty class="{{ $incomeTransactions->isNotEmpty() ? 'hidden' : '' }} px-5 py-12 text-center">
                <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-emerald-50 text-emerald-700">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/><path d="M5 19h14"/></svg>
                </span>
                <p class="mt-4 text-sm font-bold text-slate-700">Belum ada pemasukan pada periode ini.</p>
            </div>

            <div data-public-income-content class="{{ $incomeTransactions->isEmpty() ? 'hidden' : '' }}">
                <div class="hidden overflow-x-auto sm:block">
                    <table class="w-full text-sm">
                        <thead class="border-y border-gray-100 bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="whitespace-nowrap px-5 py-3">Tanggal</th>
                                <th class="whitespace-nowrap px-5 py-3">Kategori</th>
                                <th class="whitespace-nowrap px-5 py-3">Metode Transaksi</th>
                                <th class="px-5 py-3">Keterangan</th>
                                <th class="whitespace-nowrap px-5 py-3 text-right">Nominal</th>
                                <th class="whitespace-nowrap px-5 py-3">Bukti</th>
                            </tr>
                        </thead>
                        <tbody data-public-income-table>
                            @foreach($incomeTransactions as $incomeTransaction)
                                <tr class="border-b border-gray-100 transition hover:bg-emerald-50/40">
                                    <td class="whitespace-nowrap px-5 py-3.5 font-semibold text-slate-700">{{ $incomeTransaction->transaction_date->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5">{{ $incomeTransaction->category->name }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5"><span class="badge border-blue-100 bg-blue-50 text-blue-700">{{ $incomeTransaction->payment_method->label() }}</span></td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $incomeTransaction->notes ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right font-black text-emerald-700">Rp {{ number_format($incomeTransaction->amount, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        @if($incomeTransaction->proof_path)
                                            <button type="button" class="btn btn-ghost btn-xs text-blue-600" data-public-income-proof-url="{{ asset('storage/'.$incomeTransaction->proof_path) }}">Lihat Bukti</button>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 p-4 sm:hidden" data-public-income-mobile>
                    @foreach($incomeTransactions as $incomeTransaction)
                        <article class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold text-slate-400">{{ $incomeTransaction->transaction_date->format('d/m/Y') }}</p>
                                    <p class="mt-1 font-bold text-slate-800">{{ $incomeTransaction->category->name }}</p>
                                </div>
                                <p class="whitespace-nowrap text-base font-black text-emerald-700">Rp {{ number_format($incomeTransaction->amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                <span class="badge border-blue-100 bg-blue-50 text-blue-700">{{ $incomeTransaction->payment_method->label() }}</span>
                                @if($incomeTransaction->proof_path)
                                    <button type="button" class="btn btn-ghost btn-xs text-blue-600" data-public-income-proof-url="{{ asset('storage/'.$incomeTransaction->proof_path) }}">Lihat Bukti</button>
                                @endif
                            </div>
                            @if($incomeTransaction->notes)
                                <p class="mt-3 text-xs leading-5 text-slate-500">{{ $incomeTransaction->notes }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-5 sm:grid-cols-2">
            <article class="surface-card p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Ucapan</p>
                <p class="mt-3 text-xl font-black text-slate-800">{{ $setting->thanks_message ?: 'Jazakumullahu Khairan' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $setting->blessing_message ?: 'Baarakallahu Fiikum' }}</p>
            </article>
            <article class="surface-card p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Konfirmasi Donasi</p>
                @if($setting->whatsapp_number)
                    <a href="https://wa.me/{{ $setting->whatsapp_number }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-3 rounded-xl bg-emerald-50 px-4 py-3 text-emerald-700 transition hover:bg-emerald-100">
                        <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.94 9.94 0 0 0-8.57 14.99L2 22l5.16-1.35A9.97 9.97 0 1 0 12.04 2Zm0 18.16a8.17 8.17 0 0 1-4.16-1.14l-.3-.18-3.06.8.82-2.98-.2-.31a8.16 8.16 0 1 1 6.9 3.81Zm4.47-6.13c-.25-.13-1.48-.73-1.71-.81-.23-.08-.4-.13-.57.13-.17.25-.65.81-.8.98-.15.17-.3.19-.55.06a6.7 6.7 0 0 1-1.97-1.21 7.4 7.4 0 0 1-1.37-1.7c-.14-.25-.02-.39.1-.51.11-.11.25-.3.38-.45.13-.15.17-.25.25-.42.08-.17.04-.32-.02-.45-.06-.13-.57-1.37-.78-1.88-.2-.49-.41-.42-.57-.42h-.48c-.17 0-.45.06-.68.32-.23.25-.89.87-.89 2.13 0 1.26.91 2.47 1.04 2.64.13.17 1.8 2.74 4.35 3.85.61.26 1.08.42 1.45.54.61.19 1.17.16 1.61.1.49-.07 1.48-.6 1.69-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.3Z"/></svg>
                        <span>
                            <span class="block text-xs font-bold">Chat via WhatsApp</span>
                            <span class="mt-0.5 block text-base font-black">{{ $setting->confirmation_phone }}</span>
                        </span>
                    </a>
                @else
                    <p class="mt-3 text-sm text-slate-400">Nomor konfirmasi belum tersedia.</p>
                @endif
            </article>
        </section>
    </main>

    @include('partials.proof-modal')

    <button id="pwa-install" type="button" class="public-install-button hidden" aria-label="Install aplikasi ARUSKas">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0-4-4m4 4 4-4M5 21h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
