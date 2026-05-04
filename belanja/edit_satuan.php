<?php
require 'db.php'; // Koneksi ke database

// Fungsi Update Satuan Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_satuan = $_POST['id_satuan'];
    $nama_satuan = $_POST['nama_satuan'];

    // Query untuk mengupdate satuan barang
    $stmt = $pdo->prepare("UPDATE satuan_barang SET nama_satuan = ? WHERE id = ?");
    $stmt->execute([$nama_satuan, $id_satuan]);

    echo "Satuan barang berhasil diupdate!";
}

// Ambil data satuan yang ingin diedit
if (isset($_GET['id'])) {
    $id_satuan = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM satuan_barang WHERE id = ?");
    $stmt->execute([$id_satuan]);
    $satuan = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Satuan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
    <h2>Edit Satuan Barang</h2>
    <form action="edit_satuan.php" method="post">
        <div class="form-group">
            <label for="nama_satuan">Nama Satuan:</label>
            <input type="text" class="form-control" name="nama_satuan" value="<?php echo $satuan['nama_satuan']; ?>" required>
            <input type="hidden" name="id_satuan" value="<?php echo $satuan['id']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Satuan</button>
    </form>
    <a href="tambah_satuan.php" class="btn btn-secondary mt-3">Kembali ke Tambah Satuan</a>
</div>

</body>
</html>
