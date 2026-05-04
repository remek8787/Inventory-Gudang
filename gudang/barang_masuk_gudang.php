<?php
require '../belanja/db.php';

$message = '';

// Barang dikirim dari Selesai QC ke Gudang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_to_gudang'])) {
    $id_barang = $_POST['id_barang'] ?? '';
    if ($id_barang !== '') {
        $checkQuery = $pdo->prepare("SELECT id_barang FROM barang_masuk_gudang WHERE id_barang = ? LIMIT 1");
        $checkQuery->execute([$id_barang]);
        if ($checkQuery->rowCount() == 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO barang_masuk_gudang (id_barang, nama_barang, tipe_barang, mac_address, stok, satuan_barang, nama_toko, ekspedisi, belanja_via, siapa_order, petugas_qc, tanggal_order, tanggal_datang, tanggal_qc, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['id_barang'] ?? '', $_POST['nama_barang'] ?? '', $_POST['tipe_barang'] ?? '', $_POST['mac_address'] ?? '',
                    $_POST['stok'] ?? 0, $_POST['satuan_barang'] ?? '', $_POST['nama_toko'] ?? '', $_POST['ekspedisi'] ?? '', $_POST['belanja_via'] ?? '',
                    $_POST['siapa_order'] ?? '', $_POST['petugas_qc'] ?? '', $_POST['tanggal_order'] ?? null, $_POST['tanggal_datang'] ?? null, $_POST['tanggal_qc'] ?? null, $_POST['keterangan'] ?? ''
                ]);
                $message = 'Barang berhasil masuk gudang.';
            } catch (PDOException $e) {
                error_log('Insert barang_masuk_gudang failed: ' . $e->getMessage());
                $message = 'Barang gagal masuk gudang. Silakan cek data.';
            }
        } else {
            $message = 'Barang sudah ada di gudang.';
        }
    }
}

// Tombol Delete = kembalikan lagi ke list Selesai QC, bukan hapus permanen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_to_qc'])) {
    $id_barang = $_POST['id_barang'] ?? '';
    if ($id_barang !== '') {
        try {
            $pdo->beginTransaction();
            $itemStmt = $pdo->prepare("SELECT * FROM barang_masuk_gudang WHERE id_barang = ? LIMIT 1");
            $itemStmt->execute([$id_barang]);
            $item = $itemStmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                // Pastikan item asal di tabel items kembali berstatus Selesai QC agar muncul lagi di list_selesai_qc.php.
                $updateItem = $pdo->prepare("UPDATE items SET qc_status = 'Selesai QC' WHERE id_barang = ?");
                $updateItem->execute([$id_barang]);

                // Hapus dari tabel gudang agar secara alur balik ke Selesai QC.
                $deleteGudang = $pdo->prepare("DELETE FROM barang_masuk_gudang WHERE id_barang = ?");
                $deleteGudang->execute([$id_barang]);

                $pdo->commit();
                $message = 'Barang dikembalikan ke Selesai QC.';
            } else {
                $pdo->rollBack();
                $message = 'Barang tidak ditemukan di gudang.';
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            error_log('Return to QC failed: ' . $e->getMessage());
            $message = 'Gagal mengembalikan barang ke Selesai QC.';
        }
    }
}

