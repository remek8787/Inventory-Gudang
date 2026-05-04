<?php
require '../belanja/db.php'; // Sesuaikan dengan path Anda

// Ambil data dari form
$id_barang = $_POST['id_barang'];
$kode_unik = $_POST['kode_unik'];
$petugas_acc = $_POST['petugas_acc'];

// Lakukan operasi yang diperlukan, misalnya update stok dan simpan informasi barang keluar
// Contoh query untuk menyimpan data barang keluar
$query = "INSERT INTO barang_keluar (id_barang, kode_unik, petugas_acc, tanggal_keluar) VALUES (:id_barang, :kode_unik, :petugas_acc, NOW())";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':id_barang' => $id_barang,
    ':kode_unik' => $kode_unik,
    ':petugas_acc' => $petugas_acc
]);

echo "Barang berhasil dikeluarkan dengan Kode Unik: " . $kode_unik;
