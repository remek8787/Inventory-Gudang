<?php
require('fpdf.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method tidak valid.'); }
$prefix = strtoupper(trim($_POST['prefix'] ?? ''));
$count = intval($_POST['count'] ?? 0);
if (!preg_match('/^[A-Z0-9_-]{1,12}$/', $prefix)) { die('Awalan kode hanya boleh huruf, angka, underscore, atau strip. Maksimal 12 karakter.'); }
if ($count < 1 || $count > 500) { die('Jumlah QR harus 1 sampai 500.'); }
function generateQrData($prefix, $count) {
    $data = []; $existingCodes = [];
    while (count($data) < $count) {
        $randomNumber = random_int(1000, 999999);
        $code = $prefix . $randomNumber;
        if (!isset($existingCodes[$code])) { $data[] = [$code, $code]; $existingCodes[$code] = true; }
    }
    return $data;
}
$data = generateQrData($prefix, $count);
$tempDir = __DIR__ . '/temp/'; $outDir = __DIR__ . '/generated/';
if (!is_dir($tempDir)) { mkdir($tempDir, 0755, true); }
if (!is_dir($outDir)) { mkdir($outDir, 0755, true); }
foreach ($data as $row) {
    $text = $row[1]; $filename = $tempDir . $text . '.png';
    $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?data=' . rawurlencode($text) . '&size=120x120';
    $png = @file_get_contents($apiUrl);
    if ($png === false) { array_map('unlink', glob($tempDir . '*.png')); @rmdir($tempDir); die('Gagal mengambil QR dari API eksternal. Coba lagi.'); }
    file_put_contents($filename, $png);
}
$pdf = new FPDF('P', 'mm', 'A4'); $pdf->AddPage(); $pdf->SetFont('Arial', '', 4);
$x = 5; $y = 10; $qrSize = 12; $cellHeight = 16; $columns = 14; $counter = 0;
foreach ($data as $row) {
    $text = $row[1]; $filename = $tempDir . $text . '.png';
    $pdf->Image($filename, $x, $y, $qrSize, $qrSize);
    $pdf->SetXY($x, $y + $qrSize + 1); $pdf->Cell($qrSize, 3, $text, 0, 0, 'C');
    $x += ($qrSize + 2); $counter++;
    if ($counter == $columns) { $counter = 0; $x = 5; $y += $cellHeight; }
    if ($y + $cellHeight > 280) { $pdf->AddPage(); $x = 5; $y = 10; }
}
$uniqueFileName = 'QR_Codes_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.pdf';
$outputFile = $outDir . $uniqueFileName;
$pdf->Output('F', $outputFile);
array_map('unlink', glob($tempDir . '*.png')); @rmdir($tempDir);
$url = 'generated/' . rawurlencode($uniqueFileName);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>QR Berhasil Dibuat</title><link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/dsg-modern.css" rel="stylesheet"></head><body class="dsg-app"><main class="dsg-pro-main mx-auto" style="max-width:760px"><div class="dsg-panel text-center mt-5"><h1 class="page-title">QR PDF Berhasil Dibuat</h1><p class="text-muted">Total QR: <?= htmlspecialchars((string)$count) ?> | Prefix: <?= htmlspecialchars($prefix) ?></p><a class="btn btn-primary" href="<?= htmlspecialchars($url) ?>" download>Download PDF QR</a><a class="btn btn-light ml-2" href="index.php">Generate Lagi</a><a class="btn btn-success ml-2" href="/index.php">Dashboard</a></div></main><script src="/assets/js/dsg-modern.js"></script></body></html>
