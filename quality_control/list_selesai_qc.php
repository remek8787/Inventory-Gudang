<?php
require '../belanja/db.php'; // Koneksi ke database

// Logika untuk mengembalikan barang ke QC Lolos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['undo_qc'])) {
    $id_barang = $_POST['id_barang'];
    $alasan = $_POST['alasan'];

    // Update status barang menjadi 'Lolos QC'
    $stmt = $pdo->prepare("UPDATE items SET qc_status = 'Lolos QC', alasan_undo_qc = ? WHERE id_barang = ?");
    $stmt->execute([$alasan, $id_barang]);

    echo "Barang berhasil dikembalikan ke QC Lolos!";
}

// Logika untuk filter berdasarkan ID Barang
$where_clause = ""; // Default no where clause

if (isset($_GET['id_barang']) && !empty($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];
    $where_clause .= "AND id_barang = '$id_barang' ";
}

// Query untuk menampilkan barang dengan status 'Selesai QC' berdasarkan ID Barang
$query = "SELECT * FROM items WHERE qc_status = 'Selesai QC' $where_clause";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Logika Ekspor CSV berdasarkan hasil filter
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=barang_selesai_qc.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID Barang', 'Nama Barang', 'Tipe', 'Mac Address', 'Stok', 'Satuan', 'Nama Toko', 'Ekspedisi', 'Belanja Via', 'Petugas Order', 'Petugas QC', 'Tanggal Order', 'Tanggal Datang', 'Tanggal QC', 'Keterangan'));

    $result = $pdo->query($query);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Barang Selesai QC</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 200px;
            position: fixed;
            height: 100%;
            background: #343a40;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.5);
        }
        .sidebar a {
            color: white;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #007bff;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
    </style>

<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
<div class="sidebar">
        <h4 class="text-center text-white">Menu List Barang Selesai QC</h4>
        <a href="/quality_control/qc_dashboard.php">Back Dashboard QC</a>
        <a href="/gudang/barang_masuk_gudang.php">Ke Dhasboard Gudang</a>
    </div>


    <div class="container mt-5">
        <h2 class="mb-4 text-center">List Barang Selesai QC</h2>

        <!-- Form Pencarian berdasarkan ID Barang -->
        <form method="GET" action="list_selesai_qc.php" class="form-inline">
            <input type="text" class="form-control mr-2" name="id_barang" placeholder="Cari ID Barang" value="<?= $_GET['id_barang'] ?? '' ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="list_selesai_qc.php?export=true" class="btn btn-success ml-2">Export ke CSV</a>
        </form>

        <!-- Tabel Data Barang -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
                    <th>Mac Address</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Nama Toko</th>
                    <th>Ekspedisi</th>
                    <th>Belanja Via</th>
                    <th>Petugas Order</th>
                    <th>Petugas QC</th>
                    <th>Tanggal Order</th>
                    <th>Tanggal Datang</th>
                    <th>Tanggal QC</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
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
                    <td><?php echo $row['petugas_order']; ?></td>
                    <td><?php echo $row['petugas_qc']; ?></td>
                    <td><?php echo $row['tanggal_order']; ?></td>
                    <td><?php echo $row['tanggal_datang']; ?></td>
                    <td><?php echo $row['tanggal_qc']; ?></td>
                    <td><?php echo $row['keterangan']; ?></td>
                    <td>
                        <!-- Form untuk mengembalikan barang ke QC Lolos -->
                        <form method="POST" action="">
                            <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                            <input type="text" name="alasan" class="form-control" placeholder="Alasan Undo">
                            <button type="submit" name="undo_qc" class="btn btn-warning btn-sm mt-2">Kembalikan ke QC</button>
                        </form>
                        <!-- Form Untuk Masuk Ke Barang Masuk Gudang -->
                        <form method="POST" action="../gudang/barang_masuk_gudang.php">
                            <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                            <input type="hidden" name="nama_barang" value="<?php echo $row['nama_barang']; ?>">
                            <input type="hidden" name="tipe_barang" value="<?php echo $row['tipe_barang']; ?>">
                            <input type="hidden" name="mac_address" value="<?php echo $row['mac_address']; ?>">
                            <input type="hidden" name="stok" value="<?php echo $row['stok']; ?>">
                            <input type="hidden" name="satuan_barang" value="<?php echo $row['satuan_barang']; ?>">
                            <input type="hidden" name="nama_toko" value="<?php echo $row['nama_toko']; ?>">
                            <input type="hidden" name="ekspedisi" value="<?php echo $row['ekspedisi']; ?>">
                            <input type="hidden" name="belanja_via" value="<?php echo $row['belanja_via']; ?>">
                            <input type="hidden" name="siapa_order" value="<?php echo $row['siapa_order']; ?>">
                            <input type="hidden" name="petugas_qc" value="<?php echo $row['petugas_qc']; ?>">
                            <input type="hidden" name="tanggal_order" value="<?php echo $row['tanggal_order']; ?>">
                            <input type="hidden" name="tanggal_datang" value="<?php echo $row['tanggal_datang']; ?>">
                            <input type="hidden" name="tanggal_qc" value="<?php echo $row['tanggal_qc']; ?>">
                            <input type="hidden" name="keterangan" value="<?php echo $row['keterangan']; ?>">
                            <!-- Button Kirim ke Gudang -->
                            <button type="submit" class="btn btn-success btn-sm mt-2">Kirim ke Gudang</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
