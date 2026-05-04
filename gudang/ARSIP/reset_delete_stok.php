<?php
require '../belanja/db.php'; // Koneksi ke database

// Fungsi untuk reset stok ke 0 di barang_masuk_gudang dan barang_keluar
if (isset($_GET['reset_stok'])) {
    $id_barang = $_GET['reset_stok'];

    // Query untuk mengatur stok ke 0 di barang_masuk_gudang
    $stmt = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = 0 WHERE id_barang = ?");
    $stmt->execute([$id_barang]);

    // Query untuk mengatur stok ke 0 di barang_keluar jika ada
    $stmt_keluar = $pdo->prepare("UPDATE barang_keluar SET stok = 0 WHERE id_barang = ?");
    $stmt_keluar->execute([$id_barang]);

    echo "Stok berhasil di-reset ke 0 di barang masuk dan barang keluar!";
}

// Fungsi untuk menghapus data barang setelah reset stok
if (isset($_GET['delete'])) {
    $id_barang = $_GET['delete'];

    // Query untuk menghapus data di barang_masuk_gudang
    $stmt = $pdo->prepare("DELETE FROM barang_masuk_gudang WHERE id_barang = ?");
    $stmt->execute([$id_barang]);

    // Query untuk menghapus data di barang_keluar (opsional jika ingin menghapus)
    $stmt_keluar = $pdo->prepare("DELETE FROM barang_keluar WHERE id_barang = ?");
    $stmt_keluar->execute([$id_barang]);

    echo "Barang berhasil dihapus dari barang masuk dan barang keluar!";
}

// Query untuk mengambil data barang masuk gudang
$stmt = $pdo->query("SELECT * FROM barang_masuk_gudang");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Barang Masuk Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Daftar Barang Masuk Gudang</h2>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Tipe Barang</th>
                        <th>Mac Address</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Nama Toko</th>
                        <th>Ekspedisi</th>
                        <th>Belanja Via</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr>
                            <td><?php echo $row['id_barang']; ?></td>
                            <td><?php echo $row['nama_barang']; ?></td>
                            <td><?php echo $row['tipe_barang']; ?></td>
                            <td><?php echo $row['mac_address']; ?></td>
                            <td><?php echo $row['stok']; ?></td>
                            <td><?php echo $row['satuan_barang']; ?></td>
                            <td><?php echo $row['nama_toko']; ?></td>
                            <td><?php echo $row['ekspedisi']; ?></td>
                            <td><?php echo $row['belanja_via']; ?></td>
                            <td>
                                <a href="?reset_stok=<?php echo $row['id_barang']; ?>" class="btn btn-warning btn-sm">Reset Stok ke 0</a>
                                <a href="?delete=<?php echo $row['id_barang']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
