<?php
require_once 'vendor/autoload.php'; // Assurez-vous d'avoir installé PDFParser via Composer

use Smalot\PdfParser\Parser;

function extractTextFromPdf($filePath)
{
    if (!file_exists($filePath)) {
        return "Fichier non trouvé.";
    }

    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    $text = $pdf->getText();

    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    // Vérification du fichier uploadé
    $uploadDir = "uploads/";
    $fileName = basename($_FILES['pdfFile']['name']);
    $filePath = $uploadDir . $fileName;

    // Création du dossier uploads s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Vérification du type de fichier
    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($fileType !== 'pdf') {
        echo "Veuillez uploader un fichier PDF valide.";
    } else {
        // Déplacement du fichier uploadé
        if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $filePath)) {
            $response = gemini("Donne-moi un résumé de ce Cours.", $filePath);
            // echo "<pre>Réponse brute de l'API : " . htmlspecialchars($response) . "</pre>";
        } else {
            echo "Une erreur est survenue lors de l'upload du fichier.";
        }
    }
}

function gemini($message, $filePath = null)
{
    $GKey = "AIzaSyDWuYR-M8EzvTE3OIz1iNlyjugCxf7IVJ0";
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

    $fileContent = $filePath ? extractTextFromPdf($filePath) : null;

    $requestData = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $message]
                ]
            ]
        ]
    ]);

    if ($fileContent) {
        $requestData = json_encode([
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $message],
                        ['text' => $fileContent]
                    ]
                ]
            ]
        ]);
    }

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return "Erreur cURL : " . curl_error($ch);
    }

    curl_close($ch);

    $responseObject = json_decode($response, true);

    if (isset($responseObject['candidates'][0]['content']['parts'][0]['text'])) {
        return str_replace('*', ' ', $responseObject['candidates'][0]['content']['parts'][0]['text']);
    }

    return "Réponse non disponible ou mal formatée.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader un fichier PDF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        input[type="file"] {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Uploader un fichier PDF</h1>
        <form action="test.php" method="post" enctype="multipart/form-data">
            <label for="pdfFile">Choisissez un fichier PDF :</label>
            <input type="file" name="pdfFile" id="pdfFile" accept=".pdf" required>
            <button type="submit">Uploader</button>
        </form>
    </div>

    <?php if (!empty($response)): ?>
                <h3>
                    <i class="fas fa-check-circle text-success"></i> <?= htmlspecialchars($response); ?>
                </h3>
                <div class="popup-actions">
                    <button onclick="closePopup()">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                </div>    
    <?php endif; ?>
</body>

</html>
