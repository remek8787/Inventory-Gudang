<?php
include require '../belanja/db.php'; // Koneksi ke database
$id_barang = $_POST['id_barang'];
$jumlah_barang = $_POST['jumlah_barang'];
$keterangan = $_POST['keterangan'];
$nama_teknisi = 'teknisi_login'; // Ambil dari sesi login

$query = "INSERT INTO permintaan_barang (id_barang, nama_teknisi, jumlah_barang, keterangan, status) 
          VALUES ('$id_barang', '$nama_teknisi', '$jumlah_barang', '$keterangan', 'Menunggu Persetujuan')";

if (mysqli_query($conn, $query)) {
    echo "Permintaan barang berhasil diajukan!";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}
?>
