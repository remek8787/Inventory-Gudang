<?php
require 'db.php'; // Koneksi ke database

// Fungsi Tambah Awalan ID Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_awalan'])) {
    $kode_awalan = $_POST['kode_awalan'];
    $deskripsi_awalan = $_POST['deskripsi_awalan'];

    // Query untuk menambah awalan ID barang
    $stmt = $pdo->prepare("INSERT INTO awalan_id_barang (kode_awalan, deskripsi_awalan) VALUES (?, ?)");
    try {
        $stmt->execute([$kode_awalan, $deskripsi_awalan]);
        echo "Awalan ID Barang berhasil ditambahkan!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fungsi Edit Awalan ID Barang
if (isset($_POST['edit_awalan'])) {
    $id_awalan = $_POST['id_awalan'];
    $kode_awalan = $_POST['kode_awalan'];
    $deskripsi_awalan = $_POST['deskripsi_awalan'];

    // Query untuk mengedit awalan ID barang
    $stmt = $pdo->prepare("UPDATE awalan_id_barang SET kode_awalan = ?, deskripsi_awalan = ? WHERE id = ?");
    $stmt->execute([$kode_awalan, $deskripsi_awalan, $id_awalan]);

    echo "Awalan ID Barang berhasil diupdate!";
}

// Fungsi Hapus Awalan ID Barang
if (isset($_POST['hapus_awalan'])) {
    $id_awalan = $_POST['id_awalan'];

    // Query untuk menghapus awalan ID barang
    $stmt = $pdo->prepare("DELETE FROM awalan_id_barang WHERE id = ?");
    $stmt->execute([$id_awalan]);

    echo "Awalan ID Barang berhasil dihapus!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Awalan ID Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
    <h2>Tambah Awalan ID Barang</h2>
    <form action="kelola_awalan_id.php" method="post">
        <div class="form-group">
            <label for="kode_awalan">Kode Awalan:</label>
            <input type="text" class="form-control" name="kode_awalan" required>
        </div>
        <div class="form-group">
            <label for="deskripsi_awalan">Deskripsi Awalan:</label>
            <input type="text" class="form-control" name="deskripsi_awalan" required>
        </div>
        <button type="submit" name="tambah_awalan" class="btn btn-primary">Tambah Awalan</button>
    </form>

    <hr>

    <h2>Edit Awalan ID Barang</h2>
    <form action="kelola_awalan_id.php" method="post">
        <div class="form-group">
            <label for="id_awalan">Pilih Awalan:</label>
            <select name="id_awalan" class="form-control" required>
                <?php
                // Query untuk menampilkan awalan yang ada
                $stmt = $pdo->query("SELECT * FROM awalan_id_barang");
                while ($row = $stmt->fetch()) {
                    echo "<option value=\"" . $row['id'] . "\">" . $row['kode_awalan'] . " - " . $row['deskripsi_awalan'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="kode_awalan">Kode Awalan Baru:</label>
            <input type="text" class="form-control" name="kode_awalan" required>
        </div>
        <div class="form-group">
            <label for="deskripsi_awalan">Deskripsi Awalan Baru:</label>
            <input type="text" class="form-control" name="deskripsi_awalan" required>
        </div>
        <button type="submit" name="edit_awalan" class="btn btn-warning">Edit Awalan</button>
    </form>

    <hr>

    <h2>Hapus Awalan ID Barang</h2>
    <form action="kelola_awalan_id.php" method="post">
        <div class="form-group">
            <label for="id_awalan">Pilih Awalan:</label>
            <select name="id_awalan" class="form-control" required>
                <?php
                // Query untuk menampilkan awalan yang ada
                $stmt = $pdo->query("SELECT * FROM awalan_id_barang");
                while ($row = $stmt->fetch()) {
                    echo "<option value=\"" . $row['id'] . "\">" . $row['kode_awalan'] . " - " . $row['deskripsi_awalan'] . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="hapus_awalan" class="btn btn-danger">Hapus Awalan</button>
    </form>
</div>

</body>
</html>
