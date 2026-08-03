<div class="surface-card overflow-hidden">
    <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-extrabold text-slate-800">Daftar Transaksi</h2>
            <p class="mt-1 text-xs text-slate-400">Saldo berjalan dihitung otomatis berdasarkan tanggal transaksi.</p>
        </div>
        @unless($readOnly ?? false)
            <button type="button" class="btn btn-primary hidden lg:inline-flex" data-add-transaction>
                <span class="text-lg leading-none">+</span> Tambah Transaksi
            </button>
        @endunless
    </div>
    <table id="transactions-table" class="display w-full">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Metode Transaksi</th>
                <th>Kategori</th>
                <th>Nama Jamaah / Vendor</th>
                <th class="text-right">Cash In</th>
                <th class="text-right">Cash Out</th>
                <th class="text-right">Balance</th>
                <th>Bukti</th>
                @unless($readOnly ?? false)<th class="text-right">Aksi</th>@endunless
            </tr>
        </thead>
    </table>
</div>
