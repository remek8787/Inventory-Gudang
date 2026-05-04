<?php
require '../belanja/db.php'; // Pastikan koneksi database sudah benar

// Cek jika ada parameter delete
if (isset($_GET['delete'])) {
    $id_barang = $_GET['delete'];

    // Ambil data barang yang akan dipindahkan ke barang_tidak_jadi_keluar
    $stmt = $pdo->prepare("SELECT id_barang, mac_address, keterangan FROM barang_keluar WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    $barang = $stmt->fetch();

    // Cek apakah data barang ditemukan
    if ($barang) {
        // Pindahkan barang ke tabel barang_tidak_jadi_keluar
        $stmt_insert = $pdo->prepare("INSERT INTO barang_tidak_jadi_keluar (id_barang, mac_address, keterangan) VALUES (?, ?, ?)");
        $stmt_insert->execute([$barang['id_barang'], $barang['mac_address'], $barang['keterangan']]);

        // Hapus barang dari tabel barang_keluar
        $stmt_delete = $pdo->prepare("DELETE FROM barang_keluar WHERE id_barang = ?");
        $stmt_delete->execute([$id_barang]);

        // Redirect ke halaman list_barang_keluar.php dengan pesan sukses
        header("Location: list_barang_keluar.php?status=success&msg=Barang berhasil dipindahkan ke form Barang Tidak Jadi Keluar.");
        exit();
    } else {
        // Jika barang tidak ditemukan
        header("Location: list_barang_keluar.php?status=error&msg=Barang tidak ditemukan.");
        exit();
    }
}

// Cek apakah ada filter berdasarkan bulan dan tahun
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Query untuk mengambil barang keluar
if (!empty($bulan) && !empty($tahun)) {
    $stmt = $pdo->prepare("SELECT * FROM barang_keluar WHERE bulan_keluar = ? AND tahun_keluar = ?");
    $stmt->execute([$bulan, $tahun]);
} else {
    $stmt = $pdo->query("SELECT * FROM barang_keluar");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Barang Keluar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Daftar Barang Keluar</h2>
        <form class="form-inline mb-3" method="get" action="">
            <input type="number" name="bulan" class="form-control mr-2" placeholder="Bulan (1-12)" value="<?php echo htmlspecialchars($bulan); ?>">
            <input type="number" name="tahun" class="form-control mr-2" placeholder="Tahun" value="<?php echo htmlspecialchars($tahun); ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Teknisi</th>
                        <th>Keterangan</th>
                        <th>Mac Address</th>
                        <th>Tanggal Keluar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr>
                            <td><?php echo $row['id_barang']; ?></td>
                            <td><?php echo $row['nama_teknisi']; ?></td>
                            <td><?php echo $row['keterangan']; ?></td>
                            <td><?php echo $row['mac_address']; ?></td>
                            <td><?php echo $row['tanggal_keluar']; ?></td>
                            <td>
                                <!-- Tombol untuk menghapus dan memindahkan barang ke barang_tidak_jadi_keluar -->
                                <a href="?delete=<?php echo $row['id_barang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin memindahkan barang ini ke form Barang Tidak Jadi Keluar?');">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
