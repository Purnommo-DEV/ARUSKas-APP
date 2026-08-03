@extends('layouts.admin')

@section('title', 'Master Kategori')

@section('content')
<div id="categories-module"
     data-data-url="{{ route('admin.categories.data') }}"
     data-store-url="{{ route('admin.categories.store') }}"
     data-base-url="{{ url('/admin/categories') }}"
     class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-400">Pengelompokan arus kas</p>
            <h2 class="mt-1 text-2xl font-black text-slate-800">Master Kategori</h2>
        </div>
        <button id="add-category" type="button" class="btn btn-primary"><span class="text-lg leading-none">+</span> Tambah Kategori</button>
    </div>

    <div class="surface-card overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-5">
            <h3 class="text-base font-extrabold text-slate-800">Daftar Kategori</h3>
            <p class="mt-1 text-xs text-slate-400">Jenis kategori menentukan pemasukan atau pengeluaran transaksi.</p>
        </div>
        <table id="categories-table" class="display w-full">
            <thead><tr><th>Nama</th><th>Jenis</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        </table>
    </div>

    <dialog id="category-modal" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                <h3 id="category-modal-title" class="text-lg font-extrabold text-slate-800">Tambah Kategori</h3>
                <button type="button" class="btn btn-circle btn-ghost btn-sm" data-modal-close>✕</button>
            </div>
            <form id="category-form" class="space-y-5 p-6">
                <div>
                    <label for="category-name" class="form-label">Nama Kategori</label>
                    <input id="category-name" name="name" class="input" placeholder="Contoh: Infak Jamaah">
                    <p class="form-error" data-error-for="name"></p>
                </div>
                <div>
                    <label for="category-type" class="form-label">Jenis</label>
                    <select id="category-type" name="type" class="select">
                        <option value="">Pilih jenis</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                    <p class="form-error" data-error-for="type"></p>
                </div>
                <div>
                    <label for="category-active" class="form-label">Status</label>
                    <select id="category-active" name="is_active" class="select">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                    <p class="form-error" data-error-for="is_active"></p>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" class="btn border-gray-200 bg-white text-slate-600" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
@endsection
