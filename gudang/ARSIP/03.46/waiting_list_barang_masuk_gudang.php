<?php
include('../belanja/db.php'); // pastikan path benar

// Query untuk menampilkan data dengan status waiting menggunakan PDO
$query = "SELECT * FROM qc_lolos WHERE status = 'waiting'";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Menampilkan data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting List Barang Masuk Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Waiting List Barang Masuk Gudang</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
                    <th>Mac Address</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Nama Toko</th>
                    <th>Ekspedisi</th>
                    <th>Belanja Via</th>
                    <th>Petugas Order</th>
                    <th>Petugas QC</th>
                    <th>Tanggal Order</th>
                    <th>Tanggal Datang</th>
                    <th>Tanggal QC</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
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
                    <td><?php echo $row['siapa_order']; ?></td>
                    <td><?php echo $row['petugas_qc']; ?></td>
                    <td><?php echo $row['tanggal_order']; ?></td>
                    <td><?php echo $row['tanggal_datang']; ?></td>
                    <td><?php echo $row['tanggal_qc']; ?></td>
                    <td><?php echo $row['keterangan']; ?></td>
                    <td>
                        <form action="masuk_gudang.php" method="POST">
                            <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                            <button type="submit" class="btn btn-success">Masukkan ke Gudang</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
