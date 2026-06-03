<?php
require 'db.php'; // Koneksi ke database

$flash_message = '';
$flash_type = 'success';

function normalize_barang_id($value) {
    return strtoupper(preg_replace('/\s+/', '', trim((string)$value)));
}

// Fungsi Tambah Barang (Menu Belanja)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
    $id_awalan = normalize_barang_id($_POST['id_awalan'] ?? ''); // Ambil awalan ID dari form
    $id_barang = normalize_barang_id($_POST['id_barang'] ?? '');  // Ambil bagian ID Barang yang diinputkan
    $qc_status = $_POST['qc_status'] ?? 'Menunggu QC'; // Status QC dari form
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $tipe_barang = trim($_POST['tipe_barang'] ?? '');
    $satuan_barang = trim($_POST['satuan_barang'] ?? '');
    $nama_toko = trim($_POST['nama_toko'] ?? '');
    $ekspedisi = trim($_POST['ekspedisi'] ?? '');
    $belanja_via = trim($_POST['belanja_via'] ?? '');
    $tanggal_order = trim($_POST['tanggal_order'] ?? '');
    $tanggal_datang = trim($_POST['tanggal_datang'] ?? '');
    $siapa_order = trim($_POST['siapa_order'] ?? '');

    // Gabungkan awalan ID dengan ID barang yang diinput, tanpa spasi/tab agar tidak membuat ID ganda tersembunyi
    $id_barang_full = $id_awalan . $id_barang;

    if ($satuan_barang == 'Hasbel') {
        $stok = (float)($_POST['stok'] ?? 0) * 1000; // Konversi 1 Hasbel = 1000 Meter
    } else {
        $stok = $_POST['stok'] ?? 0; // Satuan lain tetap
    }

    if ($id_awalan === '' || $id_barang === '') {
        $flash_type = 'danger';
        $flash_message = 'ID barang wajib diisi.';
    } else {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM items WHERE id_barang = ?");
        $cek->execute([$id_barang_full]);

        if ((int)$cek->fetchColumn() > 0) {
            $flash_type = 'danger';
            $flash_message = "ID Barang {$id_barang_full} sudah ada. Silakan cek data yang sudah diinput atau gunakan ID lain.";
        } else {
            try {
                // Query untuk menambah barang
                $stmt = $pdo->prepare("INSERT INTO items (id_barang, nama_barang, tipe_barang, stok, satuan_barang, nama_toko, ekspedisi, belanja_via, tanggal_order, tanggal_datang, siapa_order, qc_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$id_barang_full, $nama_barang, $tipe_barang, $stok, $satuan_barang, $nama_toko, $ekspedisi, $belanja_via, $tanggal_order, $tanggal_datang, $siapa_order, $qc_status])) {
                    $flash_type = 'success';
                    $flash_message = 'Barang berhasil ditambahkan dan sedang menunggu QC!';
                } else {
                    $flash_type = 'danger';
                    $flash_message = 'Error: Barang gagal ditambahkan.';
                }
            } catch (PDOException $e) {
                $flash_type = 'danger';
                if ($e->getCode() === '23000') {
                    $flash_message = "ID Barang {$id_barang_full} sudah ada. Data tidak disimpan ulang.";
                } else {
                    $flash_message = 'Terjadi error server saat menyimpan barang. Silakan cek error log.';
                }
            }
        }
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

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filter pencarian
$search_id = $_GET['search_id'] ?? '';
$search_name = $_GET['search_name'] ?? '';
$search_type = $_GET['search_type'] ?? '';
$search_date = $_GET['search_date'] ?? '';

// Query data dengan filter dan pagination (prepared agar aman)
$where = [];
$params = [];
if ($search_id) {
    $where[] = "id_barang LIKE :search_id";
    $params[':search_id'] = "%$search_id%";
}
if ($search_name) {
    $where[] = "nama_barang LIKE :search_name";
    $params[':search_name'] = "%$search_name%";
}
if ($search_type) {
    $where[] = "tipe_barang LIKE :search_type";
    $params[':search_type'] = "%$search_type%";
}
if ($search_date) {
    $where[] = "(tanggal_order = :search_date OR tanggal_datang = :search_date)";
    $params[':search_date'] = $search_date;
}
$whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';

$stmt = $pdo->prepare("SELECT * FROM items $whereSql ORDER BY tanggal_order DESC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();

$total_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM items $whereSql");
foreach ($params as $key => $value) {
    $total_stmt->bindValue($key, $value);
}
$total_stmt->execute();
$total_result = $total_stmt->fetch();
$total_items = $total_result['total'];
$total_pages = ceil($total_items / $limit);

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    ob_start();
    if ($items) {
        foreach ($items as $item) {
            $id = htmlspecialchars($item['id_barang'], ENT_QUOTES, 'UTF-8');
            echo '<tr>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['id_barang']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['nama_barang']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['tipe_barang']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['stok']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['satuan_barang']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['nama_toko']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['ekspedisi']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['belanja_via']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['siapa_order']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['tanggal_order']) . '</td>';
            echo '<td class="py-2 px-4 border-b">' . htmlspecialchars($item['tanggal_datang']) . '</td>';
            echo '<td class="py-2 px-4 border-b space-x-2">';
            echo '<a href="tambah_barang.php?delete=' . $id . '" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a> ';
            echo '<a href="edit_barang.php?id=' . $id . '" class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a> ';
            echo '<button class="text-blue-600 hover:text-blue-800" onclick="printLabel(\'' . $id . '\')"><i class="fas fa-print"></i></button> ';
            echo '<a href="../quality_control/qc_dashboard.php?id=' . $id . '" class="text-green-600 hover:text-green-800"><i class="fas fa-check"></i></a>';
            echo '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="12" class="text-center py-4 text-gray-500">Tidak ada data ditemukan</td></tr>';
    }
    $html = ob_get_clean();
    echo json_encode(['ok' => true, 'html' => $html, 'total' => (int)$total_items, 'page' => (int)$page, 'totalPages' => (int)$total_pages]);
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
    @media print {
        /* Sembunyikan elemen lain saat print */
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }

        /* Posisi QR Code kecil di pojok kiri atas */
        #print-area {
            position: fixed;
            top: 0.5cm; /* Jarak dari atas */
            left: 0.5cm; /* Jarak dari kiri */
            text-align: left; /* Rata kiri */
        }

        /* Ukuran QR Code kecil */
        #print-id-barang {
            width: 1.2cm;
            height: 1.2cm;
        }

        /* Teks ID Barang kecil di bawah QR Code */
        #text-id-barang {
            font-size: 3pt;
            font-weight: bold;
            margin: 0;
            padding-top: 0.2cm; /* Sedikit jarak dari QR Code */
        }

        /* Hindari margin default cetak */
        @page {
            margin: 0;
        }
    }
