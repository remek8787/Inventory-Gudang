<?php
require '../belanja/db.php'; // Koneksi ke database

$barang_id = $_POST['barang_id'];
$jumlah = $_POST['jumlah'];
$teknisi_id = 1; // Sesuaikan dengan ID teknisi yang login

$query = "INSERT INTO permintaan (teknisi_id, barang_id, jumlah, status) VALUES ('$teknisi_id', '$barang_id', '$jumlah', 'Menunggu Persetujuan')";
$conn->query($query);

$conn->close();

header("Location: order_barang.php");
?>