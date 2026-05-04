<?php
require '../belanja/db.php'; // Pastikan path ini benar

// Query untuk mengambil data permintaan barang beserta statusnya
$query = "
    SELECT p.id, p.nama_peminta, p.tipe_barang, p.jumlah, p.keterangan_penggunaan, p.tanggal_permintaan, p.kode_unik, s.status
    FROM permintaan_barang p
    LEFT JOIN status_permintaan s ON p.id = s.id_permintaan
    ORDER BY p.tanggal_permintaan DESC";
$stmt = $pdo->query($query);
$rekapitulasi = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Permintaan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Rekapitulasi Permintaan Barang</h2>
        
        <table class="table table-striped table-bordered mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>Kode Unik</th>
                    <th>Nama Peminta</th>
                    <th>Tipe Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan Penggunaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekapitulasi as $permintaan): ?>
                    <tr>
                        <td><?php echo $permintaan['kode_unik']; ?></td>
                        <td><?php echo $permintaan['nama_peminta']; ?></td>
                        <td><?php echo $permintaan['tipe_barang']; ?></td>
                        <td><?php echo $permintaan['jumlah']; ?></td>
                        <td><?php echo $permintaan['keterangan_penggunaan']; ?></td>
                        <td><?php echo $permintaan['tanggal_permintaan']; ?></td>
                        <td>
                            <?php
                                if ($permintaan['status'] === 'Disetujui') {
                                    echo '<span class="badge badge-success">Disetujui</span>';
                                } elseif ($permintaan['status'] === 'Ditolak') {
                                    echo '<span class="badge badge-danger">Ditolak</span>';
                                } else {
                                    echo '<span class="badge badge-warning">Menunggu</span>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
