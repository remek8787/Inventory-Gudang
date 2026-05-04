---
title: "Blueprint — Inventory Denta Sejahtera Group"
description: "Acuan awal migrasi project inventory lama dari XAMPP ke hosting subdomain inventory.dentasejahteragroup.my.id via FTP."
project: inventory-dsg
created: "2026-05-04"
updated: "2026-05-04"
tags: [inventory, dsg, hosting, ftp, xampp, migration, php, mysql]
---

# Blueprint — Inventory Denta Sejahtera Group

## Ringkasan
Project baru/legacy awal belajar milik Tuan Besar. Aplikasi sebelumnya berjalan lokal di **XAMPP** dan sekarang akan dimigrasikan ke hosting pada subdomain:

- URL/subdomain: `inventory.dentasejahteragroup.my.id`
- Target akses awal: hosting shared/cPanel-style via **FTP**
- Status awal: di FTP sudah ada file ZIP backup dan file SQL; database akan dibantu/dibuat lewat phpMyAdmin oleh Tuan Besar bila perlu.

## Kredensial FTP
> Sensitif. Jangan tampilkan ulang di grup/publik.

- Host: `inventory.dentasejahteragroup.my.id`
- Username: `admin@inventory.dentasejahteragroup.my.id`
- Password: tersimpan dari chat pribadi Tuan Besar tanggal 2026-05-04
- Metode: FTP

## Tujuan Migrasi
1. Ambil/cek isi ZIP backup dan SQL di FTP.
2. Identifikasi struktur aplikasi: PHP native / framework / asset / config database.
3. Ekstrak di workspace lokal untuk audit cepat.
4. Siapkan struktur deploy yang aman untuk shared hosting.
5. Sesuaikan konfigurasi database hosting.
6. Setelah database dibuat/import oleh Tuan Besar, update config koneksi.
7. Upload hasil ke root subdomain via FTP.
8. Verifikasi aplikasi live.

## Prinsip Kerja
- Jangan hapus file hosting sembarangan; backup dulu sebelum overwrite.
- Jangan expose kredensial di output publik.
- Simpan catatan perubahan penting di blueprint/memory.
- Untuk file lama hasil belajar, prioritaskan **jalan dulu**, baru refactor UI/UX/keamanan bertahap.
- Jika ada `config.php`, `.env`, `koneksi.php`, `database.php`, atau sejenisnya, audit bagian DB credential dan path base URL.

## Checklist Awal
- [ ] Test koneksi FTP.
- [ ] List file di root FTP.
- [ ] Download ZIP backup dan SQL ke workspace lokal.
- [ ] Ekstrak ZIP ke folder kerja lokal.
- [ ] Identifikasi entrypoint (`index.php`) dan config DB.
- [ ] Cek versi PHP/minimal requirement.
- [ ] Cek nama database/credential lama dari file XAMPP.
- [ ] Tunggu/terima credential DB hosting dari Tuan Besar.
- [ ] Import SQL via phpMyAdmin atau tool yang tersedia.
- [ ] Update config DB.
- [ ] Upload aplikasi.
- [ ] Test URL live.

## Catatan Operasional
- Subdomain sudah diarahkan oleh Tuan Besar.
- File backup ZIP dan SQL disebut sudah ada di sisi FTP.
- Database belum final; Tuan Besar akan bantu lewat phpMyAdmin.

## Temuan Awal 2026-05-04

### FTP
- Koneksi FTP sukses.
- Root FTP berisi:
  - `GUDANGV1-backup.zip` ±14 MB
  - folder bawaan: `cgi-bin`, `.well-known`
  - belum terlihat file SQL terpisah di root, ternyata SQL ada di dalam ZIP.

### Backup ZIP
- File sudah didownload ke workspace lokal: `/root/.openclaw/workspace/projects/inventory-dsg/GUDANGV1-backup.zip`
- Ekstrak lokal: `/root/.openclaw/workspace/projects/inventory-dsg/extracted/`

