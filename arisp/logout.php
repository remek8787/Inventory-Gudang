<?php
session_start(); // Memulai session

// Mengakhiri session
session_unset(); // Menghapus semua data session
session_destroy(); // Menghancurkan session

// Mengarahkan pengguna ke halaman login setelah logout
header("Location: login.php");
exit();
?>
