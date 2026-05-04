<?php
require '../belanja/db.php'; // Koneksi ke database

// Ambil data dari form
$petugas_admin = $_POST['petugas_admin'];
$tipe_barang = $_POST['tipe_barang'];

// Update kolom petugas_admin berdasarkan tipe_barang
$stmt = $pdo->prepare("UPDATE gudang SET petugas_admin = ? WHERE tipe_barang = ?");
$stmt->execute([$petugas_admin, $tipe_barang]);

// Cek apakah query berhasil mengupdate baris
if ($stmt->rowCount() > 0) {
    // Redirect kembali ke halaman display_gudang.php dengan pesan sukses
    header("Location: display_gudang.php?status=success&msg=Petugas Admin berhasil diperbarui");
} else {
    // Jika tidak ada baris yang diupdate, mungkin tipe_barang salah
    header("Location: display_gudang.php?status=error&msg=Gagal memperbarui Petugas Admin, mungkin tipe barang salah.");
}
exit();
?>
