<?php
require '../belanja/db.php'; // Koneksi ke database

// Cek apakah ada permintaan untuk menghapus semua data
if (isset($_POST['reset_all'])) {
    // Ambil semua ID Barang dari tabel pengeluaran
    $stmt_get_all = $pdo->query("SELECT id_barang FROM barang_keluar");
    $barang_keluar = $stmt_get_all->fetchAll(PDO::FETCH_ASSOC);

    // Kembalikan stok barang ke tabel barang_masuk_gudang
    foreach ($barang_keluar as $barang) {
        $id_barang = $barang['id_barang'];
        $stmt_update = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok + 1 WHERE id_barang = ?");
        $stmt_update->execute([$id_barang]);
    }

    // Hapus semua data dari tabel barang_keluar
    $stmt_delete_all = $pdo->query("DELETE FROM barang_keluar");

    echo "<script>alert('Semua data pengeluaran berhasil dihapus dan stok barang telah dikembalikan!'); window.location.href='reset_delete_stok.php';</script>";
}

// Cek apakah ada permintaan untuk menghapus data tertentu
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Ambil informasi ID Barang dari tabel pengeluaran
    $stmt_get = $pdo->prepare("SELECT id_barang FROM barang_keluar WHERE id = ?");
    $stmt_get->execute([$delete_id]);
    $id_barang = $stmt_get->fetchColumn();

    if ($id_barang) {
        // Kembalikan stok barang ke tabel barang_masuk_gudang
        $stmt_update = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok + 1 WHERE id_barang = ?");
        $stmt_update->execute([$id_barang]);

        // Hapus data dari tabel barang_keluar
        $stmt_delete = $pdo->prepare("DELETE FROM barang_keluar WHERE id = ?");
        $stmt_delete->execute([$delete_id]);

        echo "<script>alert('Data pengeluaran berhasil dihapus dan stok barang telah dikembalikan!'); window.location.href='reset_delete_stok.php';</script>";
    } else {
        echo "<script>alert('Gagal mengembalikan stok, ID Barang tidak ditemukan.'); window.location.href='reset_delete_stok.php';</script>";
    }
}

// Query untuk mendapatkan semua data pengeluaran barang
$stmt = $pdo->query("SELECT * FROM barang_keluar ORDER BY tanggal_keluar DESC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Pengeluaran Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Reset Pengeluaran Barang</h1>
        
        <!-- Tombol Reset Semua -->
        <div class="mb-3 text-end">
            <form method="POST" action="">
                <button type="submit" name="reset_all" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus semua data pengeluaran dan mengembalikan stok?')">Hapus Semua Data Pengeluaran</button>
            </form>
        </div>

        <!-- Tabel Data Pengeluaran Barang -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Barang</th>
                    <th>Nama Teknisi</th>
                    <th>Keterangan</th>
                    <th>Penggunaan</th>
                    <th>Petugas Admin</th>
                    <th>Tanggal Keluar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['id_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_teknisi']); ?></td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            <td><?php echo htmlspecialchars($row['penggunaan']); ?></td>
                            <td><?php echo htmlspecialchars($row['petugas_admin']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_keluar']); ?></td>
                            <td>
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini dan mengembalikan stok?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data pengeluaran barang.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
