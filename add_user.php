<?php
session_start();
require 'belanja/db.php'; // Pastikan file db.php sudah terkoneksi dengan database

// Cek jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Meng-hash password
    $role = $_POST['role'];

    // Query untuk memasukkan data user baru
    $query = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $query->execute([$username, $password, $role]);

    echo "User berhasil ditambahkan!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Tambah Pengguna Baru</h2>
        <form action="add_user.php" method="POST">
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
                    <option value="teknisi">Teknisi</option>
                    <option value="admin_gudang">Admin Gudang</option>
                    <option value="kepala_gudang">Kepala Gudang</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Tambah Pengguna</button>
        </form>
    </div>
</body>
</html>
