@extends('layouts.admin')

@section('title', 'User')

@section('content')
<div id="users-module"
     data-data-url="{{ route('admin.users.data') }}"
     data-store-url="{{ route('admin.users.store') }}"
     data-base-url="{{ url('/admin/users') }}"
     class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-400">Akses pengguna aplikasi</p>
            <h2 class="mt-1 text-2xl font-black text-slate-800">Kelola User</h2>
        </div>
        <button id="add-user" type="button" class="btn btn-primary"><span class="text-lg leading-none">+</span> Tambah User</button>
    </div>

    <div class="surface-card overflow-hidden">
        <div class="border-b border-gray-100 px-5 py-5">
            <h3 class="text-base font-extrabold text-slate-800">Daftar User</h3>
            <p class="mt-1 text-xs text-slate-400">Admin dapat mengatur akun Admin dan User read-only.</p>
        </div>
        <table id="users-table" class="display w-full">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th><th class="text-right">Aksi</th></tr></thead>
        </table>
    </div>

    <dialog id="user-modal" class="modal">
        <div class="modal-box w-11/12 max-w-lg">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                <h3 id="user-modal-title" class="text-lg font-extrabold text-slate-800">Tambah User</h3>
                <button type="button" class="btn btn-circle btn-ghost btn-sm" data-modal-close>✕</button>
            </div>
            <form id="user-form" class="space-y-5 p-6">
                <div>
                    <label for="user-name" class="form-label">Nama</label>
                    <input id="user-name" name="name" class="input" placeholder="Nama lengkap">
                    <p class="form-error" data-error-for="name"></p>
                </div>
                <div>
                    <label for="user-email" class="form-label">Email</label>
                    <input id="user-email" name="email" type="email" class="input" placeholder="nama@email.com">
                    <p class="form-error" data-error-for="email"></p>
                </div>
                <div>
                    <label for="user-role" class="form-label">Role</label>
                    <select id="user-role" name="role" class="select">
                        <option value="">Pilih role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <p class="form-error" data-error-for="role"></p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="user-password" class="form-label">Kata Sandi</label>
                        <input id="user-password" name="password" type="password" class="input" placeholder="••••••••">
                        <p id="password-hint" class="mt-1 text-xs text-slate-400">Minimal 8 karakter.</p>
                        <p class="form-error" data-error-for="password"></p>
                    </div>
                    <div>
                        <label for="user-password-confirmation" class="form-label">Ulangi Kata Sandi</label>
                        <input id="user-password-confirmation" name="password_confirmation" type="password" class="input" placeholder="••••••••">
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" class="btn border-gray-200 bg-white text-slate-600" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
@endsection
