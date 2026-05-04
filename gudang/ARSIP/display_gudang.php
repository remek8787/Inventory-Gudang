<?php
require '../belanja/db.php'; // Koneksi ke database

// Query untuk mengambil data dari qc_lolos.php dan mengelompokkan berdasarkan tipe barang
$stmt = $pdo->query("SELECT tipe_barang, COUNT(*) as total_stok, petugas_qc FROM items WHERE qc_status = 'Lolos QC' GROUP BY tipe_barang");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang di Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .danger-warning {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Daftar Barang di Gudang</h2>
        <table class="table table-bordered mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>Tipe Barang</th>
                    <th>Total Stok</th>
                    <th>Petugas QC</th>
                    <th>Petugas Admin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()) { ?>
                <tr>
                    <td><?php echo $row['tipe_barang']; ?></td>
                    <td>
                        <?php 
                        echo $row['total_stok']; 
                        if ($row['total_stok'] == 1) {
                            echo '<span class="danger-warning"> (Stok Hampir Habis)</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo $row['petugas_qc']; ?></td>
                    <td>
                        <!-- Form untuk mengisi nama petugas admin -->
                        <form method="post" action="update_petugas_admin.php">
                        <input type="text" name="petugas_admin" class="form-control" placeholder="Masukkan Petugas Admin" required>
                        <input type="hidden" name="tipe_barang" value="<?php echo $row['tipe_barang']; ?>">
                        <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </td>
                    <td>

                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
