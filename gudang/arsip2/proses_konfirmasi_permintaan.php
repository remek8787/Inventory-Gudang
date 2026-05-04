<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../belanja/db.php'; // Pastikan path ini benar

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_permintaan'], $_POST['action'])) {
    $id_permintaan = $_POST['id_permintaan'];
    $action = $_POST['action'];

    $status = $action === 'confirm' ? "Disetujui" : "Ditolak";

    try {
        // Update status di tabel status_permintaan
        $query = "UPDATE status_permintaan SET status = :status WHERE id_permintaan = :id_permintaan";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['status' => $status, 'id_permintaan' => $id_permintaan]);

        header("Location: list_konfirmasi_permintaan.php?status=success");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Data POST tidak lengkap atau metode bukan POST";
}
?>
