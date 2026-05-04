<?php
require '../belanja/db.php'; // Koneksi ke database

// PAGINATION SETUP
$records_per_page = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $records_per_page;

// HANDLE SEARCH
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = $search ? "AND (id_barang LIKE '%$search%' OR nama_barang LIKE '%$search%')" : "";

// HANDLE UPDATE FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_barang = $_POST['id_barang'];
    $mac_address = $_POST['mac_address'];
    $petugas_qc = $_POST['petugas_qc'];
    $tanggal_qc = $_POST['tanggal_qc'];
    $keterangan = $_POST['keterangan'];

    $stmt = $pdo->prepare("UPDATE items SET mac_address = ?, petugas_qc = ?, tanggal_qc = ?, keterangan = ? WHERE id_barang = ?");
    $stmt->execute([$mac_address, $petugas_qc, $tanggal_qc, $keterangan, $id_barang]);
    echo "<script>alert('Data berhasil diperbarui!');</script>";
}

// HANDLE SELESAI QC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selesai_qc'])) {
    $id_barang = $_POST['id_barang'];
    $stmt = $pdo->prepare("UPDATE items SET qc_status = 'Selesai QC' WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    echo "<script>alert('Barang berhasil ditandai sebagai selesai QC!');</script>";
}

// HANDLE DELETE
if (isset($_GET['delete'])) {
    $id_barang = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM items WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    header("Location: qc_lolos.php");
    exit();
}

// QUERY DATA
$stmt = $pdo->prepare("SELECT * FROM items WHERE qc_status = 'Lolos QC' $search_query ORDER BY tanggal_order DESC LIMIT $start, $records_per_page");
$stmt->execute();

$total_stmt = $pdo->query("SELECT COUNT(*) FROM items WHERE qc_status = 'Lolos QC' $search_query");
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $records_per_page);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Lolos QC</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .side-panel {
            background-color: #343a40;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            width: 200px;
        }
        .side-panel a { color: white; text-decoration: none; display: block; padding: 10px; }
        .side-panel a:hover { background-color: #495057; border-radius: 5px; }
        .content { margin-left: 220px; padding: 20px; }
        .form-control-inline { display: inline-block; width: auto; }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <!-- SIDE PANEL -->
    <div class="side-panel">
        <h4>Menu</h4>
        <a href="/quality_control/qc_dashboard.php">Dashboard QC</a>
      <!--  <a href="/gudang/index_gudang.html">Dashboard Gudang</a>
        <a href="/gudang/barang_keluar.php">Barang Keluar</a>
        <a href="/gudang/riwayat_pengeluaran.php">Riwayat Pengeluaran</a>
        <a href="/gudang/stok_gudang.php">Stok Gudang</a> -->
        <a href="#">Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <h2 class="text-center mb-4">Daftar Barang Lolos QC</h2>
        <!-- SEARCH FORM -->
        <form class="form-inline mb-3" method="get">
            <input type="text" name="search" class="form-control mr-2" placeholder="Cari Barang" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <!-- TABLE DATA -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Mac Address</th>
                        <th>Petugas QC</th>
                        <th>Tanggal QC</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr>
                            <form method="post">
                                <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                                <td><?php echo $row['id_barang']; ?></td>
                                <td><?php echo $row['nama_barang']; ?></td>
                                <td><input type="text" name="mac_address" class="form-control form-control-inline" value="<?php echo $row['mac_address']; ?>"></td>
                                <td><input type="text" name="petugas_qc" class="form-control form-control-inline" value="<?php echo $row['petugas_qc']; ?>"></td>
                                <td><input type="date" name="tanggal_qc" class="form-control form-control-inline" value="<?php echo $row['tanggal_qc']; ?>"></td>
                                <td><input type="text" name="keterangan" class="form-control form-control-inline" value="<?php echo $row['keterangan']; ?>"></td>
                                <td>
                                    <button type="submit" name="update" class="btn btn-success btn-sm">Update</button>
                                    <button type="submit" name="selesai_qc" class="btn btn-primary btn-sm mt-1">Selesai QC</button>
                                    <a href="?delete=<?php echo $row['id_barang']; ?>" class="btn btn-danger btn-sm mt-1">Delete</a>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
