<?php
require '../belanja/db.php';

// Query untuk mengambil data permintaan barang beserta statusnya
$query = "
    SELECT p.id, p.nama_peminta, p.tipe_barang, p.jumlah, p.keterangan_penggunaan, p.tanggal_permintaan, p.kode_unik, s.status, s.alasan_penolakan
    FROM permintaan_barang p
    LEFT JOIN status_permintaan s ON p.id = s.id_permintaan
    ORDER BY p.tanggal_permintaan DESC";
$stmt = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Permintaan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Status Permintaan Barang</h2>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Kode Unik</th>
                        <th>Nama Peminta</th>
                        <th>Tipe Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan Penggunaan</th>
                        <th>Tanggal Permintaan</th>
                        <th>Status</th>
                        <th>Alasan Penolakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr>
                            <td><?php echo $row['kode_unik']; ?></td>
                            <td><?php echo $row['nama_peminta']; ?></td>
                            <td><?php echo $row['tipe_barang']; ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                            <td><?php echo $row['keterangan_penggunaan']; ?></td>
                            <td><?php echo $row['tanggal_permintaan']; ?></td>
                            <td><?php echo $row['status'] ?: 'Menunggu'; ?></td>
                            <td><?php echo $row['alasan_penolakan'] ?: '-'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
