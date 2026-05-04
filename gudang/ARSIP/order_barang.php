<?php
// Koneksi ke database
require '../belanja/db.php'; // Pastikan path benar

// Buat request_id unik (misalnya menggunakan fungsi uniqid)
$request_id = uniqid('REQ-');

// Query untuk mengambil semua tipe barang
$query_tipe = "SELECT DISTINCT tipe_barang FROM barang_masuk_gudang";
$result_tipe = $pdo->query($query_tipe);

// Cek apakah tipe barang sudah dipilih
$selected_tipe = isset($_GET['tipe_barang']) ? $_GET['tipe_barang'] : '';

// Cek apakah ada status sukses setelah redirect
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<p>Order berhasil dibuat dengan Request ID: " . htmlspecialchars($_GET['request_id']) . ", menunggu konfirmasi admin.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Order Barang</title>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>

<h2>Form Order Barang</h2>

<!-- Form untuk memilih tipe barang -->
<form method="GET" action="">
    <label for="tipe_barang">Pilih Tipe Barang:</label>
    <select name="tipe_barang" id="tipe_barang" onchange="this.form.submit()">
        <option value="">Pilih Tipe Barang</option>
        <?php while ($row_tipe = $result_tipe->fetch()) { ?>
            <option value="<?php echo htmlspecialchars($row_tipe['tipe_barang']); ?>" <?php echo ($selected_tipe == $row_tipe['tipe_barang']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($row_tipe['tipe_barang']); ?>
            </option>
        <?php } ?>
    </select>
</form>

<!-- Jika ada tipe yang dipilih, tampilkan form order -->
<?php if (!empty($selected_tipe)) { ?>
    <h3>Tipe Barang yang Dipilih: <?php echo htmlspecialchars($selected_tipe); ?></h3>

    <form method="POST" action="submit_order.php">
        <label for="jumlah_order">Jumlah Order:</label>
        <input type="number" name="jumlah_order" id="jumlah_order" required>

        <br><br>

        <label for="nama_teknisi">Nama Teknisi:</label>
        <input type="text" name="nama_teknisi" id="nama_teknisi" required>

        <br><br>

        <label for="penggunaan">Penggunaan:</label>
        <input type="text" name="penggunaan" id="penggunaan" required>

        <br><br>

        <label for="keterangan">Keterangan:</label>
        <textarea name="keterangan" id="keterangan" rows="4" cols="50" required></textarea>

        <br><br>

        <button type="submit" name="submit_order">Order</button>
    </form>
<?php } ?>

<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
