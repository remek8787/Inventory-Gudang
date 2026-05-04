<?php
session_start();
require '../belanja/db.php'; // Koneksi ke database

// Ambil data order yang sudah disetujui dari tabel barang_keluar
$query = "SELECT * FROM barang_keluar WHERE status = 'Disetujui'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Form Pengeluaran Barang</h2>

        <?php if (!empty($results)): ?>
            <form action="proses_keluar_barang.php" method="POST">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Barang</th>
                            <th>Tipe Barang</th>
                            <th>Jumlah Keluar</th>
                            <th>Keterangan</th>
                            <th>Nama Teknisi</th>
                            <th>Isi ID Barang</th> <!-- Admin harus mengisi ID Barang -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <?php 
                                $jumlah_keluar = $row['jumlah_keluar'];
                                for ($i = 0; $i < $jumlah_keluar; $i++): ?>
                                    <tr>
                                        <td>Pending ID</td> <!-- Placeholder sementara -->
                                        <td><?= $row['tipe_barang']; ?></td>
                                        <td><?= 1; ?></td> <!-- Setiap ID barang keluar satu per satu -->
                                        <td><?= $row['keterangan']; ?></td>
                                        <td><?= $row['nama_teknisi']; ?></td>
                                        <td>
                                            <input type="text" name="id_barang_keluar[]" class="form-control" required placeholder="Masukkan ID Barang">
                                        </td>
                                    </tr>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="order_id[]" value="<?= $row['id_barang']; ?>">
                <input type="hidden" name="tipe_barang" value="<?= $row['tipe_barang']; ?>">
                <button type="submit" class="btn btn-primary">Selesaikan Pengeluaran Barang</button>
            </form>
        <?php else: ?>
            <p class="text-danger">Tidak ada order barang yang disetujui saat ini.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
