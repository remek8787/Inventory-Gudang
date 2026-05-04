<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel
$id_barang = '';
$nama_teknisi = '';
$keterangan = '';
$penggunaan = '';
$petugas_admin = ''; // Inisialisasi petugas_admin

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pilih id_barang yang mana yang digunakan (dropdown atau manual)
    if (!empty($_POST['id_barang_manual'])) {
        $id_barang = $_POST['id_barang_manual'];
    } else {
        $id_barang = $_POST['id_barang'];
    }

    $nama_teknisi = $_POST['nama_teknisi'];
    $keterangan = $_POST['keterangan'];
    $penggunaan = $_POST['penggunaan'];

    // Pastikan petugas_admin sudah diisi dari form
    $petugas_admin = isset($_POST['petugas_admin']) ? $_POST['petugas_admin'] : ''; 

    // Cek apakah barang sudah pernah keluar
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM barang_keluar WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Jika barang belum pernah keluar, lakukan proses insert
        $stmt = $pdo->prepare("INSERT INTO barang_keluar (id_barang, nama_teknisi, keterangan, penggunaan, petugas_admin, tanggal_keluar) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id_barang, $nama_teknisi, $keterangan, $penggunaan, $petugas_admin]); // Sertakan petugas_admin

        // Update stok barang
        $stmt_update = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok - 1 WHERE id_barang = ?");
        $stmt_update->execute([$id_barang]);

        echo "Barang berhasil dikeluarkan!";
    } else {
        echo "Barang ini sudah pernah keluar sebelumnya.";
    }
}

// Query untuk mengambil semua data barang yang tersedia
$stmt = $pdo->query("SELECT * FROM barang_masuk_gudang");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Barang Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Form Barang Keluar</h2>

        <!-- Form Barang Keluar -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_barang">ID Barang</label>
                <select class="form-control" name="id_barang">
                    <option value="">Pilih Barang</option>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <option value="<?php echo $row['id_barang']; ?>">
                            <?php echo $row['id_barang'] . ' - ' . $row['nama_barang']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_barang_manual">Atau Masukkan ID Barang Secara Manual</label>
                <input type="text" class="form-control" name="id_barang_manual" placeholder="Masukkan ID Barang secara manual">
            </div>

            <div class="form-group">
                <label for="nama_teknisi">Nama Teknisi</label>
                <input type="text" class="form-control" name="nama_teknisi" placeholder="Masukkan Nama Teknisi" required>
            </div>

            <div class="form-group">
                <label for="petugas_admin">Petugas Admin</label>
                <input type="text" class="form-control" name="petugas_admin" placeholder="Masukkan Nama Petugas Admin" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" name="keterangan" placeholder="Keterangan tambahan"></textarea>
            </div>

            <div class="form-group">
                <label for="penggunaan">Penggunaan</label>
                <input type="text" class="form-control" name="penggunaan" placeholder="Penggunaan barang" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
</html>
