<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
</head>
<body>
    <div class="welcome-container">
        <h2>SELAMAT DATANG SILAHKAN MENUNGGU!</h2>
        <p>You can manage users here.</p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
