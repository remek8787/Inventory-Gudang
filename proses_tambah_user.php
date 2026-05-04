<?php
// Koneksi ke database
include 'belanja/db.php'; // Path ke db.php

// Periksa apakah data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    // Validasi data
    if (empty($username) || empty($password) || empty($role)) {
        echo "<script>alert('Semua field harus diisi!'); window.location.href='kelola_izin_pengguna.php';</script>";
        exit;
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Cek apakah username sudah ada
        $query_check = "SELECT * FROM users WHERE username = :username";
        $stmt_check = $pdo->prepare($query_check);
        $stmt_check->execute(['username' => $username]);

        if ($stmt_check->rowCount() > 0) {
            echo "<script>alert('Username sudah terdaftar!'); window.location.href='kelola_izin_pengguna.php';</script>";
            exit;
        }

        // Simpan user ke database
        $query_insert = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt_insert = $pdo->prepare($query_insert);
        $stmt_insert->execute([
            'username' => $username,
            'password' => $hashed_password,
            'role' => $role
        ]);

        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='kelola_izin_pengguna.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menambahkan user: " . $e->getMessage() . "'); window.location.href='kelola_izin_pengguna.php';</script>";
    }
} else {
    echo "<script>alert('Akses tidak valid!'); window.location.href='kelola_izin_pengguna.php';</script>";
}
?>
