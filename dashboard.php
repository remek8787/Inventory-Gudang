<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Cek role pengguna
$role = $_SESSION['role']; // Pastikan role disimpan di session saat login

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .dashboard-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        .dashboard-container h1 {
            margin-bottom: 20px;
        }
        .dashboard-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .dashboard-container a:hover {
            background-color: #c82333;
        }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>

        <!-- Konten berdasarkan role -->
        <?php if ($role == 'administrator') { ?>
            <p>You are an Administrator. You have full access.</p>
            <!-- Tambahkan menu atau tombol untuk administrator -->
        <?php } elseif ($role == 'kepala_gudang') { ?>
            <p>You are the Head of Warehouse. Here is your dashboard.</p>
            <!-- Tambahkan menu atau tombol untuk kepala gudang -->
        <?php } elseif ($role == 'petugas_qc') { ?>
            <p>You are a QC Officer. Manage the QC processes here.</p>
            <!-- Tambahkan menu atau tombol untuk petugas QC -->
        <?php } elseif ($role == 'teknisi') { ?>
            <p>You are a Technician. You can request items here.</p>
            <!-- Tambahkan menu atau tombol untuk Teknisi -->
            <a href="teknisi/order_barang_keluar.php">Order Barang Keluar</a>
        <?php } else { ?>
            <p>You have a standard user role. Limited access.</p>
            <!-- Tambahkan menu atau tombol untuk role standar atau lainnya -->
        <?php } ?>
        
        <a href="logout.php">Logout</a>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
