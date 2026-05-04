<?php
require 'belanja/db.php'; // Koneksi ke database

// Default username dan password yang ingin di-hash
$username = 'ananta';
$password_plain = '@Gudang2024';

// Hash password menggunakan bcrypt
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Cek apakah user sudah ada
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Jika user ditemukan, update password dengan yang di-hash
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$password_hashed, $username]);
    echo "Password untuk $username telah diperbarui.";
} else {
    // Jika user belum ada, tambahkan user baru
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'administrator')");
    $stmt->execute([$username, $password_hashed]);
    echo "User $username dengan password default telah ditambahkan.";
}
?>
