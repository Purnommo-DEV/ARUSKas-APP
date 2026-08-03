@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="mb-6">
    <p class="text-sm font-semibold text-slate-400">Identitas yang tampil di dashboard keuangan User</p>
    <h2 class="mt-1 text-2xl font-black text-slate-800">Pengaturan Kajian</h2>
</div>

<div class="grid gap-6 xl:grid-cols-[1fr_360px]">
    <div class="surface-card p-6 sm:p-8">
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="study-name" class="form-label">Nama Kajian</label>
                    <input id="study-name" name="study_name" class="input" value="{{ $setting->study_name }}" placeholder="Kajian Ahad Pagi">
                    <p class="form-error" data-error-for="study_name"></p>
                </div>
                <div>
                    <label for="mosque-name" class="form-label">Nama Masjid</label>
                    <input id="mosque-name" name="mosque_name" class="input" value="{{ $setting->mosque_name }}" placeholder="Masjid Al-Ikhlas">
                    <p class="form-error" data-error-for="mosque_name"></p>
                </div>
            </div>
            <div>
                <label for="address" class="form-label">Alamat</label>
                <textarea id="address" name="address" rows="3" class="textarea" placeholder="Alamat masjid">{{ $setting->address }}</textarea>
                <p class="form-error" data-error-for="address"></p>
            </div>
            <div>
                <label for="confirmation-phone" class="form-label">Nomor Konfirmasi Donasi</label>
                <input id="confirmation-phone" name="confirmation_phone" class="input" value="{{ $setting->confirmation_phone }}" placeholder="08xxxxxxxxxx">
                <p class="form-error" data-error-for="confirmation_phone"></p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="thanks-message" class="form-label">Ucapan Terima Kasih</label>
                    <input id="thanks-message" name="thanks_message" class="input" value="{{ $setting->thanks_message }}" placeholder="Jazakumullahu Khairan">
                    <p class="form-error" data-error-for="thanks_message"></p>
                </div>
                <div>
                    <label for="blessing-message" class="form-label">Ucapan Penutup</label>
                    <input id="blessing-message" name="blessing_message" class="input" value="{{ $setting->blessing_message }}" placeholder="Baarakallahu Fiikum">
                    <p class="form-error" data-error-for="blessing_message"></p>
                </div>
            </div>
            <div>
                <label for="opening_balance_display" class="form-label">Saldo Awal Kas</label>
                <label class="input flex items-center gap-3">
                    <span class="font-bold text-slate-400">Rp</span>
                    <input id="opening_balance_display" type="text" inputmode="numeric" autocomplete="off" class="grow outline-none" data-raw-value="{{ $setting->opening_balance }}" @readonly($setting->opening_balance_set) placeholder="0">
                </label>
                <input id="opening_balance" name="opening_balance" type="hidden" value="{{ $setting->opening_balance }}">
                <input id="confirm_opening_balance_change" name="confirm_opening_balance_change" type="hidden" value="0">
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <p id="opening-balance-hint" class="text-xs leading-5 text-slate-400">{{ $setting->opening_balance_set ? 'Saldo Awal Kas telah dikunci sebagai dasar seluruh perhitungan.' : 'Isi Saldo Awal Kas untuk menetapkan dasar seluruh pembukuan.' }}</p>
                    <button id="change-opening-balance" type="button" class="btn btn-ghost btn-xs text-amber-700 {{ $setting->opening_balance_set ? '' : 'hidden' }}">Ubah Saldo Awal</button>
                </div>
                <p class="form-error" data-error-for="opening_balance"></p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="qris-image" class="form-label">QRIS Image</label>
                    <input id="qris-image" name="qris_image" type="file" accept="image/*" class="file-input">
                    <p class="mt-1 text-xs text-slate-400">JPG, PNG, atau WEBP · Maks. 2 MB</p>
                    <p class="form-error" data-error-for="qris_image"></p>
                </div>
                <div>
                    <label for="logo" class="form-label">Logo Kajian</label>
                    <input id="logo" name="logo" type="file" accept="image/*" class="file-input">
                    <p class="mt-1 text-xs text-slate-400">JPG, PNG, atau WEBP · Maks. 2 MB</p>
                    <p class="form-error" data-error-for="logo"></p>
                </div>
            </div>
            <div class="flex justify-end border-t border-gray-100 pt-5">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        <div class="surface-card p-5">
            <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Preview Logo</p>
            <div class="grid min-h-44 place-items-center rounded-2xl border border-dashed border-gray-200 bg-slate-50 p-6">
                @if($setting->logo_path)
                    <img id="logo-preview" src="{{ asset('storage/'.$setting->logo_path) }}" alt="Logo kajian" class="max-h-28 max-w-full rounded-xl object-contain">
                @else
                    <div class="grid size-24 place-items-center rounded-3xl bg-gradient-to-br from-blue-600 to-emerald-500 text-4xl font-black text-white shadow-lg">A</div>
                    <img id="logo-preview" src="" alt="Logo kajian" class="hidden max-h-28 max-w-full rounded-xl object-contain">
                @endif
            </div>
        </div>
        <div class="surface-card p-5">
            <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Preview QRIS</p>
            <div class="grid min-h-64 place-items-center rounded-2xl border border-dashed border-gray-200 bg-slate-50 p-6">
                @if($setting->qris_image_path)
                    <img id="qris-preview" src="{{ asset('storage/'.$setting->qris_image_path) }}" alt="QRIS donasi" class="max-h-56 max-w-full rounded-xl object-contain">
                @else
                    <div class="text-center text-sm text-slate-400"><span class="mb-2 block text-3xl">▧</span>Belum ada gambar QRIS</div>
                    <img id="qris-preview" src="" alt="QRIS donasi" class="hidden max-h-56 max-w-full rounded-xl object-contain">
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
