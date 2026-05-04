<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel
$id_barang = '';
$nama_teknisi = '';
$keterangan = '';
$penggunaan = '';
$petugas_admin = '';

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = !empty($_POST['id_barang_manual']) ? $_POST['id_barang_manual'] : $_POST['id_barang'];
    $nama_teknisi = $_POST['nama_teknisi'];
    $keterangan = $_POST['keterangan'];
    $penggunaan = $_POST['penggunaan'];
    $petugas_admin = $_POST['petugas_admin'];

    if (empty($id_barang) || empty($nama_teknisi) || empty($petugas_admin) || empty($penggunaan)) {
        die("Semua field wajib diisi!");
    }

    $stmt_check_stok = $pdo->prepare("SELECT stok FROM barang_masuk_gudang WHERE id_barang = ?");
    $stmt_check_stok->execute([$id_barang]);
    $stok = $stmt_check_stok->fetchColumn();

    if ($stok <= 0) {
        die("Stok barang tidak mencukupi untuk dikeluarkan!");
    }

    $stmt = $pdo->prepare("
        INSERT INTO barang_keluar (id_barang, nama_teknisi, keterangan, penggunaan, petugas_admin, tanggal_keluar) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$id_barang, $nama_teknisi, $keterangan, $penggunaan, $petugas_admin]);

    $stmt_update = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok - 1 WHERE id_barang = ?");
    $stmt_update->execute([$id_barang]);

    echo "Barang berhasil dikeluarkan!";
}

$stmt = $pdo->query("SELECT * FROM barang_masuk_gudang");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Barang Keluar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8">
        <div class="flex">
            <!-- Sidebar -->
            <div class="w-1/6 bg-white p-4 shadow-md rounded">
                <h3 class="mb-4 text-lg font-semibold">Navigation</h3>
                <ul class="space-y-2">
                <li>
                <a href="/GUDANGV1/gudang/barang_masuk_gudang.php" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </li>
            <li>
                <a href="/GUDANGV1/gudang/riwayat_pengeluaran.php" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-history"></i> Riwayat Pengeluaran Barang
                </a>
            </li>
            <!-- Tambahkan link baru di sini -->
            <li>
                <a href="/GUDANGV1/gudang/stok_gudang.php" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-boxes"></i> Cek Stok Gudang
                </a>
            </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="flex-1 ml-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Form Barang Keluar</h2>
                </div>

                <!-- Form Barang Keluar -->
                <form method="POST" action="" class="bg-white p-6 rounded shadow-md">
                    <div class="mb-4">
                        <label for="id_barang" class="block text-gray-700">ID Barang</label>
                        <select class="form-control w-full px-4 py-2 border rounded" name="id_barang">
                            <option value="">Pilih Barang</option>
                            <?php while ($row = $stmt->fetch()) { ?>
                                <option value="<?php echo $row['id_barang']; ?>">
                                    <?php echo $row['id_barang'] . ' - ' . $row['nama_barang']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="id_barang_manual" class="block text-gray-700">Atau Masukkan ID Barang Secara Manual</label>
                        <input type="text" class="form-control w-full px-4 py-2 border rounded" name="id_barang_manual" placeholder="Masukkan ID Barang secara manual">
                    </div>

                    <div class="mb-4">
                        <label for="nama_teknisi" class="block text-gray-700">Nama Teknisi</label>
                        <input type="text" class="form-control w-full px-4 py-2 border rounded" name="nama_teknisi" placeholder="Masukkan Nama Teknisi" required>
                    </div>

                    <div class="mb-4">
                        <label for="petugas_admin" class="block text-gray-700">Petugas Admin</label>
                        <input type="text" class="form-control w-full px-4 py-2 border rounded" name="petugas_admin" placeholder="Masukkan Nama Petugas Admin" required>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="block text-gray-700">Keterangan</label>
                        <textarea class="form-control w-full px-4 py-2 border rounded" name="keterangan" placeholder="Keterangan tambahan"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="penggunaan" class="block text-gray-700">Penggunaan</label>
                        <input type="text" class="form-control w-full px-4 py-2 border rounded" name="penggunaan" placeholder="Penggunaan barang" required>
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                </form>
            </div>
        </div>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
