<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi pesan error atau sukses
$error_message = '';
$success_message = '';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $nama_teknisi = $_POST['nama_teknisi'];
    $keterangan = $_POST['keterangan'];

    // Query untuk mencari ID Barang di database
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    $barang = $stmt->fetch();

    if ($barang) {
        // Validasi jika ID Barang ada, maka tambahkan ke daftar barang keluar
        $mac_address = $barang['mac_address'];

        // Insert data barang keluar ke dalam tabel barang_keluar
        $stmt = $pdo->prepare("INSERT INTO barang_keluar (id_barang, nama_teknisi, keterangan, mac_address, tanggal_keluar) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$id_barang, $nama_teknisi, $keterangan, $mac_address]);

        // Hapus barang dari qc_lolos setelah dikeluarkan
        $stmt = $pdo->prepare("DELETE FROM items WHERE id_barang = ?");
        $stmt->execute([$id_barang]);

        $success_message = "Barang berhasil dikeluarkan!";
    } else {
        $error_message = "ID Barang tidak ditemukan di QC Lolos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Keluarkan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Form Keluarkan Barang</h2>
        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>
        <form method="post">
            <div class="form-group">
                <label for="id_barang">ID Barang:</label>
                <input type="text" name="id_barang" id="id_barang" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nama_teknisi">Nama Teknisi:</label>
                <input type="text" name="nama_teknisi" id="nama_teknisi" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan:</label>
                <input type="text" name="keterangan" id="keterangan" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Keluarkan Barang</button>
        </form>
    </div>
</body>
</html>
