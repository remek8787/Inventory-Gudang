<?php
require '../belanja/db.php'; // Koneksi ke database

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=barang_keluar.csv');

// Output header kolom CSV
$output = fopen('php://output', 'w');
fputcsv($output, array('ID Barang', 'Nama Barang', 'Mac Address', 'Nama Teknisi', 'Petugas Admin', 'Keterangan', 'Penggunaan', 'Tanggal Keluar'));

// Query untuk mendapatkan data barang keluar
$query = "SELECT bk.id_barang, bmg.nama_barang, bmg.mac_address, bk.nama_teknisi, bk.petugas_admin, bk.keterangan, bk.penggunaan, bk.tanggal_keluar 
          FROM barang_keluar bk
          JOIN barang_masuk_gudang bmg ON bk.id_barang = bmg.id_barang";
$result = $pdo->query($query);

// Output setiap baris ke file CSV
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
?>
