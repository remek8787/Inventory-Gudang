<?php
require '../belanja/db.php'; // Koneksi ke database

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_barang = $_POST['id_barang'];
    $petugas_qc = $_POST['petugas_qc'];
    $keterangan_qc = $_POST['keterangan_qc'];

    // Validasi input - pastikan tidak kosong
    if (!empty($id_barang) && !empty($petugas_qc) && !empty($keterangan_qc)) {
        // Update data petugas QC dan keterangan di database
        $stmt = $pdo->prepare("UPDATE items SET petugas_qc = ?, keterangan_qc = ? WHERE id_barang = ?");
        $result = $stmt->execute([$petugas_qc, $keterangan_qc, $id_barang]);

        // Cek apakah query berhasil
        if ($result) {
            // Redirect kembali ke halaman qc_retur.php dengan pesan sukses
            header("Location: qc_retur.php?status=success&msg=Data berhasil diperbarui");
            exit();
        } else {
            // Redirect kembali dengan pesan error
            header("Location: qc_retur.php?status=error&msg=Gagal memperbarui data");
            exit();
        }
    } else {
        // Jika ada input yang kosong, kembalikan dengan pesan error
        header("Location: qc_retur.php?status=error&msg=Semua field harus diisi");
        exit();
    }
} else {
    // Jika request bukan POST, kembalikan error
    header("Location: qc_retur.php?status=error&msg=Request tidak valid");
    exit();
}
?>
