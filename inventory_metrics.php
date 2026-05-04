<?php
session_start();
if (!isset($_SESSION['username'])) { header('Location: login.php'); exit(); }
require 'belanja/db.php';
function scalarValue($pdo, $sql, $fallback = 0) {
    try { $stmt = $pdo->query($sql); $value = $stmt->fetchColumn(); return $value === false ? $fallback : $value; }
    catch (Throwable $e) { return $fallback; }
}
$totalItems = scalarValue($pdo, "SELECT COUNT(*) FROM items");
$waitingQc = scalarValue($pdo, "SELECT COUNT(*) FROM items WHERE qc_status LIKE '%Menunggu%' OR qc_status IS NULL OR qc_status = ''");
$totalGudang = scalarValue($pdo, "SELECT COALESCE(SUM(stok),0) FROM barang_masuk_gudang");
$barangKeluarBulanIni = scalarValue($pdo, "SELECT COUNT(*) FROM barang_keluar WHERE MONTH(tanggal_keluar)=MONTH(CURDATE()) AND YEAR(tanggal_keluar)=YEAR(CURDATE())");
$stokRendah = [];
try {
    $stmt = $pdo->query("SELECT tipe_barang, SUM(stok) total_stok FROM barang_masuk_gudang GROUP BY tipe_barang HAVING total_stok <= 2 ORDER BY total_stok ASC, tipe_barang ASC LIMIT 8");
    $stokRendah = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {}
$recentKeluar = [];
try {
    $stmt = $pdo->query("SELECT id_barang, nama_teknisi, penggunaan, tanggal_keluar FROM barang_keluar ORDER BY tanggal_keluar DESC LIMIT 8");
    $recentKeluar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {}
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(compact('totalItems','waitingQc','totalGudang','barangKeluarBulanIni','stokRendah','recentKeluar'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Statistik Inventory DSG</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/dsg-modern.css" rel="stylesheet"></head>
<body><div class="container py-5">
<div class="dsg-shell-note"><strong>Dashboard Statistik</strong><br>Halaman ini read-only: hanya membaca data untuk ringkasan, tidak mengubah database.</div>
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="page-title mb-0">Statistik Inventory</h1><a href="index.php" class="btn btn-primary">← Dashboard</a></div>
<div class="dsg-kpi-grid">
<div class="dsg-kpi"><small>Total Barang Input</small><br><b><?php echo (int)$totalItems; ?></b></div>
<div class="dsg-kpi"><small>Waiting QC</small><br><b><?php echo (int)$waitingQc; ?></b></div>
<div class="dsg-kpi"><small>Total Stok Gudang</small><br><b><?php echo (int)$totalGudang; ?></b></div>
<div class="dsg-kpi"><small>Keluar Bulan Ini</small><br><b><?php echo (int)$barangKeluarBulanIni; ?></b></div>
</div>
<div class="row"><div class="col-md-6 mb-4"><div class="card"><div class="card-header font-weight-bold">Stok Rendah</div><div class="card-body"><table class="table table-sm"><thead><tr><th>Tipe</th><th>Stok</th></tr></thead><tbody><?php if ($stokRendah): foreach ($stokRendah as $r): ?><tr><td><?php echo htmlspecialchars($r['tipe_barang']); ?></td><td class="text-danger font-weight-bold"><?php echo htmlspecialchars($r['total_stok']); ?></td></tr><?php endforeach; else: ?><tr><td colspan="2" class="text-muted">Tidak ada stok rendah.</td></tr><?php endif; ?></tbody></table></div></div></div>
<div class="col-md-6 mb-4"><div class="card"><div class="card-header font-weight-bold">Barang Keluar Terbaru</div><div class="card-body"><table class="table table-sm"><thead><tr><th>ID</th><th>Teknisi</th><th>Tanggal</th></tr></thead><tbody><?php if ($recentKeluar): foreach ($recentKeluar as $r): ?><tr><td><?php echo htmlspecialchars($r['id_barang']); ?></td><td><?php echo htmlspecialchars($r['nama_teknisi']); ?></td><td><?php echo htmlspecialchars($r['tanggal_keluar']); ?></td></tr><?php endforeach; else: ?><tr><td colspan="3" class="text-muted">Belum ada data.</td></tr><?php endif; ?></tbody></table></div></div></div></div>
</div><script src="/assets/js/dsg-modern.js"></script></body></html>
