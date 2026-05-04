<?php
require 'db.php'; // Koneksi ke database

// Fungsi untuk Ekspor Data ke CSV
function exportToCSV($stmt) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data_barang.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Barang', 'Nama Barang', 'Type', 'Stok', 'Satuan', 'Nama Toko', 'Ekspedisi', 'Belanja Via', 'Petugas', 'Tanggal Order', 'Tanggal Datang'], ';');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row, ';');
    }
    fclose($output);
    exit;
}

// Filter dan Pencarian
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$search = $_GET['search'] ?? '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Pastikan halaman minimal 1
$limit = 10;
$offset = ($page - 1) * $limit;

// Cek apakah ada ekspor yang dilakukan
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE 
        (MONTH(tanggal_order) = ? OR ? = '') AND 
        (YEAR(tanggal_order) = ? OR ? = '') AND 
        (id_barang LIKE ? OR nama_barang LIKE ? OR tipe_barang LIKE ? OR nama_toko LIKE ? OR ekspedisi LIKE ?)");
    $stmt->execute([$bulan, $bulan, $tahun, $tahun, "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
    exportToCSV($stmt);
    exit;
}

// Query data dengan filter dan pagination
$stmt = $pdo->prepare("SELECT * FROM items 
    WHERE (MONTH(tanggal_order) = :bulan OR :bulan = '') 
    AND (YEAR(tanggal_order) = :tahun OR :tahun = '') 
    AND (id_barang LIKE :search OR nama_barang LIKE :search 
        OR tipe_barang LIKE :search OR nama_toko LIKE :search 
        OR ekspedisi LIKE :search)
    LIMIT :limit OFFSET :offset");

$stmt->bindValue(':bulan', $bulan);
$stmt->bindValue(':tahun', $tahun);
$stmt->bindValue(':search', "%$search%");
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll();


// Hitung total data untuk pagination
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM items WHERE 
    (MONTH(tanggal_order) = ? OR ? = '') AND 
    (YEAR(tanggal_order) = ? OR ? = '') AND 
    (id_barang LIKE ? OR nama_barang LIKE ? OR tipe_barang LIKE ? OR nama_toko LIKE ? OR ekspedisi LIKE ?)");
$totalStmt->execute([$bulan, $bulan, $tahun, $tahun, "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Barang Belanja</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <!-- Side Panel -->
    <div class="bg-dark text-white" style="width: 200px; min-height: 100vh;">
        <h4 class="text-center p-3">Receiving</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="../index.php" class="nav-link text-white">Storage</a></li>
            <li class="nav-item"><a href="tambah_barang.php" class="nav-link text-white">Tambah Barang</a></li>
           <!-- <li class="nav-item"><a href="qc_dashboard.php" class="nav-link text-white">QC Dashboard</a></li>
            <li class="nav-item"><a href="barang_masuk.php" class="nav-link text-white">Barang Masuk Gudang</a></li> -->
            <li class="nav-item"><a href="?export=csv" class="nav-link text-white">Ekspor ke CSV</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="mb-4 text-center">Dashboard Barang Belanja</h2>

        <!-- Form untuk Filter -->
        <form class="form-inline mb-4" method="GET">
            <select name="bulan" class="form-control mr-2">
                <option value="">Semua Bulan</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo sprintf("%02d", $i); ?>" <?php echo ($bulan == sprintf("%02d", $i)) ? 'selected' : ''; ?>>
                        <?php echo date("F", mktime(0, 0, 0, $i, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="tahun" class="form-control mr-2">
                <option value="">Semua Tahun</option>
                <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($tahun == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <input type="text" name="search" class="form-control mr-2" placeholder="Cari Barang..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Cari & Filter</button>
        </form>

        <!-- Tabel Data -->
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Type</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Nama Toko</th>
                    <th>Ekspedisi</th>
                    <th>Belanja Via</th>
                    <th>Petugas</th>
                    <th>Tanggal Order</th>
                    <th>Tanggal Datang</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipe_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['stok']); ?></td>
                            <td><?php echo htmlspecialchars($row['satuan_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
                            <td><?php echo htmlspecialchars($row['ekspedisi']); ?></td>
                            <td><?php echo htmlspecialchars($row['belanja_via']); ?></td>
                            <td><?php echo htmlspecialchars($row['siapa_order']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_order']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_datang']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" class="text-center">Tidak ada data ditemukan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
