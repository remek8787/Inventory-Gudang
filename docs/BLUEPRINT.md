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
