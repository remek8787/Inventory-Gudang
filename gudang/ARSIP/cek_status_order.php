<?php
// Koneksi ke database
require '../belanja/db.php'; // Pastikan path benar

// Query untuk mengambil semua order berdasarkan teknisi tertentu atau semua order jika tanpa filter teknisi
$query_order = "SELECT request_id, tipe_barang, jumlah_order, status, alasan_penolakan, admin_approval 
                FROM order_barang ORDER BY request_id DESC";
$result_order = $pdo->query($query_order);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Order Barang</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .approved { color: green; }
        .rejected { color: red; }
        .pending { color: orange; }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>

<h2>Cek Status Order Barang</h2>

<table>
    <tr>
        <th>Request ID</th>
        <th>Tipe Barang</th>
        <th>Jumlah</th>
        <th>Status</th>
        <th>Admin Approval</th>
        <th>Alasan Penolakan</th>
    </tr>
    <?php while ($row_order = $result_order->fetch()) { 
        // Styling berdasarkan status
        $status_class = '';
        if ($row_order['status'] == 'Disetujui') {
            $status_class = 'approved';
        } elseif ($row_order['status'] == 'Ditolak') {
            $status_class = 'rejected';
        } else {
            $status_class = 'pending';
        }
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row_order['request_id']); ?></td>
            <td><?php echo htmlspecialchars($row_order['tipe_barang']); ?></td>
            <td><?php echo htmlspecialchars($row_order['jumlah_order']); ?></td>
            <td class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($row_order['status']); ?></td>
            <td><?php echo htmlspecialchars($row_order['admin_approval']); ?></td>
            <td><?php echo htmlspecialchars($row_order['alasan_penolakan'] ? $row_order['alasan_penolakan'] : '-'); ?></td>
        </tr>
    <?php } ?>
</table>

<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
