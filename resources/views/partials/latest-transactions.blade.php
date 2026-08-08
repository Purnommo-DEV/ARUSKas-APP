<section class="surface-card overflow-hidden">
    <div class="border-b border-gray-100 px-5 py-5">
        <h2 class="text-base font-extrabold text-slate-800">5 Transaksi Terbaru</h2>
        <p class="mt-1 text-xs text-slate-400">Aktivitas kas terbaru yang tercatat.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px]">
            <thead>
                <tr class="border-b border-gray-100 bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Metode Transaksi</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Keterangan</th>
                    <th class="px-5 py-3 text-right">Cash In</th>
                    <th class="px-5 py-3 text-right">Cash Out</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestTransactions as $transaction)
                    <tr class="border-b border-gray-100 text-sm last:border-0">
                        <td class="whitespace-nowrap px-5 py-3.5 font-semibold text-slate-700">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-5 py-3.5">{{ $transaction->payment_method->label() }}</td>
                        <td class="px-5 py-3.5">{{ $transaction->category->name }}</td>
                        <td class="px-5 py-3.5">{{ $transaction->party_name }}</td>
                        <td class="whitespace-nowrap px-5 py-3.5 text-right font-bold text-emerald-600">{{ $transaction->category->type->value === 'income' ? 'Rp '.number_format($transaction->amount, 0, ',', '.') : '−' }}</td>
                        <td class="whitespace-nowrap px-5 py-3.5 text-right font-bold text-red-600">{{ $transaction->category->type->value === 'expense' ? 'Rp '.number_format($transaction->amount, 0, ',', '.') : '−' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
