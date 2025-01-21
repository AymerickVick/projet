<?php
// error.php
$message = isset($_GET['message']) ? urldecode($_GET['message']) : 'Une erreur inconnue est survenue.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #d9534f;
        }
        p {
            color: #555;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Erreur</h1>
        <p><?= htmlspecialchars($message) ?></p>
    </div>
</body>
</html>
