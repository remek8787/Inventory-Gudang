<?php
require 'belanja/db.php'; // Koneksi ke database

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Update pengguna di database
    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $role, $id]);

    echo "Pengguna berhasil diupdate.";
}

// Ambil data user berdasarkan id
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$id]);
$user = $user->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Pengguna</h2>
    <form method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role">
                <option value="administrator" <?= $user['role'] == 'administrator' ? 'selected' : ''; ?>>Administrator</option>
                <option value="kepala_gudang" <?= $user['role'] == 'kepala_gudang' ? 'selected' : ''; ?>>Kepala Gudang</option>
                <option value="admin_gudang" <?= $user['role'] == 'admin_gudang' ? 'selected' : ''; ?>>Admin Gudang</option>
                <option value="teknisi" <?= $user['role'] == 'teknisi' ? 'selected' : ''; ?>>Teknisi</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Pengguna</button>
    </form>
</div>
</body>
</html>
