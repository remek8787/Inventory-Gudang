<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Dapatkan role dari session
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gudang V1</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 15px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar i {
            margin-right: 10px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .dashboard-header {
            margin-bottom: 30px;
        }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>STORAGE DSG</h4>

        <!-- Dropdown Menu Receiving -->
        <div class="dropdown mb-3">
            <a href="#inputBarangMenu" data-toggle="collapse" class="d-block text-white p-2">
                <i class="fas fa-boxes"></i> Receiving <i class="fas fa-chevron-down float-right"></i>
            </a>
            <div id="inputBarangMenu" class="collapse pl-4">
    <a href="belanja/tambah_barang.php" class="d-block text-white py-1">
        <i class="fas fa-plus"></i> Tambah Barang Belanja
    </a>
    <a href="belanja/dhasboar_barang_belanja.php" class="d-block text-white py-1">
        <i class="fas fa-box"></i> Barang Belanja
    </a>
    </div>
        </div>

        <!-- Dropdown Menu Quality Control -->
<?php if ($role == 'administrator' || $role == 'petugas_qc') { ?>
    <div class="dropdown mb-3">
        <a href="#qualityControlMenu" data-toggle="collapse" class="d-block text-white p-2">
            <i class="fas fa-tasks"></i> Quality Control <i class="fas fa-chevron-down float-right"></i>
        </a>
        <div id="qualityControlMenu" class="collapse pl-4">
            <a href="quality_control/qc_dashboard.php" class="d-block text-white py-1">
                <i class="fas fa-tachometer-alt"></i> Dashboard QC
            </a>
          <!--  <a href="quality_control/qc_lolos.php" class="d-block text-white py-1">
                <i class="fas fa-check"></i> QC Lolos
            </a>
            <a href="quality_control/qc_retur.php" class="d-block text-white py-1">
                <i class="fas fa-times"></i> QC Tidak Lolos 
            </a>
            <a href="quality_control/list_selesai_qc.php" class="d-block text-white py-1">
                <i class="fas fa-clock"></i> Barang Siap ke Gudang
            </a> -->
        </div>
    </div>
<?php } ?>

        <!-- Menu Gudang -->
     <!--   <?php if ($role == 'administrator' || $role == 'kepala_gudang') { ?>
            <a href="quality_control/list_selesai_qc.php"><i class="fas fa-clock"></i> Barang Siap ke Gudang</a> -->
            <a href="gudang/barang_masuk_gudang.php"><i class="fas fa-warehouse"></i> Finish Good</a>
        <?php } ?>
        
        <!-- Logout -->
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
<div class="dsg-shell-note"><strong>Reminder Admin:</strong> input barang → QC → finish good → barang keluar. Data rapi = stok aman dan mudah dilacak.<br><span class="dsg-dashboard-build">Build: v1.2.6-modern · Build 2026.05.05.0235</span></div>
        <div class="dsg-kpi-grid">
            <div class="dsg-kpi"><small>Statistik</small><br><b>Live</b><p class="mb-0 text-muted">Pantau stok, waiting QC, dan barang keluar.</p><a href="inventory_metrics.php" class="btn btn-primary btn-sm mt-3">Buka Statistik</a></div>
            <div class="dsg-kpi"><small>Tutorial</small><br><b>Admin</b><p class="mb-0 text-muted">Panduan alur receiving, QC, gudang, dan pengeluaran.</p><a href="admin_tutorial.php" class="btn btn-success btn-sm mt-3">Buka Tutorial</a></div>
            <div class="dsg-kpi"><small>QR Code</small><br><b>Generator</b><p class="mb-0 text-muted">Buat PDF QR label untuk ID barang dan stok inventory.</p><a href="/qr-code-ganrate/" class="btn btn-warning btn-sm mt-3">Buka QR Generator</a></div>
        </div>
    <h2 class="text-center dashboard-header">
    DSG Inventory Control Center
</h2>
<p class="text-center" style="font-size: 0.8rem; color: gray;">
    v1.2.6-modern · Build 2026.05.05.0235 — Receiving · QC · Gudang · Ship Out
</p>
        <div class="row">
            <!-- Kartu Dashboard -->
            <?php if ($role == 'administrator' || $role == 'kepala_gudang') { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-box fa-2x text-primary mb-3"></i>
                            <h5 class="card-title">Barang Belanja</h5>
                            <a href="belanja/dhasboar_barang_belanja.php" class="btn btn-primary">Lihat Barang Belanja</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-plus-circle fa-2x text-success mb-3"></i>
                            <h5 class="card-title">Tambah Barang</h5>
                            <a href="belanja/tambah_barang.php" class="btn btn-success">Tambah Barang</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            
            <!-- Kartu QC -->
            <?php if ($role == 'administrator' || $role == 'petugas_qc') { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x text-warning mb-3"></i>
                            <h5 class="card-title">QC Lolos</h5>
                            <a href="quality_control/qc_lolos.php" class="btn btn-warning">Lihat QC Lolos</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
