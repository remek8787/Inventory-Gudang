<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator — DSG Inventory</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="dsg-app">
<aside class="dsg-pro-sidebar">
    <div class="brand">📦 DSG Inventory</div>
    <a href="/index.php">Dashboard Utama</a>
    <a href="/belanja/tambah_barang.php">Receiving</a>
    <a href="/quality_control/qc_dashboard.php">Quality Control</a>
    <a href="/gudang/barang_masuk_gudang.php">Gudang</a>
    <a href="/qr-code-ganrate/" class="active">QR Generator</a>
</aside>
<main class="dsg-pro-main">
    <div class="dsg-page-head">
        <div>
            <h1>QR Code Generator</h1>
            <p class="dsg-page-subtitle">Generate PDF QR label dari awalan kode barang. Cocok untuk label inventory dan print massal.</p>
        </div>
        <span class="dsg-badge-soft">v1.0 QR Tool</span>
    </div>
    <section class="dsg-panel">
        <form method="POST" action="generate_qr.php">
            <div class="dsg-form-grid">
                <div>
                    <label>Awalan Kode</label>
                    <input type="text" name="prefix" class="form-control" placeholder="Contoh: DSA / ONU / ROUTER" maxlength="12" pattern="[A-Za-z0-9_-]+" required>
                    <small class="text-muted">Huruf/angka tanpa spasi. Contoh: DSA, ONU, ROUTER.</small>
                </div>
                <div>
                    <label>Jumlah QR</label>
                    <input type="number" name="count" class="form-control" placeholder="Contoh: 50" min="1" max="500" required>
                    <small class="text-muted">Maksimal 500 agar server tetap ringan.</small>
                </div>
            </div>
            <div class="dsg-actions mt-4">
                <button type="submit" class="btn btn-primary">Generate PDF QR</button>
                <a href="/index.php" class="btn btn-light">Kembali Dashboard</a>
            </div>
        </form>
    </section>
    <div class="dsg-shell-note"><strong>Catatan:</strong> hasil QR berupa PDF siap download. Kode dibuat unik dari awalan + angka acak.</div>
</main>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
