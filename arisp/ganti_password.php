<?php
require 'belanja/db.php'; // Koneksi ke database

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update password di database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $id]);

    echo "Password berhasil diganti.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
</head>
<body>
<div class="container mt-5">
    <h2>Ganti Password</h2>
    <form method="POST">
        <div class="form-group">
            <label for="password">Password Baru:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Ganti Password</button>
    </form>
</div>
</body>
</html>
