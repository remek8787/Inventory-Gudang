# Tutorial Admin — Inventory Gudang DSG

## Alur Utama

1. **Receiving / Barang Belanja**
   - Input barang baru dari pembelian.
   - Isi nama barang, tipe, satuan, MAC/serial bila ada, toko, ekspedisi, dan tanggal.

2. **Quality Control**
   - Cek barang sebelum masuk stok gudang.
   - Tandai barang lolos atau retur.
   - Barang belum boleh dipakai teknisi sebelum QC selesai.

3. **Finish Good / Gudang**
   - Barang lolos QC masuk gudang.
   - Pastikan stok dan satuan sesuai kondisi fisik.

4. **Barang Keluar**
   - Saat barang keluar, isi nama teknisi, penggunaan, keterangan, dan petugas admin/ACC.
   - Gunakan data ini untuk audit pemasangan atau pergantian perangkat.

## Checklist Harian

- Cek barang yang masih waiting/QC.
- Cek MAC address/serial agar tidak dobel.
- Update barang keluar hari ini.
- Review stok yang janggal.
- Export laporan jika dibutuhkan owner/admin.

## Aturan Data Rapi

- Nama barang konsisten.
- Tipe barang jelas.
- Satuan barang seragam.
- Keterangan wajib jelas.
- Jangan hapus data historis tanpa backup.

## Catatan Teknis

UI modern menggunakan:
- `assets/css/dsg-modern.css`
- `assets/js/dsg-modern.js`
- Halaman live tutorial: `admin_tutorial.php`
