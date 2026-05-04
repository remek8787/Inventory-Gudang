<?php
require '../belanja/db.php'; // Koneksi ke database

// Logging function to help with debugging
function log_message($message) {
    error_log($message); // Writes the message to the web server's error log
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $checkQuery = $pdo->prepare("SELECT * FROM barang_masuk_gudang WHERE id_barang = ?");
    $checkQuery->execute([$id_barang]);
    log_message('Checking ID: ' . $id_barang);

    if ($checkQuery->rowCount() == 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO barang_masuk_gudang (id_barang, nama_barang, tipe_barang, mac_address, stok, satuan_barang, nama_toko, ekspedisi, belanja_via, siapa_order, petugas_qc, tanggal_order, tanggal_datang, tanggal_qc, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['id_barang'], $_POST['nama_barang'], $_POST['tipe_barang'], $_POST['mac_address'],
                $_POST['stok'], $_POST['satuan_barang'], $_POST['nama_toko'], $_POST['ekspedisi'], $_POST['belanja_via'],
                $_POST['siapa_order'], $_POST['petugas_qc'], $_POST['tanggal_order'], $_POST['tanggal_datang'], $_POST['tanggal_qc'], $_POST['keterangan']
            ]);
            log_message('Insert successful: ' . $id_barang);
        } catch (PDOException $e) {
            log_message('Insert failed: ' . $e->getMessage());
        }
    } else {
        log_message('ID already exists: ' . $id_barang);
    }
}

// Filter Input
$search_id = $_GET['id_barang'] ?? '';
$search_nama = $_GET['nama_barang'] ?? '';
$search_mac = $_GET['mac_address'] ?? '';
$search_ekspedisi = $_GET['ekspedisi'] ?? '';
$search_tahun = $_GET['tahun'] ?? '';
$search_bulan = $_GET['bulan'] ?? '';

// Query dengan Filter
$query = "SELECT * FROM barang_masuk_gudang WHERE 1=1";
$params = [];

if ($search_id) {
    $query .= " AND id_barang LIKE ?";
    $params[] = "%$search_id%";
}
if ($search_nama) {
    $query .= " AND nama_barang LIKE ?";
    $params[] = "%$search_nama%";
}
if ($search_mac) {
    $query .= " AND mac_address LIKE ?";
    $params[] = "%$search_mac%";
}
if ($search_ekspedisi) {
    $query .= " AND ekspedisi LIKE ?";
    $params[] = "%$search_ekspedisi%";
}
if ($search_tahun) {
    $query .= " AND YEAR(tanggal_order) = ?";
    $params[] = $search_tahun;
}
if ($search_bulan) {
    $query .= " AND MONTH(tanggal_order) = ?";
    $params[] = $search_bulan;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang Masuk Gudang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        #sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            transition: all 0.3s;
        }
        #sidebar a:hover {
            background-color: #007bff;
        }
        #content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <!-- Side Panel -->
    <div id="sidebar">
        <h4 class="text-center">Dhasboard Gudang</h4>
        <!-- <a href="/quality_control/list_selesai_qc.php">Back To Selesai QC</a> -->
        <a href="/index.php">Back To Storage</a>
		<a href="/gudang/stok_gudang.php">Ship Stock</a>
        <a href="/gudang/barang_keluar.php">Ship Out</a>
        <a href="/gudang/riwayat_pengeluaran.php">Shipment History</a>
    </div>

    <!-- Konten Utama -->
    <div id="content">
        <div class="container">
            <h2 class="text-center">Daftar Barang Masuk Gudang</h2>

            <!-- Form Filter -->
            <form method="GET" class="form-inline mb-3">
                <input type="text" name="id_barang" placeholder="ID Barang" class="form-control mr-2" value="<?= htmlspecialchars($search_id) ?>">
                <input type="text" name="nama_barang" placeholder="Nama Barang" class="form-control mr-2" value="<?= htmlspecialchars($search_nama) ?>">
                <input type="text" name="mac_address" placeholder="MAC Address" class="form-control mr-2" value="<?= htmlspecialchars($search_mac) ?>">
                <select name="bulan" class="form-control mr-2">
                    <option value="">Bulan</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $search_bulan == $i ? 'selected' : '' ?>>
                            <?= date("F", mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <input type="number" name="tahun" placeholder="Tahun" class="form-control mr-2" value="<?= htmlspecialchars($search_tahun) ?>">
                <input type="text" name="ekspedisi" placeholder="Ekspedisi" class="form-control mr-2" value="<?= htmlspecialchars($search_ekspedisi) ?>">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered">
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
                        <?php while ($row = $stmt->fetch()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_barang']) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['tipe_barang']) ?></td>
                                <td><?= htmlspecialchars($row['mac_address']) ?></td>
                                <td><?= htmlspecialchars($row['stok']) ?></td>
                                <td><?= htmlspecialchars($row['satuan_barang']) ?></td>
                                <td><?= htmlspecialchars($row['nama_toko']) ?></td>
                                <td><?= htmlspecialchars($row['ekspedisi']) ?></td>
                                <td><?= htmlspecialchars($row['belanja_via']) ?></td>
                                <td><?= htmlspecialchars($row['siapa_order']) ?></td>
                                <td><?= htmlspecialchars($row['petugas_qc']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_order']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_datang']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_qc']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
