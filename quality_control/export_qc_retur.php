<?php
require '../belanja/db.php'; // Koneksi ke database

if (isset($_GET['bulan']) && isset($_GET['tahun'])) {
    $bulan = $_GET['bulan'];
    $tahun = $_GET['tahun'];

    // Query untuk mendapatkan data sesuai bulan dan tahun
    $stmt = $pdo->prepare("SELECT * FROM items WHERE qc_status = 'Retur' AND MONTH(tanggal_order) = ? AND YEAR(tanggal_order) = ?");
    $stmt->execute([$bulan, $tahun]);

    // Membuat file CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="export_retur_' . $bulan . '_' . $tahun . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID Barang', 'Nama Barang', 'Tipe', 'Stok', 'Satuan', 'Nama Toko', 'Ekspedisi', 'Belanja Via', 'Petugas', 'Tanggal Order', 'Tanggal Datang', 'Petugas QC', 'Keterangan'));
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
} else {
    header("Location: qc_retur.php?status=error&msg=Data tidak valid untuk export");
    exit();
}
?>
