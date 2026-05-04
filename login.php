<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body class="dsg-login-body">
    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="dsg-login-card"><div class="dsg-brand-badge">📦 DSG Inventory</div><div class="dsg-build-badge">v1.2.5-modern · Build 2026.05.04.2304</div><h3 class="mb-2">Masuk Admin</h3><p class="text-muted mb-4">Kelola barang, QC, dan gudang dalam satu panel.</p><form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form><p class="text-muted small mt-4 mb-0">Tips: pastikan username dan password sesuai role akses.</p></div>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
