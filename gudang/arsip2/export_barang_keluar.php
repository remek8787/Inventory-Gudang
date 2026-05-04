<?php
require '../belanja/db.php'; // Sesuaikan dengan path ke file koneksi database Anda

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=barang_keluar.csv');

// Query untuk mengambil data barang keluar
$query = "SELECT * FROM barang_keluar ORDER BY tanggal_keluar DESC";
$stmt = $pdo->query($query);
$barang_keluar = $stmt->fetchAll();

// Buka file output
$output = fopen('php://output', 'w');

// Tulis header kolom ke file CSV
fputcsv($output, ['ID Barang', 'Nama Barang', 'Tipe Barang', 'MAC Address', 'Stok Setelah Keluar', 'Satuan', 'Tanggal Keluar', 'Petugas ACC']);

// Tulis data ke file CSV
foreach ($barang_keluar as $row) {
    fputcsv($output, [$row['id_barang'], $row['nama_barang'], $row['tipe_barang'], $row['mac_address'], $row['stok'], $row['satuan'], $row['tanggal_keluar'], $row['petugas_acc']]);
}

// Tutup file output
fclose($output);
exit();
