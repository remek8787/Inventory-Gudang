<?php
// Koneksi ke database
require '../belanja/db.php';

// Query untuk mendapatkan tipe barang dan total stok dari tabel barang_masuk_gudang
$query = "
    SELECT tipe_barang, SUM(stok) as total_stok
    FROM barang_masuk_gudang
    GROUP BY tipe_barang
    ORDER BY tipe_barang";
$stmt = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Permintaan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Form Permintaan Barang</h2>
        <form action="proses_permintaan_barang.php" method="POST">
            <div class="form-group">
                <label for="nama_peminta">Nama Peminta:</label>
                <input type="text" id="nama_peminta" name="nama_peminta" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="tipe_barang">Tipe Barang:</label>
                <select id="tipe_barang" name="tipe_barang" class="form-control" required>
                    <option value="">Pilih Tipe Barang</option>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <option value="<?php echo $row['tipe_barang']; ?>">
                            <?php echo $row['tipe_barang']; ?> (Stok: <?php echo $row['total_stok']; ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="keterangan_penggunaan">Keterangan Penggunaan:</label>
                <textarea id="keterangan_penggunaan" name="keterangan_penggunaan" class="form-control" rows="4" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Ajukan Permintaan</button>
        </form>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
