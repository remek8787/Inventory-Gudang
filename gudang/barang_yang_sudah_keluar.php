<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel pencarian
$cari_global = isset($_GET['cari_global']) ? $_GET['cari_global'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Query untuk mendapatkan data barang keluar (prepared/read-only)
$query = "SELECT bk.id_barang, bmg.nama_barang, bmg.mac_address, bk.nama_teknisi, bk.petugas_admin, bk.keterangan, bk.penggunaan, bk.tanggal_keluar 
          FROM barang_keluar bk
          JOIN barang_masuk_gudang bmg ON bk.id_barang = bmg.id_barang WHERE 1=1";
$params = [];

if (!empty($tanggal)) {
    $query .= " AND DAY(bk.tanggal_keluar) = :tanggal";
    $params[':tanggal'] = (int)$tanggal;
}
if (!empty($bulan)) {
    $query .= " AND MONTH(bk.tanggal_keluar) = :bulan";
    $params[':bulan'] = (int)$bulan;
}
if (!empty($tahun)) {
    $query .= " AND YEAR(bk.tanggal_keluar) = :tahun";
    $params[':tahun'] = (int)$tahun;
}
if (!empty($cari_global)) {
    $query .= " AND (bk.id_barang LIKE :cari_global OR bmg.nama_barang LIKE :cari_global OR bk.nama_teknisi LIKE :cari_global OR bk.keterangan LIKE :cari_global OR bk.penggunaan LIKE :cari_global)";
    $params[':cari_global'] = "%$cari_global%";
}
$query .= " ORDER BY bk.tanggal_keluar DESC";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    ob_start();
    if ($data) {
        foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($row['mac_address']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nama_teknisi']) . '</td>';
            echo '<td>' . htmlspecialchars($row['petugas_admin']) . '</td>';
            echo '<td>' . htmlspecialchars($row['keterangan']) . '</td>';
            echo '<td>' . htmlspecialchars($row['penggunaan']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tanggal_keluar']) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data ditemukan</td></tr>';
    }
    echo json_encode(['ok'=>true,'html'=>ob_get_clean(),'total'=>count($data)]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Barang Yang Sudah Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-2 text-center page-title">Daftar Barang Yang Sudah Keluar</h2>
        <div id="barang-keluar-counter" class="text-center text-muted mb-4">Menampilkan <?php echo count($data); ?> data barang keluar</div>

        <!-- Form Filter -->
        <form method="GET" action="" class="dsg-ajax-search" data-target="#barang-keluar-body" data-counter="#barang-keluar-counter">
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
            <tbody id="barang-keluar-body">
                <?php foreach ($data as $row) { ?>
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
<script src="/assets/js/dsg-modern.js"></script>
<script src="/assets/js/dsg-ajax-search.js"></script>
</body>
</html>
