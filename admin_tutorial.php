<?php session_start(); if (!isset($_SESSION['username'])) { header('Location: login.php'); exit(); } ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutorial Admin Inventory DSG</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <div class="dsg-shell-note"><strong>DSG Inventory Guide</strong><br>Halaman pengingat cepat untuk admin agar alur barang tetap rapi dan mudah diaudit.</div>
  <h1 class="page-title mb-2">Tutorial Admin Inventory</h1>
  <p class="text-muted mb-4">Gunakan checklist ini setiap input barang, QC, stok gudang, dan pengeluaran teknisi.</p>
  <div class="dsg-kpi-grid">
    <div class="dsg-kpi"><small>1. Receiving</small><br><b>Input</b><p class="mb-0 text-muted">Masukkan barang belanja lengkap: nama, tipe, satuan, MAC/serial bila ada, toko, ekspedisi, dan tanggal.</p></div>
    <div class="dsg-kpi"><small>2. Quality Control</small><br><b>QC</b><p class="mb-0 text-muted">Cek fisik, fungsi, kelengkapan, lalu tandai lolos/retur. Jangan kirim ke gudang sebelum QC selesai.</p></div>
    <div class="dsg-kpi"><small>3. Finish Good</small><br><b>Gudang</b><p class="mb-0 text-muted">Barang lolos QC masuk stok gudang. Pastikan stok dan satuan sesuai barang real.</p></div>
    <div class="dsg-kpi"><small>4. Pengeluaran</small><br><b>Teknisi</b><p class="mb-0 text-muted">Saat barang keluar, isi teknisi, penggunaan, keterangan, dan petugas ACC agar mudah dilacak.</p></div>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3"><div class="card"><div class="card-header font-weight-bold">Checklist Harian Admin</div><div class="card-body"><ul><li>Cek barang waiting/QC.</li><li>Pastikan MAC address tidak dobel.</li><li>Update barang keluar hari ini.</li><li>Review stok minus/aneh.</li><li>Export laporan bila dibutuhkan owner.</li></ul></div></div></div>
    <div class="col-md-6 mb-3"><div class="card"><div class="card-header font-weight-bold">Aturan Data Rapi</div><div class="card-body"><ul><li>Nama barang konsisten.</li><li>Tipe barang jangan asal singkat.</li><li>Gunakan satuan yang sama.</li><li>Keterangan wajib jelas.</li><li>Jangan hapus data historis tanpa backup.</li></ul></div></div></div>
  </div>
  <a href="index.php" class="btn btn-primary">← Kembali ke Dashboard</a>
</div>
<script src="assets/js/dsg-modern.js"></script>
</body></html>