### Struktur Aplikasi
- Aplikasi PHP native, bukan Laravel.
- Entrypoint utama: `index.php`, `login.php`, `login_process.php`.
- Modul utama:
  - `belanja/`
  - `gudang/`
  - `quality_control/`
  - `order_teknisi/`
  - `admin_dhasboard/`
  - `arisp/`
  - `vendor/` untuk QR code composer package.
- Composer dependency: `endroid/qr-code`.

### Database
- SQL dump di dalam ZIP: `gudangv1-452026.sql` ±1.2 MB.
- Dump tidak berisi `CREATE DATABASE` atau `USE`, jadi bisa di-import ke database hosting apa pun yang dibuat Tuan Besar.
- Tabel terdeteksi: `awalan_id_barang`, `barang`, `barang_keluar`, `barang_keluar_detail`, `barang_masuk_gudang`, `barang_tidak_jadi_keluar`, `gudang`, `items`, `order_barang`, `order_barang_detail`, `permintaan_barang`, `qc_lolos`, `qc_tidak_lolos`, `record_permintaan`, `rekapitulasi_permintaan`, `riwayat_barang_keluar`, `satuan_barang`, `status_permintaan`, `stok_gudang`, `tipe_barang`, `users`.

### Konfigurasi DB Lama
File utama: `belanja/db.php`

```php
$host = 'localhost';
$dbname = 'gudangv1';
$username = 'root';
$password = '';
```

Perlu diganti ke credential database hosting setelah DB dibuat.

## Next Action
1. Tunggu credential database hosting dari Tuan Besar: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
2. Update `belanja/db.php`.
3. Import SQL ke phpMyAdmin/database hosting.
4. Upload file aplikasi ke root subdomain via FTP.
5. Test `https://inventory.dentasejahteragroup.my.id/`.


## UI/UX Modernization 2026-05-04

Perubahan awal:
- Menambahkan stylesheet global `assets/css/dsg-modern.css`.
- Menambahkan script helper global `assets/js/dsg-modern.js` untuk wrapping tabel responsive dan tombol tutorial.
- Memoles login page dengan brand card DSG Inventory.
- Menambahkan reminder admin di dashboard utama.
- Menambahkan halaman tutorial admin live: `admin_tutorial.php`.
- Menambahkan dokumentasi tutorial: `docs/ADMIN_TUTORIAL.md`.

Catatan teknis:
- Pendekatan sengaja **non-invasive** agar logic PHP native lama tidak rusak.
- Belum menggunakan build tool React/Vite agar tetap kompatibel dengan shared hosting FTP.
- React/Tailwind bisa dipakai di fase berikutnya untuk modul baru atau redesign dashboard penuh.


## AJAX Search Improvement 2026-05-04

Peningkatan:
- `belanja/dhasboar_barang_belanja.php` mendukung endpoint JSON `?ajax=1`.
- `belanja/tambah_barang.php` mendukung endpoint JSON `?ajax=1`.
- Search form memakai `assets/js/dsg-ajax-search.js` dengan debounce, sehingga filter/search berjalan tanpa reload halaman penuh.
- Search pada `belanja/tambah_barang.php` diganti dari raw SQL string concatenation menjadi prepared statement PDO untuk mengurangi risiko SQL injection.
- Counter hasil pencarian tampil realtime.

Verifikasi live:
- `https://inventory.dentasejahteragroup.my.id/belanja/dhasboar_barang_belanja.php?ajax=1&search=&bulan=&tahun=` mengembalikan JSON `ok: true`.
- `https://inventory.dentasejahteragroup.my.id/belanja/tambah_barang.php?ajax=1&search_id=&search_name=&search_type=&search_date=` mengembalikan JSON `ok: true`.


## Dashboard + Extra AJAX + Dark Mode 2026-05-04

