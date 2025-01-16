<?php
require '../vendor/autoload.php'; // Assurez-vous que le chemin vers autoload.php est correct

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;

function generateSignature($fileContent) {
    return hash('sha256', $fileContent);
}

function generateQRCode($data, $savePath) {
    $qrCode = new QrCode($data);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    $result->saveToFile($savePath);
    return $savePath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileContent = file_get_contents($file['tmp_name']);
    $signature = generateSignature($fileContent);

    // Chemin pour enregistrer le QR code dans le dossier Documents
    $savePath = getenv('USERPROFILE') . '\Documents\qrcode.png';
    $qrCodePath = generateQRCode($signature, $savePath);

    echo json_encode(['signature' => $signature, 'qrCodePath' => $qrCodePath]);
} else {
    echo json_encode(['error' => 'Aucun fichier téléchargé']);
}
?>
