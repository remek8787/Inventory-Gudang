<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel
$id_barang = '';
$alasan = '';

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $alasan = $_POST['alasan'];

    // Mulai transaksi
    $pdo->beginTransaction();
    try {
        // Cek apakah barang sudah keluar di tabel barang_keluar
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM barang_keluar WHERE id_barang = ?");
        $stmt_check->execute([$id_barang]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            // Hapus dari barang_keluar
            $stmt_delete = $pdo->prepare("DELETE FROM barang_keluar WHERE id_barang = ?");
            $stmt_delete->execute([$id_barang]);

            // Tambahkan kembali stok di barang_masuk_gudang
            $stmt_update = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok + 1 WHERE id_barang = ?");
            $stmt_update->execute([$id_barang]);

            // Masukkan log ke tabel barang_tidak_jadi_keluar
            $stmt_log = $pdo->prepare("INSERT INTO barang_tidak_jadi_keluar (id_barang, alasan, tanggal_keputusan) VALUES (?, ?, NOW())");
            $stmt_log->execute([$id_barang, $alasan]);

            // Commit transaksi
            $pdo->commit();
            echo "Barang berhasil dikembalikan dan stok diperbarui!";
        } else {
            // Jika barang tidak ditemukan di tabel barang_keluar
            echo "Barang dengan ID tersebut tidak ditemukan dalam daftar barang keluar.";
        }
    } catch (Exception $e) {
        // Rollback jika ada error
        $pdo->rollBack();
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Barang Tidak Jadi Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Form Barang Tidak Jadi Keluar</h2>

        <!-- Form Barang Tidak Jadi Keluar -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_barang">Masukkan ID Barang Secara Manual</label>
                <input type="text" class="form-control" name="id_barang" placeholder="Masukkan ID Barang" required>
            </div>

            <div class="form-group">
                <label for="alasan">Alasan</label>
                <textarea class="form-control" name="alasan" placeholder="Masukkan alasan barang tidak jadi keluar" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Kembalikan Barang</button>
        </form>
    </div>
</body>
</html>
