<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading — DSG Inventory</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <script type="text/javascript">
        // Fungsi untuk redirect setelah 5 detik
        setTimeout(function(){
            window.location.href = "index.php"; // Ganti dengan halaman tujuan
        }, 5000); // 5000 milidetik = 5 detik
    </script>

    <style>
        .welcome-container {
            text-align: center;
            margin-top: 150px;
        }
    </style>
<link href="/assets/css/dsg-modern.css" rel="stylesheet">
</head>
<body>
    <div class="welcome-container dsg-loading-card">
        <div class="dsg-loading-spinner"></div>
        <h2>DSG Inventory sedang menyiapkan dashboard</h2>
        <p>v1.2.6-modern · Build 2026.05.05.0235 — mohon tunggu sebentar, Anda akan diarahkan otomatis.</p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
<script src="/assets/js/dsg-modern.js"></script>
</body>
</html>