Peningkatan read-only tanpa merusak DB:
- Menambahkan `inventory_metrics.php` untuk statistik inventory: total barang input, waiting QC, total stok gudang, barang keluar bulan berjalan, stok rendah, dan barang keluar terbaru.
- Menambahkan link statistik dan tutorial di dashboard utama `index.php`.
- Menambahkan AJAX search untuk `gudang/riwayat_pengeluaran.php`.
- Menambahkan AJAX search untuk `gudang/barang_yang_sudah_keluar.php`.
- Mengubah query search `barang_yang_sudah_keluar.php` menjadi prepared statement PDO.
- Menambahkan dark mode toggle via `assets/js/dsg-modern.js` + CSS tambahan.

Verifikasi live:
- `gudang/riwayat_pengeluaran.php?ajax=1...` return JSON `ok: true`.
- `gudang/barang_yang_sudah_keluar.php?ajax=1...` return JSON `ok: true`.
- `inventory_metrics.php` aman redirect ke login jika belum login.


## 404 Path Fix 2026-05-04

Masalah:
- Beberapa link legacy masih hardcoded ke `/GUDANGV1/...` dari masa XAMPP.
- Di hosting baru aplikasi berjalan di root subdomain, sehingga link tersebut menghasilkan 404.

Perbaikan:
- Mengganti hardcoded `/GUDANGV1/` menjadi `/` pada file aktif utama:
  - `belanja/tambah_barang.php`
  - `gudang/barang_keluar.php`
  - `gudang/barang_masuk_gudang.php`
  - `gudang/riwayat_pengeluaran.php`
  - `gudang/stok_gudang.php`
  - `quality_control/list_selesai_qc.php`
  - `quality_control/qc_dashboard.php`
  - `quality_control/qc_lolos.php`
  - `quality_control/qc_retur.php`
  - `cekmacaddress_webhook.php`

Verifikasi live:
- `belanja/tambah_barang.php` HTTP 200.
- `gudang/barang_masuk_gudang.php` HTTP 200.
- `quality_control/qc_dashboard.php` HTTP 200.
- `gudang/stok_gudang.php` HTTP 200.


## Fix List Selesai QC 2026-05-04

Masalah:
- `quality_control/list_selesai_qc.php` memang HTTP 200, tetapi belum beres secara UX/logic.
- Halaman memuat terlalu banyak row tanpa pagination sehingga berat.
- Search masih sempit/exact dan belum AJAX.
- Field petugas order di beberapa bagian tidak konsisten.

Perbaikan:
- Rewrite halaman menjadi versi paginated 25 row per halaman.
- Search multi-field: ID barang, nama barang, tipe, MAC, toko, ekspedisi, petugas order, petugas QC.
- AJAX endpoint `?ajax=1` untuk search realtime.
- Export CSV mengikuti filter.
- Query search menggunakan prepared statement PDO.
- Kolom `Petugas Order` menggunakan field `siapa_order`.
- Tombol `Kirim ke Gudang` tetap memakai POST ke `../gudang/barang_masuk_gudang.php` agar alur lama tetap berjalan.

Verifikasi:
- URL utama HTTP 200.
- `quality_control/list_selesai_qc.php?ajax=1&id_barang=MODEM` return JSON `ok: true`.


## Fix Barang Masuk Gudang 2026-05-04

Permintaan:
- Pada `gudang/barang_masuk_gudang.php`, tambahkan tombol Delete.
- Jika Delete ditekan, barang harus kembali lagi ke `quality_control/list_selesai_qc.php`.
- Tambahkan pagination, AJAX search, dan optimasi load.

Perbaikan:
- Rewrite `gudang/barang_masuk_gudang.php` menjadi paginated 25 row per halaman.
- Search AJAX multi-field: ID, nama barang, MAC, bulan, tahun, ekspedisi.
- Tombol `Delete / Balik QC` memakai POST `return_to_qc`.
- Alur delete: update `items.qc_status = 'Selesai QC'`, lalu hapus row dari `barang_masuk_gudang`; ini membuat barang muncul lagi di `quality_control/list_selesai_qc.php`.
- Tombol `Kirim ke Gudang` dari `list_selesai_qc.php` disesuaikan agar mengirim `send_to_gudang`.

