<?php
require '../belanja/db.php'; // Sesuaikan dengan path ke file koneksi database Anda

// Inisialisasi variabel filter
$id_barang = $_GET['id_barang'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

// Query untuk mengambil data barang keluar dengan filter
$query = "
    SELECT * FROM barang_keluar 
    WHERE (id_barang LIKE :id_barang) 
      AND (DAY(tanggal_keluar) = :tanggal OR :tanggal = '') 
      AND (MONTH(tanggal_keluar) = :bulan OR :bulan = '') 
      AND (YEAR(tanggal_keluar) = :tahun OR :tahun = '') 
    ORDER BY tanggal_keluar DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'id_barang' => "%$id_barang%",
    'tanggal' => $tanggal,
    'bulan' => $bulan,
    'tahun' => $tahun
]);

$barang_keluar = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Barang Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Rekapitulasi Barang Keluar</h2>
        
        <!-- Form Filter -->
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="id_barang">Cari ID Barang:</label>
                    <input type="text" id="id_barang" name="id_barang" class="form-control" value="<?php echo htmlspecialchars($id_barang); ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="tanggal">Tanggal:</label>
                    <input type="number" id="tanggal" name="tanggal" class="form-control" min="1" max="31" value="<?php echo htmlspecialchars($tanggal); ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="bulan">Bulan:</label>
                    <input type="number" id="bulan" name="bulan" class="form-control" min="1" max="12" value="<?php echo htmlspecialchars($bulan); ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="tahun">Tahun:</label>
                    <input type="number" id="tahun" name="tahun" class="form-control" min="2000" value="<?php echo htmlspecialchars($tahun); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="export_barang_keluar.php" class="btn btn-success">Export ke CSV</a>
        </form>

        <!-- Tabel Data Barang Keluar -->
        <div class="table-responsive mt-4">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Tipe Barang</th>
                        <th>MAC Address</th>
                        <th>Stok Setelah Keluar</th>
                        <th>Satuan</th>
                        <th>Tanggal Keluar</th>
                        <th>Petugas ACC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang_keluar as $row): ?>
                        <tr>
                            <td><?php echo $row['id_barang']; ?></td>
                            <td><?php echo $row['nama_barang']; ?></td>
                            <td><?php echo $row['tipe_barang']; ?></td>
                            <td><?php echo $row['mac_address']; ?></td>
                            <td><?php echo $row['stok']; ?></td>
                            <td><?php echo $row['satuan']; ?></td>
                            <td><?php echo $row['tanggal_keluar']; ?></td>
                            <td><?php echo $row['petugas_acc']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