</style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Reciving Panel -->
    <aside class="w-64 bg-gray-800 text-white h-screen fixed">
        <div class="p-4 font-bold text-lg">Receiving</div>
        <nav>
            <a href="dhasboar_barang_belanja.php" class="block p-3 hover:bg-gray-700"><i class="fas fa-home mr-2"></i> Back Receiving</a>
         <!--   <a href="tambah_barang.php" class="block p-3 bg-gray-700"><i class="fas fa-plus-circle mr-2"></i> Tambah Barang</a>
            <a href="#" class="block p-3 hover:bg-gray-700"><i class="fas fa-check-circle mr-2"></i> QC Lolos</a>
            <a href="#" class="block p-3 hover:bg-gray-700"><i class="fas fa-times-circle mr-2"></i> QC Tidak Lolos</a>
            <a href="#" class="block p-3 hover:bg-gray-700"><i class="fas fa-box mr-2"></i> Barang Siap ke Gudang</a>
            <a href="#" class="block p-3 hover:bg-gray-700"><i class="fas fa-tasks mr-2"></i> Dashboard QC</a> -->
            <a href="#" class="block p-3 hover:bg-red-500"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
        </nav>
    </aside>

<body class="bg-gray-100">

<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="/index.php" class="text-white text-lg font-semibold"></a>
        <div>
            <a href="/index.php" class="text-white px-3 py-2 rounded-md text-sm font-medium">Kembali ke Dashboard</a>
        </div>
    </div>
