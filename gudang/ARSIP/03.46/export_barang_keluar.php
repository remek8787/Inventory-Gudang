<?php
require '../belanja/db.php'; // Koneksi ke database

// Ekspor berdasarkan bulan dan tahun
if (isset($_POST['export'])) {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    
    // Query untuk mengekspor data berdasarkan bulan dan tahun
    $stmt = $pdo->prepare("SELECT * FROM barang_keluar WHERE bulan_keluar = ? AND tahun_keluar = ?");
    $stmt->execute([$bulan, $tahun]);
    $data = $stmt->fetchAll();
    
    // Proses ekspor ke file CSV atau Excel
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="barang_keluar.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID Barang', 'Nama Teknisi', 'Keterangan', 'Mac Address', 'Tanggal Keluar'));
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ekspor Barang Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Ekspor Barang Keluar</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="bulan">Bulan</label>
            <input type="number" class="form-control" id="bulan" name="bulan" placeholder="Masukkan Bulan (1-12)" required>
        </div>
        <div class="form-group">
            <label for="tahun">Tahun</label>
            <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Masukkan Tahun" required>
        </div>
        <button type="submit" name="export" class="btn btn-primary">Ekspor Data</button>
    </form>
</div>
</body>
</html>
