<?php
// Query untuk mendapatkan semua data barang
$query = "SELECT * FROM barang";
$result = mysqli_query($conn, $query);

echo "<table border='1'>";
echo "<tr>
        <th>ID Barang</th>
        <th>Nama Barang</th>
        <th>Type</th>
        <th>Stok</th>
        <th>Status QC</th>
        <th>Aksi</th>
      </tr>";

// Loop data barang
while ($row = mysqli_fetch_assoc($result)) {
    $qc_status = $row['qc_status'];
    $status_color = ''; // Warna awal

    // Menentukan warna berdasarkan status QC
    if ($qc_status == 'Pending') {
        $status_color = 'orange';
    } elseif ($qc_status == 'Approved') {
        $status_color = 'green';
    } elseif ($qc_status == 'Rejected') {
        $status_color = 'red';
    }

    // Tampilkan data barang dengan warna status QC
    echo "<tr>";
    echo "<td>{$row['id_barang']}</td>";
    echo "<td>{$row['nama_barang']}</td>";
    echo "<td>{$row['type']}</td>";
    echo "<td>{$row['stok']}</td>";
    echo "<td style='color: {$status_color}; font-weight: bold;'>{$qc_status}</td>";
    echo "<td>
          <a href='approve_qc.php?id={$row['id_barang']}'>Approve</a> |
          <a href='reject_qc.php?id={$row['id_barang']}'>Reject</a>
          </td>";
    echo "</tr>";
}

echo "</table>";
?>
