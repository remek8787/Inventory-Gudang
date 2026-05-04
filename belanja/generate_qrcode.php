<?php
require_once '../php-qrcode-main/src/QrCode.php';
require_once '../php-qrcode-main/src/Output/PngOutput.php';
require_once '../php-qrcode-main/src/Output/OutputInterface.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (isset($_GET['data'])) {
    $data = $_GET['data']; // Data untuk QR Code

    // Konfigurasi QR Code
    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_L,
        'scale' => 5,
    ]);

    // Generate QR Code
    $qrcode = new QRCode($options);

    // Set header agar menghasilkan gambar PNG
    header('Content-Type: image/png');
    echo $qrcode->render($data);
}