Verifikasi:
- URL utama `gudang/barang_masuk_gudang.php?...` HTTP 200.
- Endpoint AJAX `gudang/barang_masuk_gudang.php?ajax=1...` return JSON `ok: true` dan memuat tombol Delete / Balik QC.


## Professional Panel/UI Alignment 2026-05-04

Permintaan:
- Rapikan panel.
- Samakan UI `gudang/barang_keluar.php`, `quality_control/list_selesai_qc.php`, dan `gudang/barang_masuk_gudang.php` agar lebih profesional, menarik, dan konsisten.
- Tambahkan pagination/AJAX search bila diperlukan seperti `gudang/riwayat_pengeluaran.php`.

Perbaikan:
- Menambahkan CSS reusable layout: `dsg-app`, `dsg-pro-sidebar`, `dsg-pro-main`, `dsg-page-head`, `dsg-panel`, `dsg-table-card`, dll.
- Rewrite `gudang/barang_keluar.php` ke layout profesional dengan sidebar DSG, panel form, search AJAX barang tersedia, dan tabel pilihan barang.
- Menyamakan panel/sidebar `quality_control/list_selesai_qc.php` dengan layout DSG professional.
- Menyamakan panel/sidebar `gudang/barang_masuk_gudang.php` dengan layout DSG professional.
- `barang_keluar.php` sekarang menggunakan search AJAX read-only untuk memilih barang tersedia tanpa dropdown berat.

Verifikasi:
- `gudang/barang_keluar.php` HTTP 200.
- `gudang/barang_keluar.php?ajax=1&search=MODEM` JSON ok:true.
- `quality_control/list_selesai_qc.php?ajax=1&id_barang=MODEM` JSON ok:true.
- `gudang/barang_masuk_gudang.php?ajax=1...` JSON ok:true.


## Fix Riwayat Pengeluaran Live Search 2026-05-05

Masalah:
- User melaporkan `gudang/riwayat_pengeluaran.php` belum terlihat punya pagination dan live search.

Perbaikan:
- Rewrite halaman ke layout professional DSG yang konsisten.
- Menambahkan pagination 25 row per halaman.
- Menambahkan live search AJAX via `assets/js/dsg-ajax-search.js`.
- Search global mencakup ID, nama barang, tipe, MAC, teknisi, petugas admin, keterangan, dan penggunaan.
- Query menggunakan prepared statement PDO.
- Export CSV/XML mengikuti filter.
- Menggunakan `COALESCE` agar riwayat tetap menampilkan data walau item sudah tidak ada di `barang_masuk_gudang`.

Verifikasi:
- `gudang/riwayat_pengeluaran.php` HTTP 200.
- `gudang/riwayat_pengeluaran.php?ajax=1&search=MODEM&start_date=&end_date=` return JSON `ok:true`.


## Horizontal Table Scroll Fix 2026-05-05

Masalah:
- Pada layar kecil/HP, tabel lebar tidak bisa digeser kanan-kiri karena wrapper card memakai overflow yang menahan scroll.

Perbaikan:
- Menambahkan CSS global untuk `.dsg-table-card.table-responsive`, `.table-responsive`, dan table children agar `overflow-x:auto`, `-webkit-overflow-scrolling:touch`, dan `min-width` aktif.
- Menambahkan hint mobile "Geser tabel ke kanan/kiri →".

Verifikasi:
- CSS live dapat diakses dengan cache-busting `assets/css/dsg-modern.css?v=scrollfix`.
- Halaman `gudang/riwayat_pengeluaran.php?v=scrollfix` HTTP 200.
