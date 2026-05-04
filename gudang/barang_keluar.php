<?php
require '../belanja/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = trim($_POST['id_barang'] ?? '');
    $nama_teknisi = trim($_POST['nama_teknisi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $penggunaan = trim($_POST['penggunaan'] ?? '');
    $petugas_admin = trim($_POST['petugas_admin'] ?? '');

    if ($id_barang === '' || $nama_teknisi === '' || $penggunaan === '' || $petugas_admin === '') {
        $error = 'ID barang, teknisi, penggunaan, dan petugas admin wajib diisi.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM barang_masuk_gudang WHERE id_barang = ? LIMIT 1");
            $stmt->execute([$id_barang]);
            $barang = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$barang) {
                throw new Exception('Barang tidak ditemukan di gudang.');
            }
            if ((int)$barang['stok'] <= 0) {
                throw new Exception('Stok barang sudah kosong.');
            }

            $cekKeluar = $pdo->prepare("SELECT COUNT(*) FROM barang_keluar WHERE id_barang = ?");
            $cekKeluar->execute([$id_barang]);
            if ((int)$cekKeluar->fetchColumn() > 0) {
                throw new Exception('Barang ini sudah pernah dikeluarkan.');
            }

            $insert = $pdo->prepare("INSERT INTO barang_keluar (id_barang, nama_teknisi, keterangan, penggunaan, petugas_admin, nama_barang, tipe_barang, mac_address, stok, satuan, tanggal_keluar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insert->execute([
                $id_barang,
                $nama_teknisi,
                $keterangan,
                $penggunaan,
                $petugas_admin,
                $barang['nama_barang'] ?? '',
                $barang['tipe_barang'] ?? '',
                $barang['mac_address'] ?? '',
                1,
                $barang['satuan_barang'] ?? ''
            ]);

            $update = $pdo->prepare("UPDATE barang_masuk_gudang SET stok = stok - 1 WHERE id_barang = ? AND stok > 0");
            $update->execute([$id_barang]);

            $pdo->commit();
            $message = 'Barang berhasil dikeluarkan untuk teknisi.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $error = $e->getMessage();
        }
    }
}

$search = trim($_GET['search'] ?? '');
$where = "WHERE stok > 0";
$params = [];
if ($search !== '') {
    $where .= " AND (id_barang LIKE :search OR nama_barang LIKE :search OR tipe_barang LIKE :search OR mac_address LIKE :search OR ekspedisi LIKE :search)";
    $params[':search'] = "%$search%";
}
$stmt = $pdo->prepare("SELECT id_barang, nama_barang, tipe_barang, mac_address, stok, satuan_barang, ekspedisi FROM barang_masuk_gudang $where ORDER BY tanggal_qc DESC, tanggal_datang DESC, id_barang ASC LIMIT 80");
foreach ($params as $key => $value) { $stmt->bindValue($key, $value); }
$stmt->execute();
$available = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    ob_start();
    if ($available) {
        foreach ($available as $row) {
            $id = htmlspecialchars($row['id_barang'], ENT_QUOTES, 'UTF-8');
            echo '<tr>';
            echo '<td><button type="button" class="btn btn-sm btn-primary" onclick="selectBarang(\'' . $id . '\')">Pilih</button></td>';
            echo '<td>' . htmlspecialchars($row['id_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tipe_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($row['mac_address']) . '</td>';
            echo '<td>' . htmlspecialchars($row['stok']) . '</td>';
            echo '<td>' . htmlspecialchars($row['satuan_barang']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ekspedisi']) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada barang tersedia.</td></tr>';
    }
    echo json_encode(['ok'=>true,'html'=>ob_get_clean(),'total'=>count($available)]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluarkan Barang</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="dsg-app">
<aside class="dsg-pro-sidebar">
    <div class="brand">📦 DSG Inventory</div>
    <a href="/index.php">Dashboard Utama</a>
    <a href="/gudang/barang_masuk_gudang.php">Barang Masuk Gudang</a>
    <a href="/gudang/stok_gudang.php">Stok Gudang</a>
    <a href="/gudang/barang_keluar.php" class="active">Ship Out</a>
    <a href="/gudang/riwayat_pengeluaran.php">Riwayat Pengeluaran</a>
    <a href="/quality_control/list_selesai_qc.php">Selesai QC</a>
</aside>
<main class="dsg-pro-main">
    <div class="dsg-page-head">
        <div>
            <h1>Keluarkan Barang</h1>
            <p class="dsg-page-subtitle">Pilih barang tersedia dari gudang, isi teknisi dan penggunaan. Alur DB lama tetap dipertahankan.</p>
        </div>
        <span class="dsg-badge-soft">Stok tersedia: <span id="barang-keluar-counter"><?= count($available) ?></span></span>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <section class="dsg-panel">
        <form method="POST" action="barang_keluar.php">
            <div class="dsg-form-grid">
                <div><label>ID Barang</label><input type="text" id="id_barang" name="id_barang" class="form-control" placeholder="Klik Pilih dari tabel / input manual" required></div>
                <div><label>Nama Teknisi</label><input type="text" name="nama_teknisi" class="form-control" required></div>
                <div><label>Penggunaan</label><input type="text" name="penggunaan" class="form-control" placeholder="Contoh: pemasangan pelanggan / perbaikan" required></div>
                <div><label>Petugas Admin</label><input type="text" name="petugas_admin" class="form-control" required></div>
            </div>
            <div class="mt-3"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan"></textarea></div>
            <div class="dsg-actions mt-3"><button type="submit" class="btn btn-primary">Keluarkan Barang</button><a href="/gudang/riwayat_pengeluaran.php" class="btn btn-light">Lihat Riwayat</a></div>
        </form>
    </section>

    <section class="dsg-panel">
        <form method="GET" class="dsg-ajax-search" data-target="#barang-keluar-available-body" data-counter="#barang-keluar-counter">
            <div class="dsg-form-grid">
                <input type="text" name="search" class="form-control" placeholder="Cari ID / nama / tipe / MAC / ekspedisi" value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit" class="btn btn-success mt-3">Cari Barang Tersedia</button>
        </form>
    </section>

    <div class="dsg-table-card">
        <table class="table table-bordered table-striped mb-0">
            <thead><tr><th>Aksi</th><th>ID Barang</th><th>Nama</th><th>Tipe</th><th>MAC</th><th>Stok</th><th>Satuan</th><th>Ekspedisi</th></tr></thead>
            <tbody id="barang-keluar-available-body">
            <?php if ($available): foreach ($available as $row): ?>
                <tr>
                    <td><button type="button" class="btn btn-sm btn-primary" onclick="selectBarang('<?= htmlspecialchars($row['id_barang'], ENT_QUOTES, 'UTF-8') ?>')">Pilih</button></td>
                    <td><?= htmlspecialchars($row['id_barang']) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($row['tipe_barang']) ?></td>
                    <td><?= htmlspecialchars($row['mac_address']) ?></td>
                    <td><?= htmlspecialchars($row['stok']) ?></td>
                    <td><?= htmlspecialchars($row['satuan_barang']) ?></td>
                    <td><?= htmlspecialchars($row['ekspedisi']) ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada barang tersedia.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script>function selectBarang(id){document.getElementById('id_barang').value=id;window.scrollTo({top:0,behavior:'smooth'});}</script>
<script src="/assets/js/dsg-modern.js"></script>
<script src="/assets/js/dsg-ajax-search.js"></script>
</body>
</html>
