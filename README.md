# ARUSKas

ARUSKas adalah sistem keuangan kajian berbasis Laravel 12. Admin dapat mengelola kategori, transaksi, user, dan identitas kajian. Role User memperoleh dashboard laporan keuangan read-only yang transparan dan responsif.

## Teknologi

- PHP 8.3+, Laravel 12, dan MySQL 8+
- Tailwind CSS 4 dan DaisyUI 5
- jQuery AJAX, Yajra DataTables, AutoNumeric, SweetAlert2, dan Toastr
- Spatie Laravel Permission
- Intervention Image v3 untuk bukti transaksi WEBP

Tidak menggunakan Livewire, Inertia, Bootstrap, grafik, ataupun fitur ekspor.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur koneksi MySQL di `.env`, lalu jalankan:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Untuk pengembangan aset gunakan `npm run dev`.

## Akun awal

| Role | Email | Kata sandi |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| User | `user@example.com` | `password` |

Segera ubah kata sandi akun awal sebelum aplikasi digunakan di lingkungan produksi.

## Perhitungan keuangan

- Jenis transaksi selalu mengikuti jenis kategori.
- Admin mengisi Saldo Awal Kas satu kali pada Pengaturan sebagai baseline sistem.
- Kas awal periode adalah Saldo Awal Kas ditambah akumulasi seluruh transaksi sebelum awal periode tersebut.
- Saldo akhir adalah kas awal + pemasukan − pengeluaran.
- Saldo tidak disimpan di database; seluruh nilai dihitung dari transaksi.
- Periode mingguan dihitung otomatis mulai hari Senin berdasarkan tanggal transaksi.

## Struktur halaman

- Dashboard Admin dan User hanya menampilkan ringkasan periode serta maksimal lima transaksi terbaru.
- Menu Transaksi menyajikan ledger lengkap, filter bulan/tahun, pencarian, running balance, dan bukti transaksi.
- User bersifat read-only: dapat melihat dashboard, ledger, filter, pencarian, dan bukti transaksi tanpa akses CRUD maupun master data.

## Pemeriksaan kualitas

```bash
php artisan test
vendor/bin/pint --test
npm run build
composer audit
npm audit
```
