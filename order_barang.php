<?php
include 'belanja/db.php';

$result = $conn->query("SELECT * FROM barang");

echo "<h2>Daftar Barang</h2>";
echo "<table>";
echo "<tr><th>Nama Barang</th><th>Jumlah Tersedia</th><th>Pesan</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['jumlah'] . "</td>";
    echo "<td><form method='post' action='proses_pemesanan.php'>
            <input type='hidden' name='barang_id' value='" . $row['id'] . "'>
            <input type='number' name='jumlah' min='1' max='" . $row['jumlah'] . "' required>
            <button type='submit'>Pesan</button>
          </form></td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>