<?php
require 'db.php'; // Koneksi ke database

// Fungsi Tambah Barang (Menu Belanja)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
    $id_awalan = $_POST['id_awalan']; // Ambil awalan ID dari form
    $id_barang = $_POST['id_barang'];  // Ambil bagian ID Barang yang diinputkan
    $qc_status = $_POST['qc_status']; // Status QC dari form
    $nama_barang = $_POST['nama_barang'];
    $tipe_barang = $_POST['tipe_barang'];
    $satuan_barang = $_POST['satuan_barang'];
    $nama_toko = $_POST['nama_toko'];
    $ekspedisi = $_POST['ekspedisi'];
    $belanja_via = $_POST['belanja_via'];
    $tanggal_order = $_POST['tanggal_order'];
    $tanggal_datang = $_POST['tanggal_datang'];
    $siapa_order = $_POST['siapa_order'];

    // Gabungkan awalan ID dengan ID barang yang diinput
    $id_barang_full = $id_awalan . $id_barang;

    if ($satuan_barang == 'Hasbel') {
        $stok = $_POST['stok'] * 1000; // Konversi 1 Hasbel = 1000 Meter
    } else {
        $stok = $_POST['stok']; // Satuan lain tetap
    }

    // Query untuk menambah barang
    $stmt = $pdo->prepare("INSERT INTO items (id_barang, nama_barang, tipe_barang, stok, satuan_barang, nama_toko, ekspedisi, belanja_via, tanggal_order, tanggal_datang, siapa_order, qc_status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$id_barang_full, $nama_barang, $tipe_barang, $stok, $satuan_barang, $nama_toko, $ekspedisi, $belanja_via, $tanggal_order, $tanggal_datang, $siapa_order, $qc_status])) {
        echo "Barang berhasil ditambahkan dan sedang menunggu QC!";
    } else {
        echo "Error: Barang gagal ditambahkan.";
    }
}

// Fungsi Hapus Barang
if (isset($_GET['delete'])) {
    $id_barang = $_GET['delete'];

    // Query untuk hapus barang
    $stmt = $pdo->prepare("DELETE FROM items WHERE id_barang = ?");
    $stmt->execute([$id_barang]);

    echo "Barang berhasil dihapus!";
}

// Fungsi Edit Barang
if (isset($_POST['edit'])) {
    $id_awalan = $_POST['id_awalan'];
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $tipe_barang = $_POST['tipe_barang'];
    $satuan_barang = $_POST['satuan_barang'];
    $nama_toko = $_POST['nama_toko'];
    $ekspedisi = $_POST['ekspedisi'];
    $belanja_via = $_POST['belanja_via'];
    $tanggal_order = $_POST['tanggal_order'];
    $tanggal_datang = $_POST['tanggal_datang'];
    $siapa_order = $_POST['siapa_order'];

    // Gabungkan awalan ID dengan ID barang yang diinput
    $id_barang_full = $id_awalan . $id_barang;

    if ($satuan_barang == 'Hasbel') {
        $stok = $_POST['stok'] * 1000; // Konversi 1 Hasbel = 1000 Meter
    } else {
        $stok = $_POST['stok']; // Satuan lain tetap
    }

    // Query untuk edit barang
    $stmt = $pdo->prepare("UPDATE items SET nama_barang = ?, tipe_barang = ?, stok = ?, satuan_barang = ?, nama_toko = ?, ekspedisi = ?, belanja_via = ?, tanggal_order = ?, tanggal_datang = ?, siapa_order = ? WHERE id_barang = ?");
    $stmt->execute([$nama_barang, $tipe_barang, $stok, $satuan_barang, $nama_toko, $ekspedisi, $belanja_via, $tanggal_order, $tanggal_datang, $siapa_order, $id_barang_full]);

    echo "Barang berhasil diupdate!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Menambahkan font barcode -->
    <style>
        @font-face {
            font-family: 'BarcodeFont';
            src: url('../fonts-barcode/Code39r.ttf') format('truetype');
        }

        /* Styling khusus untuk ukuran print */
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 6cm;
                height: 2cm;
                padding: 10px;
                text-align: center;
                font-size: 15px;
            }

            /* Membuat teks ID Barang menggunakan font barcode */
            .barcode {
                font-family: 'BarcodeFont';
                font-size: 40px;
            }
        }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>

