<?php
require 'db.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function normalize_tipe($value) {
    return trim(preg_replace('/\s+/', ' ', (string)$value));
}

function tipe_exists(PDO $pdo, $nama_tipe, $exclude_id = null) {
    $sql = "SELECT COUNT(*) FROM tipe_barang WHERE LOWER(TRIM(nama_tipe)) = LOWER(TRIM(?))";
    $params = [$nama_tipe];
    if ($exclude_id !== null) {
        $sql .= " AND id <> ?";
        $params[] = $exclude_id;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn() > 0;
}

function fetch_tipe(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, nama_tipe FROM tipe_barang ORDER BY nama_tipe ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $nama_tipe = normalize_tipe($_POST['nama_tipe'] ?? '');
        if ($nama_tipe === '') {
            $error = 'Nama tipe barang wajib diisi.';
        } elseif (tipe_exists($pdo, $nama_tipe)) {
            $error = "Tipe barang '{$nama_tipe}' sudah ada. Data double tidak disimpan.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO tipe_barang (nama_tipe) VALUES (?)");
            $stmt->execute([$nama_tipe]);
            $message = "Tipe barang '{$nama_tipe}' berhasil ditambahkan.";
        }
    }

    if ($action === 'edit') {
        $id_tipe = (int)($_POST['id_tipe'] ?? 0);
        $nama_tipe = normalize_tipe($_POST['nama_tipe'] ?? '');
        if ($id_tipe <= 0 || $nama_tipe === '') {
            $error = 'Pilih tipe dan isi nama tipe baru.';
        } elseif (tipe_exists($pdo, $nama_tipe, $id_tipe)) {
            $error = "Tipe barang '{$nama_tipe}' sudah ada. Edit dibatalkan agar tidak double.";
        } else {
            $stmt = $pdo->prepare("UPDATE tipe_barang SET nama_tipe = ? WHERE id = ?");
            $stmt->execute([$nama_tipe, $id_tipe]);
            $message = "Tipe barang berhasil diperbarui menjadi '{$nama_tipe}'.";
        }
    }

    if ($action === 'delete') {
        $id_tipe = (int)($_POST['id_tipe'] ?? 0);
        if ($id_tipe <= 0) {
            $error = 'Pilih tipe barang yang akan dihapus.';
        } else {
            $stmt = $pdo->prepare("DELETE FROM tipe_barang WHERE id = ?");
            $stmt->execute([$id_tipe]);
            $message = 'Tipe barang berhasil dihapus.';
        }
    }
}

$tipeList = fetch_tipe($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tipe Barang - DSG Inventory</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css?v=20260505-typefix" rel="stylesheet">
    <style>
        .type-shell{max-width:1180px;margin:36px auto;padding:0 16px}.type-hero{background:linear-gradient(135deg,#0f172a,#2563eb);color:#fff;border-radius:24px;padding:28px;box-shadow:0 20px 50px rgba(15,23,42,.22)}.type-card{background:#fff;border:1px solid #e2e8f0;border-radius:22px;box-shadow:0 18px 45px rgba(15,23,42,.10);padding:22px}.type-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;color:#64748b;background:#f8fafc}.form-control{border-radius:12px}.btn{border-radius:12px;font-weight:700}@media(max-width:900px){.type-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="type-shell">
    <div class="type-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <p class="mb-2 text-uppercase" style="letter-spacing:.12em;opacity:.8;font-weight:800">DSG Inventory</p>
                <h1 class="mb-2" style="font-weight:900">Kelola Tipe Barang</h1>
                <p class="mb-0" style="opacity:.85">Tambah, edit, dan hapus tipe barang. Sistem otomatis menolak nama tipe yang double.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="/belanja/tambah_barang.php" class="btn btn-light">← Kembali ke Barang</a>
            </div>
        </div>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= h($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>

    <div class="type-grid mb-4">
        <div class="type-card">
            <h4 class="font-weight-bold mb-3">Tambah Tipe</h4>
            <form method="post" autocomplete="off">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Nama Tipe Barang</label>
                    <input type="text" class="form-control" name="nama_tipe" placeholder="Contoh: Kabel FO, Modem, Router" required>
                    <small class="form-text text-muted">Nama yang sama tidak akan disimpan dua kali.</small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Tambah Tipe</button>
            </form>
        </div>

        <div class="type-card">
            <h4 class="font-weight-bold mb-3">Edit Tipe</h4>
            <form method="post" autocomplete="off">
                <input type="hidden" name="action" value="edit">
                <div class="form-group">
                    <label>Pilih Tipe</label>
                    <select name="id_tipe" class="form-control" required>
                        <option value="">Pilih tipe barang</option>
                        <?php foreach ($tipeList as $row): ?>
                            <option value="<?= h($row['id']) ?>"><?= h($row['nama_tipe']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Tipe Baru</label>
                    <input type="text" class="form-control" name="nama_tipe" required>
                </div>
                <button type="submit" class="btn btn-warning btn-block">Simpan Edit</button>
            </form>
        </div>
    </div>

    <div class="type-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold mb-0">Daftar Tipe Barang</h4>
            <span class="badge badge-primary p-2"><?= count($tipeList) ?> tipe</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th style="width:80px">ID</th><th>Nama Tipe</th><th style="width:180px" class="text-right">Aksi</th></tr></thead>
                <tbody>
                    <?php if (!$tipeList): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada tipe barang.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($tipeList as $row): ?>
                        <tr>
                            <td><?= h($row['id']) ?></td>
                            <td><strong><?= h($row['nama_tipe']) ?></strong></td>
                            <td class="text-right">
                                <form method="post" class="d-inline" onsubmit="return confirm('Hapus tipe barang ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_tipe" value="<?= h($row['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="/assets/js/dsg-modern.js?v=20260505-typefix"></script>
</body>
</html>
