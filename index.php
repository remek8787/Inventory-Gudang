<?php
session_start();
if (!isset($_SESSION['username'])) { header('Location: login.php'); exit(); }
$role = $_SESSION['role'] ?? 'user';
$username = $_SESSION['username'] ?? 'admin';
require_once 'belanja/db.php';
function dsgCount($pdo, $sql) { try { return (int)$pdo->query($sql)->fetchColumn(); } catch (Throwable $e) { return 0; } }
$totalItems = dsgCount($pdo, "SELECT COUNT(*) FROM items");
$waitingQc = dsgCount($pdo, "SELECT COUNT(*) FROM items WHERE qc_status LIKE '%Menunggu%' OR qc_status IS NULL OR qc_status='' ");
$selesaiQc = dsgCount($pdo, "SELECT COUNT(*) FROM items WHERE qc_status='Selesai QC'");
$stokGudang = dsgCount($pdo, "SELECT COALESCE(SUM(stok),0) FROM barang_masuk_gudang");
$keluarBulanIni = dsgCount($pdo, "SELECT COUNT(*) FROM barang_keluar WHERE MONTH(tanggal_keluar)=MONTH(CURDATE()) AND YEAR(tanggal_keluar)=YEAR(CURDATE())");
$build = 'v1.2.7-premium · Build 2026.05.05.0330';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSG Inventory Control Center</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="dsg-app dsg-home">
<aside class="dsg-pro-sidebar">
    <div class="brand">📦 DSG Inventory</div>
    <a href="/index.php" class="active"><i class="fas fa-chart-line"></i> Control Center</a>
    <a href="/belanja/tambah_barang.php"><i class="fas fa-plus-circle"></i> Receiving</a>
    <a href="/belanja/dhasboar_barang_belanja.php"><i class="fas fa-boxes"></i> Barang Belanja</a>
    <?php if ($role === 'administrator' || $role === 'petugas_qc'): ?>
        <a href="/quality_control/qc_dashboard.php"><i class="fas fa-tasks"></i> Quality Control</a>
        <a href="/quality_control/list_selesai_qc.php"><i class="fas fa-check-double"></i> Selesai QC</a>
    <?php endif; ?>
    <?php if ($role === 'administrator' || $role === 'kepala_gudang'): ?>
        <a href="/gudang/barang_masuk_gudang.php"><i class="fas fa-warehouse"></i> Gudang</a>
        <a href="/gudang/barang_keluar.php"><i class="fas fa-truck-loading"></i> Ship Out</a>
        <a href="/gudang/riwayat_pengeluaran.php"><i class="fas fa-history"></i> Riwayat</a>
    <?php endif; ?>
    <a href="/qr-code-ganrate/"><i class="fas fa-qrcode"></i> QR Generator</a>
    <a href="/admin_tutorial.php"><i class="fas fa-lightbulb"></i> Tutorial Admin</a>
    <a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</aside>

<main class="dsg-pro-main">
    <section class="dsg-hero-premium">
        <div>
            <span class="dsg-brand-pill">DENTA SEJAHTERA GROUP</span>
            <h1>Inventory Control Center</h1>
            <p>Receiving, Quality Control, Gudang, Ship Out, dan QR label dalam satu panel operasional yang ringan dan cepat.</p>
            <div class="dsg-actions mt-4">
                <a href="/belanja/tambah_barang.php" class="btn btn-primary">Input Barang</a>
                <a href="/quality_control/qc_dashboard.php" class="btn btn-light">Buka QC</a>
                <a href="/gudang/barang_masuk_gudang.php" class="btn btn-success">Gudang</a>
            </div>
        </div>
        <div class="dsg-hero-card">
            <small>Signed in as</small>
            <b><?= htmlspecialchars($username) ?></b>
            <span><?= htmlspecialchars($role) ?></span>
            <em><?= htmlspecialchars($build) ?></em>
        </div>
    </section>

    <section class="dsg-metric-grid">
        <div class="dsg-metric"><span>Total Input</span><b><?= $totalItems ?></b><small>Barang tercatat</small></div>
        <div class="dsg-metric"><span>Waiting QC</span><b><?= $waitingQc ?></b><small>Perlu dicek</small></div>
        <div class="dsg-metric"><span>Selesai QC</span><b><?= $selesaiQc ?></b><small>Siap masuk gudang</small></div>
        <div class="dsg-metric"><span>Stok Gudang</span><b><?= $stokGudang ?></b><small>Total unit finish good</small></div>
        <div class="dsg-metric"><span>Keluar Bulan Ini</span><b><?= $keluarBulanIni ?></b><small>Ship out berjalan</small></div>
    </section>

    <section class="dsg-workflow-grid">
        <a class="dsg-workflow-card" href="/belanja/tambah_barang.php"><i class="fas fa-dolly"></i><b>Receiving</b><span>Input barang, toko, ekspedisi, dan data order.</span></a>
        <a class="dsg-workflow-card" href="/quality_control/qc_dashboard.php"><i class="fas fa-clipboard-check"></i><b>Quality Control</b><span>Validasi barang sebelum masuk gudang.</span></a>
        <a class="dsg-workflow-card" href="/gudang/barang_masuk_gudang.php"><i class="fas fa-warehouse"></i><b>Gudang</b><span>Kelola finish good dan stok siap pakai.</span></a>
        <a class="dsg-workflow-card" href="/gudang/barang_keluar.php"><i class="fas fa-shipping-fast"></i><b>Ship Out</b><span>Keluarkan barang untuk teknisi/penggunaan.</span></a>
        <a class="dsg-workflow-card" href="/gudang/riwayat_pengeluaran.php"><i class="fas fa-file-export"></i><b>Riwayat</b><span>Live search, laporan, dan export pengeluaran.</span></a>
        <a class="dsg-workflow-card" href="/qr-code-ganrate/"><i class="fas fa-qrcode"></i><b>QR Generator</b><span>Generate PDF QR label untuk inventory.</span></a>
    </section>

    <section class="dsg-panel dsg-home-note">
        <strong>Alur utama:</strong> Receiving → QC → Selesai QC → Gudang → Ship Out. Pastikan ID/MAC, petugas, dan keterangan selalu lengkap agar stok mudah diaudit.
    </section>
</main>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
