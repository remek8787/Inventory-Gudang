<?php
// Koneksi ke database
require '../belanja/db.php'; // Pastikan path benar

// Buat request_id unik (misalnya menggunakan fungsi uniqid)
$request_id = uniqid('REQ-');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_order = $_POST['jumlah_order'];
    $nama_teknisi = $_POST['nama_teknisi'];
    $penggunaan = $_POST['penggunaan'];
    $keterangan = $_POST['keterangan'];
    $tipe_barang = $_GET['tipe_barang']; // Tipe barang yang dipilih

    // Simpan order ke database dengan status 'Menunggu Konfirmasi'
    $stmt = $pdo->prepare("INSERT INTO order_barang (request_id, tipe_barang, jumlah_order, nama_teknisi, penggunaan, keterangan, status) 
                           VALUES (?, ?, ?, ?, ?, ?, 'Menunggu Konfirmasi')");
    $stmt->execute([$request_id, $tipe_barang, $jumlah_order, $nama_teknisi, $penggunaan, $keterangan]);

    // Redirect ke halaman order_barang.php untuk menghindari duplicate submission
    header("Location: order_barang.php?tipe_barang=" . urlencode($tipe_barang) . "&status=success&request_id=" . $request_id);
    exit;
}
