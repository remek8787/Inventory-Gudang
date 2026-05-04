<?php
session_start();
require 'belanja/db.php'; // Hubungkan ke file koneksi database

// Ambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Cek username di database
$query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$query->execute([$username]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Jika pengguna ditemukan dan password cocok
if ($user && password_verify($password, $user['password'])) {
    // Simpan data pengguna ke session
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Redirect berdasarkan role
    if ($user['role'] == 'administrator') {
        header('Location: admin_dashboard.php');
    } elseif ($user['role'] == 'teknisi') {
        header('Location: teknisi_dashboard.php');
    } else {
        header('Location: user_dashboard.php');
    }
    exit;
} else {
    // Jika login gagal
    echo "Login gagal, username atau password salah.";
}
?>
