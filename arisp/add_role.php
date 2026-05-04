<?php
session_start();
require 'belanja/db.php'; // Koneksi ke database

// Cek apakah pengguna sudah login dan role-nya administrator
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'administrator') {
    header('Location: login.php');
    exit();
}

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Query untuk menyimpan pengguna baru
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {
        echo "Pengguna berhasil ditambahkan.";
    } else {
        echo "Gagal menambahkan pengguna.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna Baru</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Pengguna Baru</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="administrator">Administrator</option>
                <option value="kepala_gudang">Kepala Gudang</option>
                <option value="admin_gudang">Admin Gudang</option>
                <option value="teknisi">Teknisi</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
    </form>
</div>
</body>
</html>
