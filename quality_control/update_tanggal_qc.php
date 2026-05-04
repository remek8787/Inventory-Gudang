<?php
require '../belanja/db.php'; // Koneksi ke database

// Ambil data dari form
$tanggal_qc = date('Y-m-d'); // Mengambil tanggal saat ini
$petugas_qc = $_POST['petugas_qc'];
$id_barang = $_POST['id_barang'];

// Update data di tabel QC dengan menyertakan tanggal QC
$stmt = $pdo->prepare("UPDATE qc_lolos SET petugas_qc = ?, tanggal_qc = ? WHERE id_barang = ?");
$stmt->execute([$petugas_qc, $tanggal_qc, $id_barang]);

// Redirect atau feedback ke user
header("Location: qc_lolos.php?status=success&msg=Tanggal QC dan Petugas QC berhasil diperbarui");
exit();