$search_id = trim($_GET['id_barang'] ?? '');
$search_nama = trim($_GET['nama_barang'] ?? '');
$search_mac = trim($_GET['mac_address'] ?? '');
$search_ekspedisi = trim($_GET['ekspedisi'] ?? '');
$search_tahun = trim($_GET['tahun'] ?? '');
$search_bulan = trim($_GET['bulan'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$where = 'WHERE 1=1';
$params = [];
if ($search_id !== '') { $where .= ' AND id_barang LIKE :id_barang'; $params[':id_barang'] = "%$search_id%"; }
if ($search_nama !== '') { $where .= ' AND nama_barang LIKE :nama_barang'; $params[':nama_barang'] = "%$search_nama%"; }
if ($search_mac !== '') { $where .= ' AND mac_address LIKE :mac_address'; $params[':mac_address'] = "%$search_mac%"; }
if ($search_ekspedisi !== '') { $where .= ' AND ekspedisi LIKE :ekspedisi'; $params[':ekspedisi'] = "%$search_ekspedisi%"; }
if ($search_tahun !== '') { $where .= ' AND YEAR(tanggal_order) = :tahun'; $params[':tahun'] = (int)$search_tahun; }
if ($search_bulan !== '') { $where .= ' AND MONTH(tanggal_order) = :bulan'; $params[':bulan'] = (int)$search_bulan; }

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM barang_masuk_gudang $where");
foreach ($params as $key => $value) { $countStmt->bindValue($key, $value); }
$countStmt->execute();
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$stmt = $pdo->prepare("SELECT * FROM barang_masuk_gudang $where ORDER BY tanggal_qc DESC, tanggal_datang DESC, id_barang ASC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderGudangRows(array $data): string {
    ob_start();
    if ($data) {
        foreach ($data as $row) {
            $id = htmlspecialchars($row['id_barang'] ?? '', ENT_QUOTES, 'UTF-8');
            echo '<tr>';
            foreach (['id_barang','nama_barang','tipe_barang','mac_address','stok','satuan_barang','nama_toko','ekspedisi','belanja_via','siapa_order','petugas_qc','tanggal_order','tanggal_datang','tanggal_qc','keterangan'] as $col) {
                echo '<td>' . htmlspecialchars($row[$col] ?? '') . '</td>';
            }
            echo '<td style="min-width:140px">';
            echo '<form method="POST" action="" onsubmit="return confirm(\'Kembalikan barang ini ke Selesai QC?\')">';
            echo '<input type="hidden" name="id_barang" value="' . $id . '">';
            echo '<button type="submit" name="return_to_qc" class="btn btn-danger btn-sm">Delete / Balik QC</button>';
            echo '</form>';
            echo '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="16" class="text-center text-muted py-4">Tidak ada data ditemukan.</td></tr>';
    }
    return ob_get_clean();
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'html' => renderGudangRows($data), 'total' => $totalRows, 'page' => $page, 'totalPages' => $totalPages]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang Masuk Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div id="sidebar" class="sidebar" style="width:250px;">
        <h4 class="text-center text-white px-3">Dashboard Gudang</h4>
        <a href="/quality_control/list_selesai_qc.php">Back To Selesai QC</a>
        <a href="/index.php">Back To Storage</a>
        <a href="/gudang/stok_gudang.php">Ship Stock</a>
        <a href="/gudang/barang_keluar.php">Ship Out</a>
        <a href="/gudang/riwayat_pengeluaran.php">Shipment History</a>
    </div>

    <main class="content">
        <div class="dsg-shell-note"><strong>Daftar Barang Masuk Gudang</strong><br>Halaman ini sudah dipaginasi dan search AJAX. Tombol Delete berarti barang dikembalikan ke list Selesai QC.</div>
        <h2 class="text-center page-title mb-2">Daftar Barang Masuk Gudang</h2>
        <div id="barang-masuk-counter" class="text-center text-muted mb-4">Menampilkan <?= (int)$totalRows ?> data barang gudang</div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="GET" class="form-inline mb-3 dsg-ajax-search" data-target="#barang-masuk-body" data-counter="#barang-masuk-counter">
            <input type="text" name="id_barang" placeholder="ID Barang" class="form-control mr-2 mb-2" value="<?= htmlspecialchars($search_id) ?>">
            <input type="text" name="nama_barang" placeholder="Nama Barang" class="form-control mr-2 mb-2" value="<?= htmlspecialchars($search_nama) ?>">
            <input type="text" name="mac_address" placeholder="MAC Address" class="form-control mr-2 mb-2" value="<?= htmlspecialchars($search_mac) ?>">
            <select name="bulan" class="form-control mr-2 mb-2">
                <option value="">Bulan</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= $search_bulan == $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                <?php endfor; ?>
            </select>
            <input type="number" name="tahun" placeholder="Tahun" class="form-control mr-2 mb-2" value="<?= htmlspecialchars($search_tahun) ?>">
            <input type="text" name="ekspedisi" placeholder="Ekspedisi" class="form-control mr-2 mb-2" value="<?= htmlspecialchars($search_ekspedisi) ?>">
            <button type="submit" class="btn btn-primary mb-2">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Tipe Barang</th>
                        <th>Mac Address</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Nama Toko</th>
                        <th>Ekspedisi</th>
                        <th>Belanja Via</th>
                        <th>Siapa Order</th>
                        <th>Petugas QC</th>
                        <th>Tanggal Order</th>
                        <th>Tanggal Datang</th>
                        <th>Tanggal QC</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="barang-masuk-body">
                    <?= renderGudangRows($data) ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-center text-muted">Halaman <?= (int)$page ?> dari <?= (int)$totalPages ?></div>
        <nav class="mt-2 d-flex justify-content-center">
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-light' ?> mx-1" href="?page=<?= $i ?>&id_barang=<?= urlencode($search_id) ?>&nama_barang=<?= urlencode($search_nama) ?>&mac_address=<?= urlencode($search_mac) ?>&bulan=<?= urlencode($search_bulan) ?>&tahun=<?= urlencode($search_tahun) ?>&ekspedisi=<?= urlencode($search_ekspedisi) ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    </main>
</div>
<script src="/assets/js/dsg-modern.js"></script>
<script src="/assets/js/dsg-ajax-search.js"></script>
</body>
</html>
