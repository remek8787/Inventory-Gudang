<?php
require '../belanja/db.php'; // Koneksi ke database

// Handle data insertion from list_selesai_qc.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];

    // Check if the item already exists in the table
    $checkQuery = $pdo->prepare("SELECT * FROM barang_masuk_gudang WHERE id_barang = ?");
    $checkQuery->execute([$id_barang]);

    if ($checkQuery->rowCount() == 0) {
        // If no duplicate exists, insert the data into barang_masuk_gudang
        $stmt = $pdo->prepare("INSERT INTO barang_masuk_gudang (id_barang, nama_barang, tipe_barang, mac_address, stok, satuan_barang, nama_toko, ekspedisi, belanja_via, siapa_order, petugas_qc, tanggal_order, tanggal_datang, tanggal_qc, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_barang'],
            $_POST['nama_barang'],
            $_POST['tipe_barang'],
            $_POST['mac_address'],
            $_POST['stok'],
            $_POST['satuan_barang'],
            $_POST['nama_toko'],
            $_POST['ekspedisi'],
            $_POST['belanja_via'],
            $_POST['siapa_order'],
            $_POST['petugas_qc'],
            $_POST['tanggal_order'],
            $_POST['tanggal_datang'],
            $_POST['tanggal_qc'],
            $_POST['keterangan']
        ]);

        echo "Barang berhasil dimasukkan ke Gudang!";
    } else {
        echo "Barang sudah ada di Gudang, tidak bisa ditambahkan lagi!";
    }
}

// Inisialisasi variabel pencarian
$id_barang = '';
$tanggal = '';
$bulan = '';
$tahun = '';

// Cek apakah ada pencarian yang dilakukan
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_barang = isset($_GET['id_barang']) ? $_GET['id_barang'] : '';
    $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
    $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
    $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

    // Query pencarian
    $query = "SELECT * FROM barang_masuk_gudang WHERE 1";

    if (!empty($id_barang)) {
        $query .= " AND id_barang LIKE '%$id_barang%'";
    }
    if (!empty($tanggal)) {
        $query .= " AND DAY(tanggal_order) = '$tanggal'";
    }
    if (!empty($bulan)) {
        $query .= " AND MONTH(tanggal_order) = '$bulan'";
    }
    if (!empty($tahun)) {
        $query .= " AND YEAR(tanggal_order) = '$tahun'";
    }

    $stmt = $pdo->query($query);
} else {
    // Jika tidak ada pencarian, tampilkan semua data
    $stmt = $pdo->query("SELECT * FROM barang_masuk_gudang");
}

// Fitur export ke CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="barang_masuk_gudang.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID Barang', 'Nama Barang', 'Tipe Barang', 'Mac Address', 'Stok', 'Satuan', 'Nama Toko', 'Ekspedisi', 'Belanja Via', 'Siapa Order', 'Petugas QC', 'Tanggal Order', 'Tanggal Datang', 'Tanggal QC', 'Keterangan'));

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Barang Masuk Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Daftar Barang Masuk Gudang</h2>

        <!-- Form Pencarian -->
        <form method="GET" action="" class="form-inline mb-4">
            <input type="text" name="id_barang" class="form-control mr-2" placeholder="Cari ID Barang" value="<?php echo htmlspecialchars($id_barang); ?>">
            <input type="number" name="tanggal" class="form-control mr-2" placeholder="Tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" min="1" max="31">
            <input type="number" name="bulan" class="form-control mr-2" placeholder="Bulan" value="<?php echo htmlspecialchars($bulan); ?>" min="1" max="12">
            <input type="number" name="tahun" class="form-control mr-2" placeholder="Tahun" value="<?php echo htmlspecialchars($tahun); ?>" min="2000">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="?export=true" class="btn btn-success ml-2">Export ke CSV</a>
        </form>

        <!-- Tabel Data -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Tipe Barang</th>
                        <th>Mac Address</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Nama Toko</th>
                        <th>Ekspedisi</th>
                        <th>Belanja Via</th>
                        <th>Siapa Order</th>
                        <th>Petugas QC</th>
                        <th>Tanggal Order</th>
                        <th>Tanggal Datang</th>
                        <th>Tanggal QC</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()) { ?>
                        <tr>
                            <td><?php echo $row['id_barang']; ?></td>
                            <td><?php echo $row['nama_barang']; ?></td>
                            <td><?php echo $row['tipe_barang']; ?></td>
                            <td><?php echo $row['mac_address']; ?></td>
                            <td><?php echo $row['stok']; ?></td>
                            <td><?php echo $row['satuan_barang']; ?></td>
                            <td><?php echo $row['nama_toko']; ?></td>
                            <td><?php echo $row['ekspedisi']; ?></td>
                            <td><?php echo $row['belanja_via']; ?></td>
                            <td><?php echo $row['siapa_order']; ?></td>
                            <td><?php echo $row['petugas_qc']; ?></td>
                            <td><?php echo $row['tanggal_order']; ?></td>
                            <td><?php echo $row['tanggal_datang']; ?></td>
                            <td><?php echo $row['tanggal_qc']; ?></td>
                            <td><?php echo $row['keterangan']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
