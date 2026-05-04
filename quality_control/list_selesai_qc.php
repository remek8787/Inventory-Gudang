<?php
require '../belanja/db.php';

$message = '';

// Logika untuk mengembalikan barang ke QC Lolos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['undo_qc'])) {
    $id_barang = $_POST['id_barang'] ?? '';
    $alasan = $_POST['alasan'] ?? '';

    if ($id_barang !== '') {
        $stmt = $pdo->prepare("UPDATE items SET qc_status = 'Lolos QC', alasan_undo_qc = ? WHERE id_barang = ?");
        $stmt->execute([$alasan, $id_barang]);
        $message = 'Barang berhasil dikembalikan ke QC Lolos.';
    }
}

$search = trim($_GET['id_barang'] ?? $_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$where = "WHERE qc_status = 'Selesai QC'";
$params = [];
if ($search !== '') {
    $where .= " AND (id_barang LIKE :search OR nama_barang LIKE :search OR tipe_barang LIKE :search OR mac_address LIKE :search OR nama_toko LIKE :search OR ekspedisi LIKE :search OR siapa_order LIKE :search OR petugas_qc LIKE :search)";
    $params[':search'] = "%$search%";
}

$columns = "id_barang, nama_barang, tipe_barang, mac_address, stok, satuan_barang, nama_toko, ekspedisi, belanja_via, siapa_order, petugas_qc, tanggal_order, tanggal_datang, tanggal_qc, keterangan";

// Export CSV berdasarkan filter, bukan semua data mentah tanpa filter
if (isset($_GET['export'])) {
    $exportStmt = $pdo->prepare("SELECT $columns FROM items $where ORDER BY tanggal_qc DESC, tanggal_datang DESC, id_barang ASC");
    foreach ($params as $key => $value) { $exportStmt->bindValue($key, $value); }
    $exportStmt->execute();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=barang_selesai_qc.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Barang', 'Nama Barang', 'Tipe', 'Mac Address', 'Stok', 'Satuan', 'Nama Toko', 'Ekspedisi', 'Belanja Via', 'Petugas Order', 'Petugas QC', 'Tanggal Order', 'Tanggal Datang', 'Tanggal QC', 'Keterangan']);
    while ($row = $exportStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id_barang'], $row['nama_barang'], $row['tipe_barang'], $row['mac_address'], $row['stok'], $row['satuan_barang'],
            $row['nama_toko'], $row['ekspedisi'], $row['belanja_via'], $row['siapa_order'], $row['petugas_qc'],
            $row['tanggal_order'], $row['tanggal_datang'], $row['tanggal_qc'], $row['keterangan']
        ]);
    }
    fclose($output);
    exit;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM items $where");
foreach ($params as $key => $value) { $countStmt->bindValue($key, $value); }
$countStmt->execute();
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$stmt = $pdo->prepare("SELECT $columns FROM items $where ORDER BY tanggal_qc DESC, tanggal_datang DESC, id_barang ASC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderRows(array $data): string {
    ob_start();
    if ($data) {
        foreach ($data as $row) {
            $id = htmlspecialchars($row['id_barang'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
            <tr>
                <td><?= htmlspecialchars($row['id_barang'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['nama_barang'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['tipe_barang'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['mac_address'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['stok'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['satuan_barang'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['nama_toko'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['ekspedisi'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['belanja_via'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['siapa_order'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['petugas_qc'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['tanggal_order'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['tanggal_datang'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['tanggal_qc'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                <td style="min-width:170px">
                    <form method="POST" action="" class="mb-2">
                        <input type="hidden" name="id_barang" value="<?= $id ?>">
                        <input type="text" name="alasan" class="form-control form-control-sm" placeholder="Alasan Undo">
                        <button type="submit" name="undo_qc" class="btn btn-warning btn-sm mt-2">Kembalikan ke QC</button>
                    </form>
                    <form method="POST" action="../gudang/barang_masuk_gudang.php">
                        <input type="hidden" name="id_barang" value="<?= $id ?>">
                        <input type="hidden" name="nama_barang" value="<?= htmlspecialchars($row['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="tipe_barang" value="<?= htmlspecialchars($row['tipe_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="mac_address" value="<?= htmlspecialchars($row['mac_address'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="stok" value="<?= htmlspecialchars($row['stok'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="satuan_barang" value="<?= htmlspecialchars($row['satuan_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="nama_toko" value="<?= htmlspecialchars($row['nama_toko'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="ekspedisi" value="<?= htmlspecialchars($row['ekspedisi'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="belanja_via" value="<?= htmlspecialchars($row['belanja_via'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="siapa_order" value="<?= htmlspecialchars($row['siapa_order'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="petugas_qc" value="<?= htmlspecialchars($row['petugas_qc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="tanggal_order" value="<?= htmlspecialchars($row['tanggal_order'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="tanggal_datang" value="<?= htmlspecialchars($row['tanggal_datang'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="tanggal_qc" value="<?= htmlspecialchars($row['tanggal_qc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="keterangan" value="<?= htmlspecialchars($row['keterangan'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-success btn-sm">Kirim ke Gudang</button>
                    </form>
                </td>
            </tr>
            <?php
        }
    } else {
        echo '<tr><td colspan="16" class="text-center text-muted py-4">Tidak ada data ditemukan.</td></tr>';
    }
    return ob_get_clean();
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'html' => renderRows($data),
        'total' => $totalRows,
        'page' => $page,
        'totalPages' => $totalPages,
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Barang Selesai QC</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="sidebar" style="width:240px;">
        <h4 class="text-center text-white px-3">Selesai QC</h4>
        <a href="/quality_control/qc_dashboard.php">Back Dashboard QC</a>
        <a href="/gudang/barang_masuk_gudang.php">Ke Dashboard Gudang</a>
        <a href="/index.php">Dashboard Utama</a>
    </div>

    <main class="content" style="margin-left:0;">
        <div class="dsg-shell-note"><strong>List Barang Selesai QC</strong><br>Halaman ini sudah dipaginasi agar tidak berat. Gunakan search untuk mencari ID, nama, tipe, MAC, toko, ekspedisi, petugas order, atau petugas QC.</div>

        <h2 class="mb-2 text-center page-title">List Barang Selesai QC</h2>
        <div id="selesai-qc-counter" class="text-center text-muted mb-4">Menampilkan <?= (int)$totalRows ?> data selesai QC</div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="GET" action="list_selesai_qc.php" class="form-inline mb-4 dsg-ajax-search" data-target="#selesai-qc-body" data-counter="#selesai-qc-counter">
            <input type="text" class="form-control mr-2 mb-2" name="id_barang" placeholder="Cari ID/Nama/Tipe/MAC/Petugas" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary mb-2">Cari</button>
            <a href="list_selesai_qc.php?export=true&id_barang=<?= urlencode($search) ?>" class="btn btn-success ml-2 mb-2">Export CSV</a>
        </form>

        <div class="table-responsive">
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="selesai-qc-body">
                    <?= renderRows($data) ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-center text-muted">Halaman <?= (int)$page ?> dari <?= (int)$totalPages ?></div>
        <nav class="mt-2 d-flex justify-content-center">
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-light' ?> mx-1" href="?page=<?= $i ?>&id_barang=<?= urlencode($search) ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    </main>
</div>
<script src="/assets/js/dsg-modern.js"></script>
<script src="/assets/js/dsg-ajax-search.js"></script>
</body>
</html>
