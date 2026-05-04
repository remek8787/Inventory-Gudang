<?php
require '../belanja/db.php';

$start_date = trim($_GET['start_date'] ?? '');
$end_date = trim($_GET['end_date'] ?? '');
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];
if ($search !== '') {
    $where[] = "(bk.id_barang LIKE :search OR COALESCE(bmg.nama_barang, bk.nama_barang) LIKE :search OR COALESCE(bmg.tipe_barang, bk.tipe_barang) LIKE :search OR COALESCE(bmg.mac_address, bk.mac_address) LIKE :search OR bk.nama_teknisi LIKE :search OR bk.petugas_admin LIKE :search OR bk.keterangan LIKE :search OR bk.penggunaan LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($start_date !== '' && $end_date !== '') {
    $where[] = "DATE(bk.tanggal_keluar) BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $start_date;
    $params[':end_date'] = $end_date;
} elseif ($start_date !== '') {
    $where[] = "DATE(bk.tanggal_keluar) >= :start_date";
    $params[':start_date'] = $start_date;
} elseif ($end_date !== '') {
    $where[] = "DATE(bk.tanggal_keluar) <= :end_date";
    $params[':end_date'] = $end_date;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$selectSql = "
    SELECT
        bk.id_barang,
        COALESCE(bmg.nama_barang, bk.nama_barang) AS nama_barang,
        COALESCE(bmg.tipe_barang, bk.tipe_barang) AS tipe_barang,
        COALESCE(bmg.mac_address, bk.mac_address) AS mac_address,
        bmg.nama_toko,
        bmg.ekspedisi,
        bmg.belanja_via,
        bmg.siapa_order,
        bmg.petugas_qc,
        bmg.tanggal_order,
        bmg.tanggal_datang,
        bmg.tanggal_qc,
        bk.nama_teknisi,
        bk.keterangan,
        bk.penggunaan,
        bk.petugas_admin,
        bk.tanggal_keluar
    FROM barang_keluar bk
    LEFT JOIN barang_masuk_gudang bmg ON bk.id_barang = bmg.id_barang
    $whereSql
";

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM barang_keluar bk LEFT JOIN barang_masuk_gudang bmg ON bk.id_barang = bmg.id_barang $whereSql");
foreach ($params as $key => $value) { $countStmt->bindValue($key, $value); }
$countStmt->execute();
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

// Export CSV/XML mengikuti filter, tetap read-only
if (isset($_GET['export']) && in_array($_GET['export'], ['csv', 'xml'], true)) {
    $exportStmt = $pdo->prepare($selectSql . " ORDER BY bk.tanggal_keluar DESC");
    foreach ($params as $key => $value) { $exportStmt->bindValue($key, $value); }
    $exportStmt->execute();
    $rows = $exportStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_GET['export'] === 'xml') {
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename=riwayat_pengeluaran.xml');
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<riwayat_pengeluaran>\n";
        foreach ($rows as $row) {
            echo "  <barang>\n";
            foreach ($row as $key => $value) {
                echo "    <" . htmlspecialchars($key) . ">" . htmlspecialchars((string)$value) . "</" . htmlspecialchars($key) . ">\n";
            }
            echo "  </barang>\n";
        }
        echo "</riwayat_pengeluaran>";
        exit;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=riwayat_pengeluaran.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Barang','Nama Barang','Tipe Barang','Mac Address','Nama Toko','Ekspedisi','Belanja Via','Siapa Order','Petugas QC','Tanggal Order','Tanggal Datang','Tanggal QC','Nama Teknisi','Keterangan','Penggunaan','Petugas Admin','Tanggal Keluar']);
    foreach ($rows as $row) { fputcsv($output, $row); }
    fclose($output);
    exit;
}

$stmt = $pdo->prepare($selectSql . " ORDER BY bk.tanggal_keluar DESC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderRiwayatRows(array $data): string {
    ob_start();
    if ($data) {
        foreach ($data as $row) {
            echo '<tr>';
            foreach (['id_barang','nama_barang','tipe_barang','mac_address','nama_toko','ekspedisi','belanja_via','siapa_order','petugas_qc','tanggal_order','tanggal_datang','tanggal_qc','nama_teknisi','keterangan','penggunaan','petugas_admin','tanggal_keluar'] as $col) {
                echo '<td>' . htmlspecialchars($row[$col] ?? '') . '</td>';
            }
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="17" class="text-center text-muted py-4">Tidak ada data yang ditemukan.</td></tr>';
    }
    return ob_get_clean();
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>true,'html'=>renderRiwayatRows($data),'total'=>$totalRows,'page'=>$page,'totalPages'=>$totalPages]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengeluaran Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="dsg-app">
<aside class="dsg-pro-sidebar">
    <div class="brand">📦 DSG Inventory</div>
    <a href="/index.php">Dashboard Utama</a>
    <a href="/gudang/barang_masuk_gudang.php">Barang Masuk Gudang</a>
    <a href="/gudang/stok_gudang.php">Stok Gudang</a>
    <a href="/gudang/barang_keluar.php">Ship Out</a>
    <a href="/gudang/riwayat_pengeluaran.php" class="active">Riwayat Pengeluaran</a>
    <a href="/quality_control/list_selesai_qc.php">Selesai QC</a>
</aside>

<main class="dsg-pro-main">
    <div class="dsg-page-head">
        <div>
            <h1>Riwayat Pengeluaran Barang</h1>
            <p class="dsg-page-subtitle">Live search + pagination 25 data per halaman. Halaman ini hanya membaca data pengeluaran.</p>
        </div>
        <span class="dsg-badge-soft" id="riwayat-pengeluaran-counter">Menampilkan <?= (int)$totalRows ?> data pengeluaran</span>
    </div>

    <section class="dsg-panel">
        <form method="GET" action="riwayat_pengeluaran.php" class="dsg-ajax-search" data-target="#riwayat-pengeluaran-body" data-counter="#riwayat-pengeluaran-counter">
            <div class="dsg-form-grid">
                <div>
                    <label>Cari Global</label>
                    <input type="text" name="search" class="form-control" placeholder="ID / nama / tipe / MAC / teknisi / penggunaan" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div>
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div>
                    <label>Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                </div>
            </div>
            <div class="dsg-actions mt-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?export=csv&search=<?= urlencode($search) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="btn btn-success">Export CSV</a>
                <a href="?export=xml&search=<?= urlencode($search) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="btn btn-light">Export XML</a>
            </div>
        </form>
    </section>

    <div class="dsg-table-card table-responsive">
        <table class="table table-striped table-bordered mb-0">
            <thead>
                <tr>
                    <th>ID Barang</th><th>Nama Barang</th><th>Tipe Barang</th><th>Mac Address</th><th>Nama Toko</th><th>Ekspedisi</th><th>Belanja Via</th><th>Siapa Order</th><th>Petugas QC</th><th>Tanggal Order</th><th>Tanggal Datang</th><th>Tanggal QC</th><th>Nama Teknisi</th><th>Keterangan</th><th>Penggunaan</th><th>Petugas Admin</th><th>Tanggal Keluar</th>
                </tr>
            </thead>
            <tbody id="riwayat-pengeluaran-body">
                <?= renderRiwayatRows($data) ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-center text-muted">Halaman <?= (int)$page ?> dari <?= (int)$totalPages ?></div>
    <nav class="mt-2 d-flex justify-content-center flex-wrap">
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-light' ?> mx-1 mb-1" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </nav>
</main>
<script src="/assets/js/dsg-modern.js"></script>
<script src="/assets/js/dsg-ajax-search.js"></script>
</body>
</html>
