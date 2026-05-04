<?php
// Tampilkan error jika ada masalah
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Koneksi ke database
require '../belanja/db.php'; // Sesuaikan dengan path ke file koneksi database Anda

// Ambil data dari form
$nama_peminta = $_POST['nama_peminta'];
$tipe_barang = $_POST['tipe_barang'];
$jumlah = $_POST['jumlah'];
$keterangan_penggunaan = $_POST['keterangan_penggunaan'];

// Generate kode unik
$initials = strtoupper(substr($nama_peminta, 0, 3)); // Ambil 3 huruf pertama dari nama
$dateCode = date('Ymd'); // Tanggal dalam format YYYYMMDD
$kode_unik = $initials . '-' . $dateCode;

// Tampilkan kode unik untuk memastikan nilai yang dihasilkan
var_dump($kode_unik);

// Insert data ke tabel permintaan_barang
$query = "INSERT INTO permintaan_barang (nama_peminta, tipe_barang, jumlah, keterangan_penggunaan, kode_unik, tanggal_permintaan)
          VALUES (:nama_peminta, :tipe_barang, :jumlah, :keterangan_penggunaan, :kode_unik, NOW())";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':nama_peminta' => $nama_peminta,
    ':tipe_barang' => $tipe_barang,
    ':jumlah' => $jumlah,
    ':keterangan_penggunaan' => $keterangan_penggunaan,
    ':kode_unik' => $kode_unik
]);

// Dapatkan ID permintaan terakhir untuk mengisi status awal
$lastInsertId = $pdo->lastInsertId();

// Insert status awal "Menunggu" ke tabel status_permintaan
$queryStatus = "INSERT INTO status_permintaan (id_permintaan, status) VALUES (:id_permintaan, 'Menunggu')";
$stmtStatus = $pdo->prepare($queryStatus);
$stmtStatus->execute([':id_permintaan' => $lastInsertId]);

echo "Permintaan berhasil diajukan dengan Kode Unik: " . $kode_unik;
