<?php
require '../belanja/db.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = $_POST['id_barang'] ?? '';

    if ($id_barang) {
        // Query untuk menghapus barang dari database
        $stmt = $pdo->prepare("DELETE FROM items WHERE id_barang = ?");
        $stmt->execute([$id_barang]);

        if ($stmt->rowCount()) {
            // Jika berhasil dihapus, kembali ke halaman sebelumnya
            header("Location: qc_retur.php?success=1");
            exit();
        } else {
            // Jika gagal menghapus
            header("Location: qc_retur.php?error=1");
            exit();
        }
    }
}

// Jika akses langsung tanpa POST
header("Location: qc_retur.php");
exit();
?>
