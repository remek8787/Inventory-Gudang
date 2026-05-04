<?php
require 'db.php'; // Koneksi ke database

// Fungsi Tambah Satuan Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
    $nama_satuan = $_POST['nama_satuan'];

    // Cek apakah satuan sudah ada
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM satuan_barang WHERE nama_satuan = ?");
    $stmt_check->execute([$nama_satuan]);
    $exists = $stmt_check->fetchColumn();

    if ($exists > 0) {
        echo "Error: Satuan barang sudah ada!";
    } else {
        // Query untuk menambah satuan barang
        $stmt = $pdo->prepare("INSERT INTO satuan_barang (nama_satuan) VALUES (?)");
        try {
            $stmt->execute([$nama_satuan]);
            echo "Satuan barang berhasil ditambahkan!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Fungsi Hapus Satuan Barang
if (isset($_GET['delete'])) {
    $id_satuan = $_GET['delete'];

    // Query untuk menghapus satuan barang
    $stmt = $pdo->prepare("DELETE FROM satuan_barang WHERE id = ?");
    try {
        $stmt->execute([$id_satuan]);
        echo "Satuan barang berhasil dihapus!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fungsi Edit Satuan Barang
if (isset($_POST['edit'])) {
    $id_satuan = $_POST['id_satuan'];
    $nama_satuan = $_POST['nama_satuan'];

    // Cek apakah nama satuan sudah ada (selain yang sedang diedit)
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM satuan_barang WHERE nama_satuan = ? AND id != ?");
    $stmt_check->execute([$nama_satuan, $id_satuan]);
    $exists = $stmt_check->fetchColumn();

    if ($exists > 0) {
        echo "Error: Satuan barang sudah ada!";
    } else {
        // Query untuk mengedit satuan barang
        $stmt = $pdo->prepare("UPDATE satuan_barang SET nama_satuan = ? WHERE id = ?");
        try {
            $stmt->execute([$nama_satuan, $id_satuan]);
            echo "Satuan barang berhasil diedit!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Satuan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
    <h2>Kelola Satuan Barang</h2>
    <form action="tambah_satuan.php" method="post">
        <div class="form-group">
            <label for="nama_satuan">Nama Satuan:</label>
            <input type="text" class="form-control" name="nama_satuan" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Satuan</button>
    </form>

    <h2 class="my-4">Daftar Satuan Barang</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID Satuan</th>
                <th>Nama Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk menampilkan satuan barang dari database
            $stmt = $pdo->query("SELECT * FROM satuan_barang");
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nama_satuan'] . "</td>";
                echo "<td>
                    <a href='tambah_satuan.php?delete=" . $row['id'] . "' class='btn btn-danger btn-sm'>Hapus</a>
                    <a href='edit_satuan.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
