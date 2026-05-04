<?php
require 'db.php'; // Koneksi ke database

// Fungsi untuk menambah tipe barang
if (isset($_POST['tambah_tipe'])) {
    $nama_tipe = $_POST['nama_tipe'];
    
    // Query untuk menambah tipe barang
    $stmt = $pdo->prepare("INSERT INTO tipe_barang (nama_tipe) VALUES (?)");
    $stmt->execute([$nama_tipe]);

    echo "Tipe barang berhasil ditambahkan!";
}

// Fungsi untuk mengedit tipe barang
if (isset($_POST['edit_tipe'])) {
    $id_tipe = $_POST['id_tipe'];
    $nama_tipe = $_POST['nama_tipe'];

    // Query untuk mengedit tipe barang
    $stmt = $pdo->prepare("UPDATE tipe_barang SET nama_tipe = ? WHERE id = ?");
    $stmt->execute([$nama_tipe, $id_tipe]);

    echo "Tipe barang berhasil diupdate!";
}

// Fungsi untuk menghapus tipe barang
if (isset($_POST['hapus_tipe'])) {
    $id_tipe = $_POST['id_tipe'];

    // Query untuk menghapus tipe barang
    $stmt = $pdo->prepare("DELETE FROM tipe_barang WHERE id = ?");
    $stmt->execute([$id_tipe]);

    echo "Tipe barang berhasil dihapus!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tipe Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Tambah Tipe Barang</h2>
    <form action="manage_tipe_barang.php" method="post">
        <div class="form-group">
            <label for="nama_tipe">Nama Tipe:</label>
            <input type="text" class="form-control" name="nama_tipe" required>
        </div>
        <button type="submit" name="tambah_tipe" class="btn btn-primary">Tambah Tipe</button>
    </form>

    <hr>

    <h2>Edit Tipe Barang</h2>
    <form action="manage_tipe_barang.php" method="post">
        <div class="form-group">
            <label for="id_tipe">Pilih Tipe Barang:</label>
            <select name="id_tipe" class="form-control" required>
                <?php
                // Query untuk menampilkan tipe barang yang ada
                $stmt = $pdo->query("SELECT * FROM tipe_barang");
                while ($row = $stmt->fetch()) {
                    echo "<option value=\"" . $row['id'] . "\">" . $row['nama_tipe'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="nama_tipe">Nama Tipe Baru:</label>
            <input type="text" class="form-control" name="nama_tipe" required>
        </div>
        <button type="submit" name="edit_tipe" class="btn btn-warning">Edit Tipe</button>
    </form>

    <hr>

    <h2>Hapus Tipe Barang</h2>
    <form action="manage_tipe_barang.php" method="post">
        <div class="form-group">
            <label for="id_tipe">Pilih Tipe Barang:</label>
            <select name="id_tipe" class="form-control" required>
                <?php
                // Query untuk menampilkan tipe barang yang ada
                $stmt = $pdo->query("SELECT * FROM tipe_barang");
                while ($row = $stmt->fetch()) {
                    echo "<option value=\"" . $row['id'] . "\">" . $row['nama_tipe'] . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="hapus_tipe" class="btn btn-danger">Hapus Tipe</button>
    </form>
</div>
</body>
</html>
