<?php
include require '../belanja/db.php'; // Koneksi ke database
$id_permintaan = $_GET['id'];
$action = $_GET['action'];
$admin_name = 'admin_login'; // Ambil dari sesi login
$admin_role = 'admin_role';  // Ambil dari sesi login
$date = date('Y-m-d H:i:s');

if ($action == 'approve') {
    $query = "UPDATE permintaan_barang SET status = 'Disetujui', admin='$admin_name', role='$admin_role', waktu_persetujuan='$date' WHERE id_permintaan='$id_permintaan'";
} else {
    $query = "UPDATE permintaan_barang SET status = 'Ditolak' WHERE id_permintaan='$id_permintaan'";
}

if (mysqli_query($conn, $query)) {
    echo "Permintaan berhasil diproses!";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}
?>