<h2 class="text-center my-4">Tambah Barang Belanja</h2>
<div class="container">
    <form action="tambah_barang.php" method="post" class="form-group">
        <div class="row mb-3">
            <div class="col">
                <label for="id_awalan">ID</label>
                <select name="id_awalan" class="form-control" required>
                    <?php
                    // Query untuk mengambil awalan ID barang dari database
                    $stmt = $pdo->query("SELECT * FROM awalan_id_barang");
                    while ($row = $stmt->fetch()) {
                        echo "<option value=\"" . $row['kode_awalan'] . "\">" . $row['kode_awalan'] . " - " . $row['deskripsi_awalan'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <label for="id_barang">ID Barang:</label>
                <input type="text" class="form-control" name="id_barang" placeholder="Masukkan ID Barang" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" class="form-control" name="nama_barang" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="tipe_barang">Type:</label>
                <select name="tipe_barang" class="form-control" required>
                    <?php
                    // Query untuk mengambil tipe barang dari database
                    $stmt = $pdo->query("SELECT * FROM tipe_barang");
                    while ($row = $stmt->fetch()) {
                        echo "<option value=\"" . $row['nama_tipe'] . "\">" . $row['nama_tipe'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <label for="stok">Stok:</label>
                <input type="number" class="form-control" name="stok" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="satuan_barang">Satuan Barang:</label>
                <select name="satuan_barang" class="form-control" required>
                    <?php
                    // Query untuk mengambil satuan barang dari database
                    $stmt = $pdo->query("SELECT * FROM satuan_barang");
                    while ($row = $stmt->fetch()) {
                        echo "<option value=\"" . $row['nama_satuan'] . "\">" . $row['nama_satuan'] . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nama_toko">Nama Toko:</label>
                <input type="text" class="form-control" name="nama_toko" required>
            </div>
            <div class="col">
                <label for="ekspedisi">Ekspedisi:</label>
                <input type="text" class="form-control" name="ekspedisi" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="belanja_via">Belanja Via:</label>
                <select name="belanja_via" class="form-control" required>
                    <option value="Offline">Offline</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            <div class="col">
                <label for="siapa_order">Petugas Order:</label>
                <input type="text" class="form-control" name="siapa_order" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="tanggal_order">Tanggal Order:</label>
                <input type="date" class="form-control" name="tanggal_order" required>
            </div>
            <div class="col">
                <label for="tanggal_datang">Tanggal Barang Datang:</label>
                <input type="date" class="form-control" name="tanggal_datang" required>
            </div>
        </div>

    <!-- Status QC (Otomatis diatur sebagai Menunggu QC) -->
    <input type="hidden" name="qc_status" value="Menunggu QC">

        <button type="submit" class="btn btn-primary mt-3">Tambah Barang</button>
    </form>

    <a href="kelola_awalan_id.php" class="btn btn-secondary mt-3">Kelola Awalan ID Barang</a>
    <a href="manage_tipe_barang.php" class="btn btn-secondary mt-3">Kelola Tipe Barang</a>

    <h2 class="my-4">Daftar Barang</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Type</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Nama Toko</th>
                <th>Ekspedisi</th>
                <th>Belanja Via</th>
                <th> Petugas</th>
                <th>Tanggal Order</th>
                <th>Tanggal Datang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk menampilkan barang dari database
            $stmt = $pdo->query("SELECT * FROM items");
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $row['id_barang'] . "</td>";
                echo "<td>" . $row['nama_barang'] . "</td>";
                echo "<td>" . $row['tipe_barang'] . "</td>";
                echo "<td>" . $row['stok'] . "</td>";
                echo "<td>" . $row['satuan_barang'] . "</td>";
                echo "<td>" . $row['nama_toko'] . "</td>";
                echo "<td>" . $row['ekspedisi'] . "</td>";
                echo "<td>" . $row['belanja_via'] . "</td>";
                echo "<td>" . $row['siapa_order'] . "</td>";
                echo "<td>" . $row['tanggal_order'] . "</td>";
                echo "<td>" . $row['tanggal_datang'] . "</td>";
                echo "<td>
                    <a href='tambah_barang.php?delete=" . $row['id_barang'] . "' class='btn btn-danger btn-sm'>Hapus</a>
                    <a href='edit_barang.php?id=" . $row['id_barang'] . "' class='btn btn-warning btn-sm'>Edit</a>
                    <button class='btn btn-info btn-sm' onclick='printLabel(\"" . $row['id_barang'] . "\")'>Print</button>
                    <a href='../quality_control/qc_dashboard.php?id=" . $row['id_barang'] . "' class='btn btn-success btn-sm' style='font-size: 12px; padding: 2px 5px;'>Lanjut QC</a>

                </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="print-area" class="print-area" style="display:none;">
    <p><strong></strong></p>
    <img id="print-id-barang" style="width:50px; height:50px;" alt="QR Code" />
    <p id="text-id-barang" style="font-size: 10px;">Teks biasa di bawah QR Code</p>
</div>

</div>
</div>

<script>
 function printLabel(id_barang) {
    // URL API QR Code
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${id_barang}&size=200x200`;

    // Set gambar QR Code
    const qrImage = document.getElementById('print-id-barang');
    qrImage.src = qrCodeUrl;

    // Tampilkan teks biasa
    document.getElementById('text-id-barang').innerText = id_barang;

    // Tampilkan area cetak
    document.getElementById('print-area').style.display = 'block';

    // Cetak halaman
    window.print();

    // Sembunyikan kembali area cetak setelah selesai
    setTimeout(function () {
        document.getElementById('print-area').style.display = 'none';
    }, 1000);
}

</script>

<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
