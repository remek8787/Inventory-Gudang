<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header('Location: login.php');
    exit;
}
?>
<h1>Selamat Datang di Dashboard Teknisi, <?php echo $_SESSION['username']; ?>!</h1>
<a href="order_barang_keluar.php">Order Barang Keluar</a>
<br>
<a href="logout.php">Logout</a>
