<?php
require '../belanja/db.php'; // Koneksi ke database

// Query untuk menghitung stok berdasarkan tipe barang
$query = "
    SELECT tipe_barang, SUM(stok) as total_stok
    FROM barang_masuk_gudang
    GROUP BY tipe_barang
    ORDER BY tipe_barang";

$stmt = $pdo->query($query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Stok Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .low-stock {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Daftar Stok Gudang</h2>

        <!-- Tabel Data Stok -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Tipe Barang</th>
                        <th>Total Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { 
                        // Jika stok kurang dari atau sama dengan 2, tambahkan kelas CSS low-stock
                        $lowStockClass = ($row['total_stok'] <= 2) ? 'low-stock' : '';
                    ?>
                        <tr>
                            <td><?php echo $row['tipe_barang']; ?></td>
                            <td class="<?php echo $lowStockClass; ?>"><?php echo $row['total_stok']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
