<?php
require '../belanja/db.php'; // Sesuaikan dengan path ke file koneksi database Anda

// Variabel untuk menyimpan pesan
$message = "";

// Proses pencarian data barang berdasarkan kode unik
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kode_unik'])) {
    $kode_unik = $_POST['kode_unik'];
    
    // Query untuk mengambil data permintaan berdasarkan kode unik
    $query = "
        SELECT p.id, p.nama_peminta, p.tipe_barang, p.jumlah, p.keterangan_penggunaan, p.tanggal_permintaan, s.status
        FROM permintaan_barang p
        LEFT JOIN status_permintaan s ON p.id = s.id_permintaan
        WHERE p.kode_unik = :kode_unik";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['kode_unik' => $kode_unik]);
    $permintaan = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Permintaan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Konfirmasi Permintaan Barang</h2>
        
        <!-- Form untuk mencari permintaan berdasarkan kode unik -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="kode_unik">Masukkan Kode Unik:</label>
                <input type="text" id="kode_unik" name="kode_unik" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cari Permintaan</button>
        </form>
        
        <?php if (isset($permintaan) && $permintaan): ?>
            <div class="mt-4">
                <h4>Detail Permintaan</h4>
                <p><strong>Nama Peminta:</strong> <?php echo $permintaan['nama_peminta']; ?></p>
                <p><strong>Tipe Barang:</strong> <?php echo $permintaan['tipe_barang']; ?></p>
                <p><strong>Jumlah:</strong> <?php echo $permintaan['jumlah']; ?></p>
                <p><strong>Keterangan Penggunaan:</strong> <?php echo $permintaan['keterangan_penggunaan']; ?></p>
                <p><strong>Tanggal Permintaan:</strong> <?php echo $permintaan['tanggal_permintaan']; ?></p>
                <p><strong>Status:</strong> <?php echo $permintaan['status'] ?: 'Menunggu'; ?></p>
                
                <?php if ($permintaan['status'] == 'Disetujui'): ?>
                    <!-- Tombol Konfirmasi dan Batalkan -->
                    <form method="POST" action="proses_konfirmasi_permintaan.php">
                        <input type="hidden" name="id_permintaan" value="<?php echo $permintaan['id']; ?>">
                        <button type="submit" name="action" value="confirm" class="btn btn-success">Konfirmasi</button>
                        <button type="submit" name="action" value="cancel" class="btn btn-danger">Batalkan</button>
                    </form>
                <?php else: ?>
                    <p><em>Permintaan ini belum disetujui atau sudah ditolak.</em></p>
                <?php endif; ?>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p class="text-danger">Permintaan dengan kode unik tersebut tidak ditemukan.</p>
        <?php endif; ?>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
