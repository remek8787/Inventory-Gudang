<?php
require '../belanja/db.php'; // Atur path ke db.php sesuai struktur folder Anda


// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($password)) {
        echo "Semua kolom harus diisi.";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Ambil izin akses dari checkbox form
    $izin_akses = [
        'akses_barang_yang_sudah_keluar' => isset($_POST['akses_barang_yang_sudah_keluar']),
        'akses_barang_masuk_gudang' => isset($_POST['akses_barang_masuk_gudang']),
        'akses_tambah_barang' => isset($_POST['akses_tambah_barang']),
        'akses_dhasboar_barang_belanja' => isset($_POST['akses_dhasboar_barang_belanja']),
        'akses_qc_lolos' => isset($_POST['akses_qc_lolos']),
        'akses_qc_retur' => isset($_POST['akses_qc_retur']),
        'akses_list_selesai_qc' => isset($_POST['akses_list_selesai_qc']),
        'akses_qc_dashboard' => isset($_POST['akses_qc_dashboard']),
        'akses_reset_delete_stok' => isset($_POST['akses_reset_delete_stok'])
    ];

    // Ubah array izin akses menjadi JSON
    $izin_akses_json = json_encode($izin_akses);

    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, izin_akses) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $izin_akses_json]);

    // Pesan sukses atau redirect
    header("Location: kelola_izin_pengguna.php?sukses=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Pengguna</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <h4>Izin Akses:</h4>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="akses_barang_yang_sudah_keluar" name="akses_barang_yang_sudah_keluar">
            <label class="form-check-label" for="akses_barang_yang_sudah_keluar"> Akses Barang Yang Sudah Keluar </label>
        </div>
        <!-- Tambahkan checkbox lainnya sesuai izin yang diperlukan -->

        <button type="submit" class="btn btn-primary mt-3">Tambah Pengguna</button>
    </form>
</div>
</body>
</html>
