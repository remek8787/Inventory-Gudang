<?php
require '../belanja/db.php'; // Pastikan path koneksi database benar

// Query untuk mengambil semua permintaan barang yang statusnya masih "Menunggu"
$query = "
    SELECT p.id, p.nama_peminta, p.tipe_barang, p.jumlah, p.keterangan_penggunaan, p.tanggal_permintaan, s.status
    FROM permintaan_barang p
    LEFT JOIN status_permintaan s ON p.id = s.id_permintaan
    WHERE s.status = 'Menunggu'
    ORDER BY p.tanggal_permintaan DESC";
$stmt = $pdo->query($query);
$permintaan_list = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Konfirmasi Permintaan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>List Konfirmasi Permintaan Barang</h2>
        
        <table class="table table-striped table-bordered mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>Nama Peminta</th>
                    <th>Tipe Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan Penggunaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permintaan_list as $permintaan): ?>
                    <tr>
                        <td><?php echo $permintaan['nama_peminta']; ?></td>
                        <td><?php echo $permintaan['tipe_barang']; ?></td>
                        <td><?php echo $permintaan['jumlah']; ?></td>
                        <td><?php echo $permintaan['keterangan_penggunaan']; ?></td>
                        <td><?php echo $permintaan['tanggal_permintaan']; ?></td>
                        <td>
                            <form method="POST" action="proses_konfirmasi_permintaan.php" style="display:inline;">
                                <input type="hidden" name="id_permintaan" value="<?php echo $permintaan['id']; ?>">
                                <button type="submit" name="action" value="confirm" class="btn btn-success btn-sm">Terima</button>
                                <button type="submit" name="action" value="cancel" class="btn btn-danger btn-sm">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
