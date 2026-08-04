<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
        <div class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h.01M9 13h.01M9 17h.01M15 15h.01M15 19h.01"/></svg>
            </span>
            <p class="text-xs font-bold uppercase tracking-wide text-blue-700">Kas Awal</p>
        </div>
        <div class="mt-3">
            <p class="text-xl font-black tracking-tight text-blue-700" data-summary="opening">Rp {{ number_format($summary['opening_balance'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] font-medium text-blue-700/70">Saldo awal periode</p>
        </div>
    </article>

    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
        <div class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/><path d="M5 19h14"/></svg>
            </span>
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Pemasukan</p>
        </div>
        <div class="mt-3">
            <p class="text-xl font-black tracking-tight text-emerald-700" data-summary="cash-in">Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] font-medium text-emerald-700/70">Total pemasukan</p>
        </div>
    </article>

    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md">
        <div class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/><path d="M5 5h14"/></svg>
            </span>
            <p class="text-xs font-bold uppercase tracking-wide text-rose-700">Pengeluaran</p>
        </div>
        <div class="mt-3">
            <p class="text-xl font-black tracking-tight text-rose-700" data-summary="cash-out">Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] font-medium text-rose-700/70">Total pengeluaran</p>
        </div>
    </article>

    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-slate-800 bg-gradient-to-br from-blue-900 to-slate-800 p-4 text-white shadow-sm transition-shadow duration-200 hover:shadow-md">
        <div class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-xl bg-white/15 text-white">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 7V6a2 2 0 0 0-2-2H5a3 3 0 0 0 0 6h15v8a2 2 0 0 1-2 2H5a3 3 0 0 1-3-3V7"/><path d="M16 14h.01"/></svg>
            </span>
            <p class="text-xs font-bold uppercase tracking-wide text-white">Saldo Kas</p>
        </div>
        <div class="mt-3">
            <p class="text-xl font-black tracking-tight text-white" data-summary="closing">Rp {{ number_format($summary['closing_balance'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] font-medium text-blue-100">Kas tersedia saat ini</p>
        </div>
    </article>
</div>
