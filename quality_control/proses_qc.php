<?php
require '../belanja/db.php'; // Koneksi ke database

// Cek apakah ada ID barang dan aksi yang dikirim
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id_barang = $_GET['id'];
    $action = $_GET['action'];

    // Tentukan status QC berdasarkan aksi
    if ($action == 'accept') {
        $qc_status = 'Lolos QC'; // Ubah menjadi 'Lolos QC'
    } elseif ($action == 'reject') {
        $qc_status = 'Retur'; // Ubah menjadi 'Retur'
    }

    // Update status QC barang
    $stmt = $pdo->prepare("UPDATE items SET qc_status = ? WHERE id_barang = ?");
    $stmt->execute([$qc_status, $id_barang]);

    echo "Status QC berhasil diperbarui menjadi '$qc_status'.";
}
?>

