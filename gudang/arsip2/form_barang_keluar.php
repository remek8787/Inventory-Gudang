<?php
require '../belanja/db.php'; // Sesuaikan dengan path ke file koneksi database Anda

// Variabel untuk menyimpan pesan
$message = "";

// Proses pencarian data barang berdasarkan ID Barang
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_barang'])) {
    $id_barang = $_POST['id_barang'];
    
    // Query untuk mengambil data barang berdasarkan ID Barang
    $query = "SELECT * FROM barang_masuk_gudang WHERE id_barang = :id_barang";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_barang' => $id_barang]);
    $barang = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Barang Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Form Barang Keluar</h2>
        
        <!-- Form untuk mencari barang berdasarkan ID Barang -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_barang">ID Barang:</label>
                <input type="text" id="id_barang" name="id_barang" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cari Barang</button>
        </form>
        
        <?php if (isset($barang) && $barang): ?>
            <div class="mt-4">
                <h4>Detail Barang</h4>
                <p><strong>Nama Barang:</strong> <?php echo $barang['nama_barang']; ?></p>
                <p><strong>Tipe Barang:</strong> <?php echo $barang['tipe_barang']; ?></p>
                <p><strong>MAC Address:</strong> <?php echo $barang['mac_address']; ?></p>
                <p><strong>Stok Saat Ini:</strong> <?php echo $barang['stok']; ?></p>
                <p><strong>Satuan:</strong> <?php echo $barang['satuan']; ?></p>
                
                <!-- Form untuk konfirmasi barang keluar -->
                <form method="POST" action="proses_barang_keluar.php">
                    <input type="hidden" name="id_barang" value="<?php echo $barang['id_barang']; ?>">
                    <input type="hidden" name="nama_barang" value="<?php echo $barang['nama_barang']; ?>">
                    <input type="hidden" name="tipe_barang" value="<?php echo $barang['tipe_barang']; ?>">
                    <input type="hidden" name="mac_address" value="<?php echo $barang['mac_address']; ?>">
                    <input type="hidden" name="stok" value="<?php echo $barang['stok']; ?>">
                    <input type="hidden" name="satuan" value="<?php echo $barang['satuan']; ?>">
                    
                    <div class="form-group">
                        <label for="petugas_acc">Petugas ACC Barang Keluar:</label>
                        <input type="text" id="petugas_acc" name="petugas_acc" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Konfirmasi Barang Keluar</button>
                </form>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p class="text-danger">Barang dengan ID tersebut tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
