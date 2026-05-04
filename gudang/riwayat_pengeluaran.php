<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel untuk filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query default dengan join ke tabel barang_masuk_gudang
$query = "
    SELECT 
        barang_keluar.id_barang,
        barang_masuk_gudang.nama_barang,
        barang_masuk_gudang.tipe_barang,
        barang_masuk_gudang.mac_address,
        barang_masuk_gudang.nama_toko,
        barang_masuk_gudang.ekspedisi,
        barang_masuk_gudang.belanja_via,
        barang_masuk_gudang.siapa_order,
        barang_masuk_gudang.petugas_qc,
        barang_masuk_gudang.tanggal_order,
        barang_masuk_gudang.tanggal_datang,
        barang_masuk_gudang.tanggal_qc,
        barang_keluar.nama_teknisi,
        barang_keluar.keterangan,
        barang_keluar.penggunaan,
        barang_keluar.petugas_admin,
        barang_keluar.tanggal_keluar
    FROM 
        barang_keluar
    LEFT JOIN 
        barang_masuk_gudang 
    ON 
        barang_keluar.id_barang = barang_masuk_gudang.id_barang
";
$params = [];

// Tambahkan filter berdasarkan input pencarian
$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(barang_keluar.id_barang LIKE ? OR barang_masuk_gudang.nama_barang LIKE ? OR barang_masuk_gudang.mac_address LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Tambahkan filter tanggal jika diisi
if (!empty($start_date) && !empty($end_date)) {
    $where_clauses[] = "barang_keluar.tanggal_keluar BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
} elseif (!empty($start_date)) {
    $where_clauses[] = "barang_keluar.tanggal_keluar >= ?";
    $params[] = $start_date;
} elseif (!empty($end_date)) {
    $where_clauses[] = "barang_keluar.tanggal_keluar <= ?";
    $params[] = $end_date;
}

// Gabungkan semua klausa WHERE
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY barang_keluar.tanggal_keluar DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pengeluaran Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Side Panel -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/GUDANGV1/gudang/barang_masuk_gudang.php">
                                Back To Dhasboard Gudang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/GUDANGV1/gudang/barang_keluar.php">
                                Keluarkan Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/GUDANGV1/gudang/stok_gudang.php">
                                Stok Gudang
                            </a>
                        </li>
                        <!-- Tambahkan lebih banyak link jika diperlukan -->
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="container mt-5">
                    <h1 class="text-center mb-4">Riwayat Pengeluaran Barang</h1>
                    <!-- Form Filter Berdasarkan Tanggal -->
                    <form method="GET" action="" class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="search" class="form-label">Cari (ID Barang / Nama Barang / Mac Address)</label>
                                <input type="text" name="search" class="form-control" placeholder="Masukkan kata kunci" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Tombol Export -->
                    <div class="mb-3">
                        <a href="?export=csv&search=<?php echo $search; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-success">Export ke CSV</a>
                        <a href="?export=xml&search=<?php echo $search; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-primary">Export ke XML</a>
                    </div>

                    <!-- Tabel Riwayat Pengeluaran -->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Mac Address</th>
                                <th>Nama Toko</th>
                                <th>Ekspedisi</th>
                                <th>Belanja Via</th>
                                <th>Siapa Order</th>
                                <th>Petugas QC</th>
                                <th>Tanggal Order</th>
                                <th>Tanggal Datang</th>
                                <th>Tanggal QC</th>
                                <th>Nama Teknisi</th>
                                <th>Keterangan</th>
                                <th>Penggunaan</th>
                                <th>Petugas Admin</th>
                                <th>Tanggal Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data) > 0): ?>
                                <?php foreach ($data as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipe_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['mac_address']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ekspedisi']); ?></td>
                                        <td><?php echo htmlspecialchars($row['belanja_via']); ?></td>
                                        <td><?php echo htmlspecialchars($row['siapa_order']); ?></td>
                                        <td><?php echo htmlspecialchars($row['petugas_qc']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_order']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_datang']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_qc']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_teknisi']); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['penggunaan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['petugas_admin']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggal_keluar']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="17" class="text-center">Tidak ada data yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
