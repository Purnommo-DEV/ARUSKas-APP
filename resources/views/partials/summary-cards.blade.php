<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <article class="surface-card surface-card-hover p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kas Awal</p>
        <p class="mt-2 text-xl font-black text-slate-800" data-summary="opening">Rp {{ number_format($summary['opening_balance'], 0, ',', '.') }}</p>
    </article>
    <article class="surface-card surface-card-hover p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Cash In</p>
        <p class="mt-2 text-xl font-black text-emerald-600" data-summary="cash-in">Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
    </article>
    <article class="surface-card surface-card-hover p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Cash Out</p>
        <p class="mt-2 text-xl font-black text-red-500" data-summary="cash-out">Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
    </article>
    <article class="surface-card surface-card-hover border-blue-100 bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white">
        <p class="text-xs font-bold uppercase tracking-wider text-blue-100">Saldo Akhir</p>
        <p class="mt-2 text-xl font-black" data-summary="closing">Rp {{ number_format($summary['closing_balance'], 0, ',', '.') }}</p>
        <p class="mt-2 text-[11px] text-blue-100">Kas Awal + Cash In − Cash Out</p>
    </article>
</div>
