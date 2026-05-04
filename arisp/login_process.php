<?php
session_start();
require 'belanja/db.php'; // Pastikan file db.php sudah terkoneksi dengan benar

// Ambil data username dan password dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk cek apakah username dan password sesuai
$query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$query->execute([$username]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Simpan informasi pengguna di sesi
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Redirect pengguna berdasarkan role
    if ($user['role'] == 'administrator') {
        header('Location: admin_dashboard.php'); // Arahkan ke dashboard administrator
    } elseif ($user['role'] == 'admin_gudang') {
        header('Location: admin_gudang_dashboard.php'); // Arahkan ke dashboard admin gudang
    } elseif ($user['role'] == 'kepala_gudang') {
        header('Location: kepala_gudang_dashboard.php'); // Arahkan ke dashboard kepala gudang
    } elseif ($user['role'] == 'teknisi') {
        header('Location: teknisi/order_barang_keluar.php'); // Arahkan ke form teknisi order barang keluar
    } else {
        echo "Role tidak dikenal.";
    }
    exit;
} else {
    // Jika login gagal
    echo "Username atau password salah!";
    // Atau redirect ke halaman login lagi
    // header('Location: login.php');
}

?>
