<?php
require '../belanja/db.php'; // Koneksi ke database

// Tentukan bagian mana yang sedang aktif berdasarkan parameter 'page' di URL
$page = $_GET['page'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pergudangan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Dashboard Pergudangan</h2>

        <!-- Tombol Navigasi -->
        <div class="btn-group mb-4" role="group">
            <a href="?page=barang_masuk" class="btn btn-primary">Barang Masuk Gudang</a>
            <a href="?page=permintaan_barang" class="btn btn-primary">Permintaan Barang</a>
            <a href="?page=barang_keluar" class="btn btn-primary">Barang Keluar</a>
            <a href="?page=rekapitulasi" class="btn btn-primary">Rekapitulasi Barang Keluar</a>
        </div>

        <!-- Konten Berdasarkan Tombol yang Dipilih -->
        <div class="content mt-4">
            <?php
            if ($page == 'barang_masuk') {
                include 'barang_masuk_gudang.php';
            } elseif ($page == 'permintaan_barang') {
                include 'form_permintaan_barang.php';
            } elseif ($page == 'barang_keluar') {
                include 'form_barang_keluar.php';
            } elseif ($page == 'rekapitulasi') {
                include 'rekapitulasi_barang_keluar.php';
            } else {
                echo "<p class='text-center'>Silakan pilih bagian untuk ditampilkan.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
