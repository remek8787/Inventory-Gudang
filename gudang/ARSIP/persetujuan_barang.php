<?php
include require '../belanja/db.php'; // Koneksi ke database
$query = "SELECT * FROM permintaan_barang WHERE status = 'Menunggu Persetujuan'";
$result = mysqli_query($conn, $query);
?>

<table>
   <tr>
      <th>ID Barang</th>
      <th>Nama Teknisi</th>
      <th>Jumlah</th>
      <th>Keterangan</th>
      <th>Aksi</th>
   </tr>
   <?php while ($row = mysqli_fetch_assoc($result)) { ?>
   <tr>
      <td><?= $row['id_barang']; ?></td>
      <td><?= $row['nama_teknisi']; ?></td>
      <td><?= $row['jumlah_barang']; ?></td>
      <td><?= $row['keterangan']; ?></td>
      <td>
         <a href="proses_persetujuan.php?id=<?= $row['id_permintaan']; ?>&action=approve">Setujui</a>
         <a href="proses_persetujuan.php?id=<?= $row['id_permintaan']; ?>&action=reject">Tolak</a>
      </td>
   </tr>
   <?php } ?>
</table>
