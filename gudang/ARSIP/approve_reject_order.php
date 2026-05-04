<?php
// Koneksi ke database
require '../belanja/db.php'; // Pastikan path benar

// Query untuk mendapatkan order yang menunggu konfirmasi
$query = "SELECT * FROM order_barang WHERE status = 'Menunggu Konfirmasi'";
$result = $pdo->query($query); // Jalankan query

// Jika admin menyetujui atau menolak order
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $id_barang = $_POST['id_barang'];

    // Jika status "Ditolak", tidak perlu cek ID Barang atau mengurangi stok
    if ($status == 'Ditolak') {
        // Update status order tanpa perlu ID Barang
        $update = $pdo->prepare("UPDATE order_barang SET status = ? WHERE request_id = ?");
        $update->execute([$status, $order_id]);

        echo "<div class='alert alert-success'>Order dengan Request ID $order_id telah ditolak!</div>";
    } else {
        // Cek apakah ID Barang ada di database untuk status "Diterima"
        $check_id = $pdo->prepare("SELECT * FROM barang_masuk_gudang WHERE id_barang = ?");
        $check_id->execute([$id_barang]);

        if ($check_id->rowCount() > 0) {
            if ($status == 'Diterima') {
                // Update stok di tabel barang_masuk_gudang HANYA jika status "Diterima"
                $update_stok = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok - 1 WHERE id_barang = ?");
                $update_stok->execute([$id_barang]);

                // Catat barang yang keluar ke tabel barang_keluar
                $barang = $check_id->fetch();
                $insert_keluar = $pdo->prepare("INSERT INTO barang_keluar (id_barang, nama_barang, tipe_barang, jumlah_keluar, tanggal_keluar)
                                                VALUES (?, ?, ?, ?, NOW())");
                $insert_keluar->execute([$barang['id_barang'], $barang['nama_barang'], $barang['tipe_barang'], 1]);

                echo "<div class='alert alert-success'>Order dengan Request ID $order_id telah disetujui dan barang tercatat keluar!</div>";
            }

            // Update status order
            $update = $pdo->prepare("UPDATE order_barang SET status = ?, id_barang = ? WHERE request_id = ?");
            $update->execute([$status, $id_barang, $order_id]);

        } else {
            // Jika ID Barang tidak ditemukan
            echo "<div class='alert alert-danger'>Error: ID Barang $id_barang tidak ditemukan di database!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Order Barang</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Daftar Order yang Menunggu Konfirmasi</h2>

    <!-- Tabel untuk menampilkan daftar order yang menunggu konfirmasi -->
    <form method="POST" action="">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Request ID</th>
                    <th>Tipe Barang</th>
                    <th>Jumlah</th>
                    <th>Nama Teknisi</th>
                    <th>Penggunaan</th>
                    <th>Keterangan</th>
                    <th>ID Barang</th> <!-- Admin memasukkan ID Barang -->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch()) { ?>
                    <tr>
                        <td><?php echo $row['request_id']; ?></td>
                        <td><?php echo $row['tipe_barang']; ?></td>
                        <td><?php echo $row['jumlah_order']; ?></td>
                        <td><?php echo $row['nama_teknisi']; ?></td>
                        <td><?php echo $row['penggunaan']; ?></td>
                        <td><?php echo $row['keterangan']; ?></td>
                        <td>
                            <input type="text" name="id_barang" class="form-control" placeholder="Masukkan ID Barang" required>
                        </td>
                        <td>
                            <input type="hidden" name="order_id" value="<?php echo $row['request_id']; ?>">
                            <select name="status" class="form-control">
                                <option value="Diterima">Setujui</option>
                                <option value="Ditolak">Tolak</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary mt-2">Update Status</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
