<?php
require '../belanja/db.php';

// Query untuk mengambil barang dengan status "Menunggu QC" dan pencarian
$search_query = $_GET['search_query'] ?? '';
$query = "SELECT * FROM items WHERE qc_status = 'Menunggu QC'";

if ($search_query) {
    $query .= " AND (id_barang LIKE '%$search_query%' 
                OR nama_barang LIKE '%$search_query%' 
                OR tipe_barang LIKE '%$search_query%' 
                OR nama_toko LIKE '%$search_query%')";
}

$stmt = $pdo->query($query);
?>

<html>
<head>
    <title>Dashboard QC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">

    <!-- Side Panel -->
    <div class="w-1/5 bg-gray-800 text-white h-screen p-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Dashboard</h2>
        <ul>
            <li class="mb-4">
                <a href="/GUDANGV1/index.php" class="flex items-center hover:bg-gray-600 p-2 rounded">
                    <i class="fas fa-home mr-3"></i> BERANDA
                </a>
            </li>
            <li class="mb-4">
                <a href="qc_dashboard.php" class="flex items-center hover:bg-gray-600 p-2 rounded">
                    <i class="fas fa-clipboard-list mr-3"></i> Barang Menunggu QC
                </a>
            </li>
          <li class="mb-4">
                <a href="qc_lolos.php" class="flex items-center hover:bg-gray-600 p-2 rounded">
                    <i class="fas fa-check-circle mr-3"></i> QC Lolos
                </a>
            </li>
            <li class="mb-4">
                <a href="qc_retur.php" class="flex items-center hover:bg-gray-600 p-2 rounded">
                    <i class="fas fa-times-circle mr-3"></i> Barang Reject
                </a>
            </li>
            <li class="mb-4">
                <a href="/GUDANGV1/quality_control/list_selesai_qc.php" class="flex items-center hover:bg-gray-600 p-2 rounded">
                    <i class="fas fa-box mr-3"></i> Barang Siap Ke Gudang
                </a>
            </li> 
            <li class="mb-4">
                <a href="logout.php" class="flex items-center hover:bg-red-500 p-2 rounded">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="w-4/5 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Daftar Barang Menunggu QC</h2>
            <a href="/GUDANGV1/index.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kembali</a>
        </div>
        
        <!-- Search Form -->
        <div class="mb-6">
            <form method="GET" action="" id="search-form" class="flex gap-4">
                <input 
                    type="text" 
                    name="search_query" 
                    id="search_query" 
                    placeholder="Cari Barang (ID, Nama, Toko, dll)" 
                    value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>" 
                    class="px-4 py-2 border rounded w-full"
                    autofocus
                >
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Cari</button>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-2 px-4 border-b">ID Barang</th>
                        <th class="py-2 px-4 border-b">Nama Barang</th>
                        <th class="py-2 px-4 border-b">Type</th>
                        <th class="py-2 px-4 border-b">Stok</th>
                        <th class="py-2 px-4 border-b">Satuan</th>
                        <th class="py-2 px-4 border-b">Nama Toko</th>
                        <th class="py-2 px-4 border-b">Ekspedisi</th>
                        <th class="py-2 px-4 border-b">Belanja Via</th>
                        <th class="py-2 px-4 border-b">Petugas Order</th>
                        <th class="py-2 px-4 border-b">Tanggal Order</th>
                        <th class="py-2 px-4 border-b">Tanggal Datang</th>
                        <th class="py-2 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr class="bg-gray-100">
                            <td class="py-2 px-4 border-b"><?php echo $row['id_barang']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['nama_barang']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['tipe_barang']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['stok']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['satuan_barang']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['nama_toko']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['ekspedisi']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['belanja_via']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['siapa_order']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['tanggal_order']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $row['tanggal_datang']; ?></td>
                            <td class="py-2 px-4 border-b">
                                <button onclick="updateQC('<?php echo $row['id_barang']; ?>', 'accept')" 
                                        class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Accept</button>
                                <button onclick="updateQC('<?php echo $row['id_barang']; ?>', 'reject')" 
                                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Reject</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    function updateQC(id, action) {
        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${action === 'accept' ? 'menerima' : 'menolak'} barang ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`proses_qc.php?id=${id}&action=${action}`)
                    .then(response => response.text())
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Status QC berhasil diperbarui menjadi ${action === 'accept' ? 'Lolos QC' : 'Tidak Lolos QC'}.`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => { location.reload(); }, 2000);
                    })
                    .catch(() => Swal.fire('Error', 'Terjadi kesalahan. Coba lagi!', 'error'));
            }
        });
    }
    </script>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
