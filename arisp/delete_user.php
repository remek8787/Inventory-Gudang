<?php
require 'belanja/db.php'; // Koneksi ke database

// Ambil ID dari URL
$id = $_GET['id'];

// Hapus pengguna dari database
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Redirect ke halaman daftar pengguna setelah menghapus
header('Location: add_user.php');
exit();
?>