</nav>
<div class="ml-64 p-8">
<h2 class="text-center my-4 text-2xl font-bold">Tambah Barang</h2>
<div class="container mx-auto p-4 bg-white shadow-md rounded">
    <?php if ($flash_message): ?>
        <div class="mb-4 rounded-lg border px-4 py-3 <?php echo $flash_type === 'danger' ? 'border-red-200 bg-red-50 text-red-700' : 'border-green-200 bg-green-50 text-green-700'; ?>">
            <?php echo htmlspecialchars($flash_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <form action="tambah_barang.php" method="post" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_awalan" class="block text-sm font-medium text-gray-700">ID</label>
                <select name="id_awalan" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM awalan_id_barang");
                    while ($row = $stmt->fetch()) {
                        echo "<option value=\"" . $row['kode_awalan'] . "\">" . $row['kode_awalan'] . " - " . $row['deskripsi_awalan'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="id_barang" class="block text-sm font-medium text-gray-700">ID Barang:</label>
                <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="id_barang" placeholder="Masukkan ID Barang" required>
            </div>
        </div>

        <div>
            <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang:</label>
            <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="nama_barang" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="tipe_barang" class="block text-sm font-medium text-gray-700">Type:</label>
                <select name="tipe_barang" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM tipe_barang");
                    while ($row = $stmt->fetch()) {
                        echo "<option value=\"" . $row['nama_tipe'] . "\">" . $row['nama_tipe'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="stok" class="block text-sm font-medium text-gray-700">Stok:</label>
                <input type="number" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="stok" required>
            </div>
        </div>

        <div>
            <label for="satuan_barang" class="block text-sm font-medium text-gray-700">Satuan Barang:</label>
            <select name="satuan_barang" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                <?php
                $stmt = $pdo->query("SELECT * FROM satuan_barang");
                while ($row = $stmt->fetch()) {
                    echo "<option value=\"" . $row['nama_satuan'] . "\">" . $row['nama_satuan'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nama_toko" class="block text-sm font-medium text-gray-700">Nama Toko:</label>
                <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="nama_toko" required>
            </div>
            <div>
                <label for="ekspedisi" class="block text-sm font-medium text-gray-700">Ekspedisi:</label>
                <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="ekspedisi" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="belanja_via" class="block text-sm font-medium text-gray-700">Belanja Via:</label>
                <select name="belanja_via" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <option value="Offline">Offline</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            <div>
                <label for="siapa_order" class="block text-sm font-medium text-gray-700">Petugas Order:</label>
                <input type="text" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="siapa_order" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="tanggal_order" class="block text-sm font-medium text-gray-700">Tanggal Order:</label>
                <input type="date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="tanggal_order" required>
            </div>
            <div>
                <label for="tanggal_datang" class="block text-sm font-medium text-gray-700">Tanggal Barang Datang:</label>
                <input type="date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="tanggal_datang" required>
            </div>
        </div>

        <input type="hidden" name="qc_status" value="Menunggu QC">

        <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Tambah Barang</button>
    </form>
</div>

<h2 class="text-center my-4 text-2xl font-bold">
    Data yang Sudah Di Input
</h2>
<p class="text-center text-sm italic text-gray-600">
    Mohon Di Cek Kembali Pastikan Data Sudah Sesuai
</p>
<div id="tambah-barang-counter" class="text-center text-sm text-gray-500 mb-4">Menampilkan <?php echo (int)$total_items; ?> data barang</div>
<div class="container mx-auto p-4 bg-white shadow-md rounded">

    <!-- Form Pencarian -->
    <form method="GET" class="mb-4 dsg-ajax-search" data-target="#tambah-barang-body" data-counter="#tambah-barang-counter">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search_id" placeholder="Cari ID Barang" value="<?php echo htmlspecialchars($search_id); ?>" class="p-2 border rounded">
            <input type="text" name="search_name" placeholder="Cari Nama Barang" value="<?php echo htmlspecialchars($search_name); ?>" class="p-2 border rounded">
            <input type="text" name="search_type" placeholder="Cari Tipe Barang" value="<?php echo htmlspecialchars($search_type); ?>" class="p-2 border rounded">
            <input type="date" name="search_date" value="<?php echo htmlspecialchars($search_date); ?>" class="p-2 border rounded">

        </div>
        <button type="submit" class="mt-4 w-full py-2 px-4 bg-green-600 text-white font-semibold rounded-md">Cari</button>
         <!-- Tambahkan Tombol Kelola Awalan ID dan Tambah Satuan -->
    <div class="flex justify-end space-x-4 mb-4">
        <a href="/belanja/kelola_awalan_id.php" 
           class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
           Kelola Awalan ID
        </a>
        <a href="/belanja/tambah_satuan.php" 
           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
           Tambah Satuan Barang
        </a>
        </a>
        <a href="/belanja/manage_tipe_barang.php" 
           class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
           Tambah Type Barang
        </a>
    </div>

    </form>

    <!-- Tabel Data Barang -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-4 border-b">ID Barang</th>
                    <th class="py-2 px-4 border-b">Nama Barang</th>
                    <th class="py-2 px-4 border-b">Type</th>
                    <th class="py-2 px-4 border-b">Stok</th>
                    <th class="py-2 px-4 border-b">Satuan</th>
                    <th class="py-2 px-4 border-b">Nama Toko</th>
                    <th class="py-2 px-4 border-b">Ekspedisi</th>
                    <th class="py-2 px-4 border-b">Belanja Via</th>
                    <th class="py-2 px-4 border-b">Petugas Order</th>
                    <th class="py-2 px-4 border-b">Tanggal Order</th>
                    <th class="py-2 px-4 border-b">Tanggal Datang</th>
                    <th class="py-2 px-4 border-b">Aksi</th>
                </tr>
            </thead>
		<tbody id="tambah-barang-body">
    <?php if ($items): ?>
        <?php foreach ($items as $item): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['id_barang']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['tipe_barang']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['stok']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['satuan_barang']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['nama_toko']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['ekspedisi']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['belanja_via']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['siapa_order']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['tanggal_order']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['tanggal_datang']); ?></td>
                <td class="py-2 px-4 border-b space-x-2">
                    <a href="tambah_barang.php?delete=<?php echo $item['id_barang']; ?>" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </a>
                    <a href="edit_barang.php?id=<?php echo $item['id_barang']; ?>" class="text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="text-blue-600 hover:text-blue-800" onclick="printLabel('<?php echo $item['id_barang']; ?>')">
                        <i class="fas fa-print"></i>
                    </button>
                    <a href="../quality_control/qc_dashboard.php?id=<?php echo $item['id_barang']; ?>" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-check"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="12" class="text-center py-4 text-gray-500">Tidak ada data ditemukan</td>
        </tr>
    <?php endif; ?>
</tbody>
</table>
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center">
    <nav class="flex">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query($_GET); ?>" class="px-3 py-2 mx-1 bg-gray-200 hover:bg-gray-300 rounded">Prev</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>" class="px-3 py-2 mx-1 <?php echo ($i == $page) ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?> rounded"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query($_GET); ?>" class="px-3 py-2 mx-1 bg-gray-200 hover:bg-gray-300 rounded">Next</a>
        <?php endif; ?>
    </nav>
</div>

<script>
function printLabel(id_barang) {
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${id_barang}&size=200x200`;

    const qrImage = document.getElementById('print-id-barang');
    qrImage.src = qrCodeUrl;

    document.getElementById('text-id-barang').innerText = id_barang;

    document.getElementById('print-area').classList.remove('hidden');

    window.print();

    setTimeout(function () {
        document.getElementById('print-area').classList.add('hidden');
    }, 1000);
}
</script>
<!-- Area untuk Print Label -->
<div id="print-area">
    <img id="print-id-barang" src="" alt="QR Code">
    <p id="text-id-barang"></p>
</div>

    </div>
</div>

<script>
function printLabel(id_barang) {
    // URL QR Code Generator
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${id_barang}&size=200x200`;

    // Mengatur QR Code ke img
    const qrImage = document.getElementById('print-id-barang');
    qrImage.src = qrCodeUrl;

    // Menambahkan teks ID Barang
    document.getElementById('text-id-barang').innerText = id_barang;

    // Menampilkan area print
    document.getElementById('print-area').classList.remove('hidden');

    // Mencetak halaman
    window.print();

    // Sembunyikan area print setelah cetak selesai
    setTimeout(function () {
        document.getElementById('print-area').classList.add('hidden');
    }, 1000);
}
</script>

<script src="/assets/js/dsg-modern.js"></script>
<script src="/assets/js/dsg-ajax-search.js"></script>
</body>
</html>
