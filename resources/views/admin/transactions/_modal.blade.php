<dialog id="transaction-modal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
            <div>
                <h3 id="transaction-modal-title" class="text-lg font-extrabold text-slate-800">Tambah Transaksi</h3>
                <p class="mt-1 text-xs text-slate-400">Jenis transaksi otomatis mengikuti kategori.</p>
            </div>
            <button type="button" class="btn btn-circle btn-ghost btn-sm" data-modal-close aria-label="Tutup">×</button>
        </div>

        <form id="transaction-form" class="space-y-5 p-6" enctype="multipart/form-data">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="form-label" for="transaction_date">Tanggal</label>
                    <input id="transaction_date" name="transaction_date" type="date" class="input">
                    <p class="form-error" data-error-for="transaction_date"></p>
                </div>
                <div>
                    <label class="form-label">Periode Mingguan</label>
                    <div id="week-period-preview" class="flex h-12 items-center rounded-xl border border-gray-200 bg-slate-50 px-4 text-sm font-semibold text-slate-500">-</div>
                </div>
                <div>
                    <label class="form-label" for="payment_method">Metode Transaksi</label>
                    <select id="payment_method" name="payment_method" class="select">
                        <option value="">Pilih metode</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->value }}">{{ $method->label() }}</option>
                        @endforeach
                    </select>
                    <p class="form-error" data-error-for="payment_method"></p>
                </div>
                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <label class="text-sm font-semibold text-slate-700" for="category_id">Kategori</label>
                        <button type="button" class="btn btn-ghost btn-xs text-blue-600" data-add-category>+ Tambah Kategori</button>
                    </div>
                    <select id="category_id" name="category_id" class="select">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-type="{{ $category->type->value }}">{{ $category->name }} · {{ $category->type->label() }}</option>
                        @endforeach
                    </select>
                    <div class="mt-1.5 flex items-center justify-between gap-3">
                        <p class="form-error" data-error-for="category_id"></p>
                        <span id="category-type-preview" class="badge badge-ghost shrink-0">Pilih kategori</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label" for="party_name">Keterangan</label>
                <input id="party_name" name="party_name" type="text" maxlength="150" class="input" placeholder="Contoh: Hamba Allah / Vendor Konsumsi">
                <p class="form-error" data-error-for="party_name"></p>
            </div>

            <div>
                <label class="form-label" for="amount_display">Nominal</label>
                <label class="input flex items-center gap-3">
                    <span class="font-bold text-slate-400">Rp</span>
                    <input id="amount_display" type="text" inputmode="numeric" autocomplete="off" class="grow outline-none" placeholder="0">
                </label>
                <input id="amount" name="amount" type="hidden">
                <p class="mt-1 text-xs text-slate-400">Nominal diformat Rupiah saat diketik.</p>
                <p class="form-error" data-error-for="amount"></p>
            </div>

            <div>
                <label class="form-label" for="notes">Catatan <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea id="notes" name="notes" rows="3" maxlength="1000" class="textarea" placeholder="Tambahkan catatan bila diperlukan"></textarea>
                <p class="form-error" data-error-for="notes"></p>
            </div>

            <div>
                <label class="form-label" for="proof">Bukti Transaksi <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="proof" name="proof" type="file" accept="image/jpeg,image/png,image/webp" class="file-input w-full">
                <p class="mt-1 text-xs text-slate-400">JPG, JPEG, PNG, atau WEBP · maksimal 10 MB. File otomatis disimpan sebagai WEBP.</p>
                <p class="form-error" data-error-for="proof"></p>
                <input id="remove_proof" name="remove_proof" type="hidden" value="0">
                <div id="proof-edit-preview" class="mt-3 hidden items-start gap-3 rounded-2xl border border-gray-200 bg-slate-50 p-3">
                    <img id="proof-edit-image" src="" alt="Preview bukti transaksi" class="h-24 w-32 rounded-xl object-cover">
                    <div class="min-w-0 flex-1">
                        <p id="proof-edit-label" class="text-sm font-bold text-slate-700">Bukti saat ini</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Pilih file baru untuk mengganti bukti.</p>
                        <button type="button" class="btn btn-ghost btn-xs mt-2 text-red-500" data-remove-proof>Hapus Gambar</button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <button type="button" class="btn border-gray-200 bg-white text-slate-600" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="quick-category-modal" class="modal">
    <div class="modal-box w-11/12 max-w-md">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800">Tambah Kategori</h3>
                <p class="mt-1 text-xs text-slate-400">Kategori baru langsung dipilih pada transaksi.</p>
            </div>
            <button type="button" class="btn btn-circle btn-ghost btn-sm" data-modal-close aria-label="Tutup">×</button>
        </div>
        <form id="quick-category-form" class="space-y-5 p-6">
            <input type="hidden" name="is_active" value="1">
            <div>
                <label class="form-label" for="quick-category-name">Nama Kategori</label>
                <input id="quick-category-name" name="name" class="input" maxlength="100" placeholder="Contoh: Perlengkapan">
                <p class="form-error" data-error-for="name"></p>
            </div>
            <div>
                <label class="form-label" for="quick-category-type">Jenis</label>
                <select id="quick-category-type" name="type" class="select">
                    <option value="">Pilih jenis</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
                <p class="form-error" data-error-for="type"></p>
            </div>
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" class="btn border-gray-200 bg-white text-slate-600" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</dialog>
