<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel pencarian
$cari_global = isset($_GET['cari_global']) ? $_GET['cari_global'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Query untuk mendapatkan data barang keluar
$query = "SELECT bk.id_barang, bmg.nama_barang, bmg.mac_address, bk.nama_teknisi, bk.petugas_admin, bk.keterangan, bk.penggunaan, bk.tanggal_keluar 
          FROM barang_keluar bk
          JOIN barang_masuk_gudang bmg ON bk.id_barang = bmg.id_barang WHERE 1=1";

// Tambahkan filter tanggal jika ada
if (!empty($tanggal)) {
    $query .= " AND DAY(bk.tanggal_keluar) = $tanggal";
}
if (!empty($bulan)) {
    $query .= " AND MONTH(bk.tanggal_keluar) = $bulan";
}
if (!empty($tahun)) {
    $query .= " AND YEAR(bk.tanggal_keluar) = $tahun";
}

// Tambahkan pencarian global jika ada
if (!empty($cari_global)) {
    $query .= " AND (bk.id_barang LIKE '%$cari_global%' 
                OR bmg.nama_barang LIKE '%$cari_global%' 
                OR bk.nama_teknisi LIKE '%$cari_global%'
                OR bk.keterangan LIKE '%$cari_global%' 
                OR bk.penggunaan LIKE '%$cari_global%')";
}

// Eksekusi query
$stmt = $pdo->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Barang Yang Sudah Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Daftar Barang Yang Sudah Keluar</h2>

        <!-- Form Filter -->
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="tanggal">Tanggal</label>
                    <input type="number" class="form-control" name="tanggal" placeholder="Tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="bulan">Bulan</label>
                    <input type="number" class="form-control" name="bulan" placeholder="Bulan" value="<?php echo htmlspecialchars($bulan); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="tahun">Tahun</label>
                    <input type="number" class="form-control" name="tahun" placeholder="Tahun" value="<?php echo htmlspecialchars($tahun); ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="cari_global">Pencarian Global</label>
                    <input type="text" class="form-control" name="cari_global" placeholder="Cari..." value="<?php echo htmlspecialchars($cari_global); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Cari</button>
                </div>
            </div>
        </form>

        <!-- Tabel Daftar Barang Keluar -->
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Mac Address</th>
                    <th>Nama Teknisi</th>
                    <th>Petugas Admin</th>
                    <th>Keterangan</th>
                    <th>Penggunaan</th>
                    <th>Tanggal Keluar</th> <!-- Tambahkan kolom tanggal keluar di sini -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['mac_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_teknisi']); ?></td>
                        <td><?php echo htmlspecialchars($row['petugas_admin']); ?></td>
                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                        <td><?php echo htmlspecialchars($row['penggunaan']); ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($row['tanggal_keluar'])); ?></td> <!-- Tampilkan tanggal dan waktu keluar -->
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tombol Export ke CSV -->
        <a href="export_csv.php" class="btn btn-success btn-block">Export ke CSV</a>
    </div>
</body>
</html>
