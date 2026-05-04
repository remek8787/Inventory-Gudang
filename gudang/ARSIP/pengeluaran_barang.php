<?php
// Koneksi ke database
require '../belanja/db.php'; // Pastikan path koneksi benar

// Cek apakah data diterima dari approval_order.php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Ambil data order berdasarkan request_id
    $stmt = $pdo->prepare("SELECT * FROM order_barang WHERE request_id = ? AND status = 'Disetujui'");
    $stmt->execute([$request_id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo "Data order tidak ditemukan atau belum disetujui.";
        exit;
    }
} else {
    echo "Akses tidak valid.";
    exit;
}

// Proses pengeluaran barang ketika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah_keluar = $_POST['jumlah_keluar'];
    $nama_teknisi = $_POST['nama_teknisi'];
    $nama_admin = $_POST['nama_admin'];
    $keterangan = $_POST['keterangan'];

    // Masukkan data pengeluaran ke tabel pengeluaran_barang
    $stmt = $pdo->prepare("INSERT INTO pengeluaran_barang (request_id, id_barang, nama_barang, jumlah_keluar, nama_teknisi, nama_admin, tanggal_keluar, keterangan) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([$request_id, $id_barang, $nama_barang, $jumlah_keluar, $nama_teknisi, $nama_admin, $keterangan]);

    // Kurangi stok di tabel barang_masuk_gudang
    $stmt_update_stock = $pdo->prepare("UPDATE barang_masuk_gudang SET jumlah_stok = jumlah_stok - ? WHERE id_barang = ?");
    $stmt_update_stock->execute([$jumlah_keluar, $id_barang]);

    echo "Barang berhasil dikeluarkan dari gudang!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengeluaran Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Form Pengeluaran Barang</h2>

        <form method="POST" action="pengeluaran_barang.php">
            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($order['request_id']); ?>">
            <input type="hidden" name="id_barang" value="<?php echo htmlspecialchars($order['id_barang']); ?>">

            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" class="form-control" name="nama_barang" id="nama_barang" value="<?php echo htmlspecialchars($order['nama_barang']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="jumlah_keluar">Jumlah Keluar</label>
                <input type="number" class="form-control" name="jumlah_keluar" id="jumlah_keluar" value="<?php echo htmlspecialchars($order['jumlah_order']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="nama_teknisi">Nama Teknisi</label>
                <input type="text" class="form-control" name="nama_teknisi" id="nama_teknisi" value="<?php echo htmlspecialchars($order['nama_teknisi']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="nama_admin">Nama Admin</label>
                <input type="text" class="form-control" name="nama_admin" id="nama_admin" value="<?php echo htmlspecialchars($order['admin_approval']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" name="keterangan" id="keterangan" rows="3"><?php echo htmlspecialchars($order['keterangan']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Konfirmasi Pengeluaran</button>
        </form>
    </div>
</body>
</html>
