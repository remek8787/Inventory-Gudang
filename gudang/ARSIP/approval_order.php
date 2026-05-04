<?php
// Koneksi ke database
require '../belanja/db.php'; // Pastikan path benar

// Ambil semua order yang statusnya 'Menunggu Konfirmasi'
$query_order = "SELECT * FROM order_barang WHERE status = 'Menunggu Konfirmasi'";
$result_order = $pdo->query($query_order);

// Jika admin meng-approve order, update status dan masukkan ID Barang serta nama admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $request_id = $_POST['request_id'];
    $id_barang = $_POST['id_barang'];
    $admin_approval = $_POST['admin_approval']; // Nama admin yang melakukan approval
    
    // Update order dengan status 'Disetujui', ID Barang, dan nama admin
    $stmt = $pdo->prepare("UPDATE order_barang SET id_barang = ?, status = 'Disetujui', admin_approval = ? WHERE request_id = ?");
    $stmt->execute([$id_barang, $admin_approval, $request_id]);

    // Mengambil tipe barang dan jumlah order untuk pengurangan stok
    $stmt_order = $pdo->prepare("SELECT tipe_barang, jumlah_order FROM order_barang WHERE request_id = ?");
    $stmt_order->execute([$request_id]);
    $order = $stmt_order->fetch();

    if ($order) {
        $tipe_barang = $order['tipe_barang'];
        $jumlah_order = $order['jumlah_order'];

        // Kurangi stok di tabel barang_masuk_gudang berdasarkan tipe barang
        $stmt_update_stock = $pdo->prepare("UPDATE barang_masuk_gudang SET jumlah_stok = jumlah_stok - ? WHERE tipe_barang = ?");
        $stmt_update_stock->execute([$jumlah_order, $tipe_barang]);

        echo "Order dengan Request ID: $request_id berhasil di-approve oleh Admin: $admin_approval dan stok berkurang.";
    } else {
        echo "Gagal mengurangi stok: Tipe barang atau jumlah order tidak ditemukan.";
    }

    header("Location: approval_order.php"); // Refresh halaman setelah approval
    exit;
}

// Jika admin menolak order, update status menjadi 'Ditolak' dan simpan alasan penolakan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject'])) {
    $request_id = $_POST['request_id'];
    $alasan_penolakan = $_POST['alasan_penolakan'];
    
    // Update order dengan status 'Ditolak' dan alasan penolakan
    $stmt = $pdo->prepare("UPDATE order_barang SET status = 'Ditolak', alasan_penolakan = ? WHERE request_id = ?");
    $stmt->execute([$alasan_penolakan, $request_id]);

    echo "Order dengan Request ID: $request_id telah ditolak dengan alasan: $alasan_penolakan.";
    header("Location: approval_order.php"); // Refresh halaman setelah penolakan
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Order Barang</title>
</head>
<body>

<h2>Approval Order Barang</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Request ID</th>
        <th>Tipe Barang</th>
        <th>Jumlah</th>
        <th>Nama Teknisi</th>
        <th>Penggunaan</th>
        <th>Keterangan</th>
        <th>ID Barang</th>
        <th>Nama Admin Approval</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row_order = $result_order->fetch()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row_order['request_id']); ?></td>
            <td><?php echo htmlspecialchars($row_order['tipe_barang']); ?></td>
            <td><?php echo htmlspecialchars($row_order['jumlah_order']); ?></td>
            <td><?php echo htmlspecialchars($row_order['nama_teknisi']); ?></td>
            <td><?php echo htmlspecialchars($row_order['penggunaan']); ?></td>
            <td><?php echo htmlspecialchars($row_order['keterangan']); ?></td>
            <td>
                <form method="POST" action="">
                    <input type="text" name="id_barang" placeholder="ID Barang">
                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row_order['request_id']); ?>">
            </td>
            <td>
                <input type="text" name="admin_approval" placeholder="Nama Admin">
            </td>
            <td>
                    <button type="submit" name="approve">Approve</button>
                </form>
                
                <!-- Form untuk menolak order -->
                <form method="POST" action="" style="margin-top: 5px;">
                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row_order['request_id']); ?>">
                    <input type="text" name="alasan_penolakan" placeholder="Alasan Penolakan" required>
                    <button type="submit" name="reject">Reject</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
