<?php
require '../belanja/db.php'; // Koneksi database

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kembalikan_barang'])) {
    $id_barang = $_POST['id_barang'];
    $alasan = $_POST['alasan'];
    $tanggal_keputusan = $_POST['tanggal_keputusan'];
    
    // Ambil data barang dari daftar barang keluar
    $stmt = $pdo->prepare("SELECT * FROM barang_keluar WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    $barang = $stmt->fetch();

    if ($barang) {
        // Masukkan barang kembali ke stok atau QC Lolos
        $stmt_insert = $pdo->prepare("INSERT INTO qc_lolos (id_barang, keterangan, tanggal_keputusan, alasan) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([$barang['id_barang'], $barang['keterangan'], $tanggal_keputusan, $alasan]);

        // Hapus dari daftar barang keluar
        $stmt_delete = $pdo->prepare("DELETE FROM barang_keluar WHERE id_barang = ?");
        $stmt_delete->execute([$id_barang]);

        echo "Barang berhasil dikembalikan ke QC Lolos atau stok!";
    } else {
        echo "Barang tidak ditemukan dalam daftar barang keluar!";
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
        <h2 class="mb-4">Barang Tidak Jadi Keluar</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_barang">ID Barang</label>
                <input type="text" name="id_barang" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="alasan">Alasan Tidak Jadi Keluar</label>
                <input type="text" name="alasan" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tanggal_keputusan">Tanggal Keputusan</label>
                <input type="date" name="tanggal_keputusan" class="form-control" required>
            </div>
            <button type="submit" name="kembalikan_barang" class="btn btn-primary">Kembalikan Barang</button>
        </form>
    </div>
</body>
</html>
