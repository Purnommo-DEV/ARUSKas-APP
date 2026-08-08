@extends('layouts.admin')

@section('title', 'Kas Awal')

@section('content')
@php($months = config('finance.months'))
<div id="opening-balances-module"
     data-data-url="{{ route('admin.opening-balances.data') }}"
     data-store-url="{{ route('admin.opening-balances.store') }}"
     data-base-url="{{ url('/admin/opening-balances') }}"
     data-current-month="{{ now()->month }}"
     data-current-year="{{ now()->year }}"
     class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-400">Dasar pembukuan per periode</p>
            <h2 class="mt-1 text-2xl font-black text-slate-800">Kas Awal</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Jika periode belum memiliki Kas Awal, sistem otomatis memakai Saldo Kas periode sebelumnya.</p>
        </div>
        <button id="add-opening-balance" type="button" class="btn btn-primary"><span class="text-lg leading-none">+</span> Tambah Kas Awal</button>
    </div>

    <div class="surface-card overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-5">
            <h3 class="text-base font-extrabold text-slate-800">Daftar Kas Awal</h3>
            <p class="mt-1 text-xs text-slate-400">Satu periode hanya dapat memiliki satu Kas Awal.</p>
        </div>
        <table id="opening-balances-table" class="display w-full">
            <thead><tr><th>Periode</th><th class="text-right">Kas Awal</th><th>Catatan</th><th>Dibuat Oleh</th><th class="text-right">Aksi</th></tr></thead>
        </table>
    </div>

    <dialog id="opening-balance-modal" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                <div>
                    <h3 id="opening-balance-modal-title" class="text-lg font-extrabold text-slate-800">Tambah Kas Awal</h3>
                    <p class="mt-1 text-xs text-slate-400">Nilai ini menjadi saldo pertama pada periode terpilih.</p>
                </div>
                <button type="button" class="btn btn-circle btn-ghost btn-sm" data-modal-close aria-label="Tutup">×</button>
            </div>
            <form id="opening-balance-form" class="space-y-5 p-6">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="opening-balance-month" class="form-label">Bulan</label>
                        <select id="opening-balance-month" name="period_month" class="select">
                            @foreach($months as $number => $label)
                                <option value="{{ $number }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="form-error" data-error-for="period_month"></p>
                    </div>
                    <div>
                        <label for="opening-balance-year" class="form-label">Tahun</label>
                        <input id="opening-balance-year" name="period_year" type="number" min="2000" max="2100" class="input" inputmode="numeric">
                        <p class="form-error" data-error-for="period_year"></p>
                    </div>
                </div>
                <div>
                    <label for="opening-balance-display" class="form-label">Kas Awal</label>
                    <label class="input flex items-center gap-3">
                        <span class="font-bold text-slate-400">Rp</span>
                        <input id="opening-balance-display" type="text" inputmode="numeric" autocomplete="off" class="grow outline-none" placeholder="0">
                    </label>
                    <input id="opening-balance-amount" name="opening_balance" type="hidden">
                    <p class="mt-1 text-xs text-slate-400">Nominal diformat Rupiah saat diketik.</p>
                    <p class="form-error" data-error-for="opening_balance"></p>
                </div>
                <div>
                    <label for="opening-balance-notes" class="form-label">Catatan <span class="font-normal text-slate-400">(opsional)</span></label>
                    <textarea id="opening-balance-notes" name="notes" rows="3" maxlength="1000" class="textarea" placeholder="Contoh: Dana awal kas periode Agustus"></textarea>
                    <p class="form-error" data-error-for="notes"></p>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" class="btn border-gray-200 bg-white text-slate-600" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kas Awal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
@endsection
