<?php
require '../belanja/db.php'; // Koneksi ke database

// Inisialisasi variabel filter
$search = "";
$tanggal_mulai = "";
$tanggal_akhir = "";

// Cek apakah ada input pencarian dari form
if (isset($_GET['search']) || isset($_GET['tanggal_mulai']) || isset($_GET['tanggal_akhir'])) {
    $search = $_GET['search'] ?? '';
    $tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
    $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

    // Query untuk mencari barang berdasarkan ID, Nama, Tipe, dan rentang tanggal
    $query = "SELECT * FROM items WHERE qc_status = 'Retur' AND 
              (id_barang LIKE ? OR nama_barang LIKE ? OR tipe_barang LIKE ?)";

    if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
        $query .= " AND tanggal_order BETWEEN ? AND ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', $tanggal_mulai, $tanggal_akhir]);
    } else {
        $stmt = $pdo->prepare($query);
        $stmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
    }
} else {
    // Query default tanpa pencarian
    $stmt = $pdo->query("SELECT * FROM items WHERE qc_status = 'Retur'");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Barang Retur</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .sidebar {
            height: 100%; /* Tinggi penuh, sesuaikan ini jika Anda ingin tinggi "auto" */
            width: 160px; /* Lebar sidebar */
            position: fixed; /* Sidebar tetap (tetap pada tempatnya saat di-scroll) */
            z-index: 1; /* Tetap di atas */
            top: 0; /* Tetap di atas */
            left: 0;
            background-color: #111; /* Warna hitam */
            overflow-x: hidden; /* Nonaktifkan scroll horizontal */
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #818181;
            display: block;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
        .main {
            margin-left: 160px; /* Sama dengan lebar sidebar */
            padding: 0px 10px;
        }
        input[type="text"], input[type="date"], textarea {
            width: 150px;
            padding: 5px;
        }
        textarea {
            height: 80px;
        }
        .table td, .table th {
            vertical-align: middle;
        }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
<div class="sidebar">
    <a href="/quality_control/qc_dashboard.php">BACK DHASBOARD QC</a>
    <a href="/quality_control/qc_lolos.php">Dashboard QC Lolos</a>
    <a href="/logout.php">Logout</a>
</div>

<div class="main">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="text-center">Daftar Barang Reject</h2>
            <!-- Tombol Kembali ke Dashboard -->
            <a href="/index.php" class="btn btn-secondary">Kembali</a>
        </div>

        <!-- Form Pencarian dan Filter Tanggal -->
        <form class="mb-4" method="GET" action="qc_retur.php">
            <div class="input-group mb-3">
                <input type="text" name="search" class="form-control" placeholder="Cari ID, Nama, atau Tipe Barang" value="<?php echo htmlspecialchars($search); ?>">
                <input type="date" name="tanggal_mulai" class="form-control ml-2" placeholder="Tanggal Mulai" value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
                <input type="date" name="tanggal_akhir" class="form-control ml-2" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($tanggal_akhir); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </div>
        </form>

        <!-- Tombol Export Berdasarkan Bulan dan Tahun -->
        <form method="GET" action="export_qc_retur.php">
            <div class="input-group mb-3">
                <select name="bulan" class="form-control">
                    <option value="">Pilih Bulan</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
                <select name="tahun" class="form-control ml-2">
                    <option value="">Pilih Tahun</option>
                    <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= 2000; $year--) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Export Data</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Nama Toko</th>
                    <th>Ekspedisi</th>
                    <th>Belanja Via</th>
                    <th>Petugas Order</th>
                    <th>Tanggal Order</th>
                    <th>Tanggal Datang</th>
                    <th>Petugas QC</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipe_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['stok']); ?></td>
                        <td><?php echo htmlspecialchars($row['satuan_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
                        <td><?php echo htmlspecialchars($row['ekspedisi']); ?></td>
                        <td><?php echo htmlspecialchars($row['belanja_via']); ?></td>
                        <td><?php echo htmlspecialchars($row['siapa_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_datang']); ?></td>
                        <td><?php echo htmlspecialchars($row['petugas_qc'] ?? 'Belum diisi'); ?></td>
                        <td><?php echo htmlspecialchars($row['keterangan_qc'] ?? 'Belum diisi'); ?></td>
                        <td>
                            <form action="update_qc_retur.php" method="POST">
                                <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                                <input type="text" name="petugas_qc" placeholder="Isi Petugas QC" class="form-control mb-2">
                                <textarea name="keterangan_qc" placeholder="Isi Keterangan" class="form-control mb-2"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            </form>
                            <!-- Tombol Hapus -->
                            <form action="hapus_qc_retur.php" method="POST" class="mt-2">
                                <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>

