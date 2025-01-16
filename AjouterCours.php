<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader un fichier PDF</title>

    <!-- Lien vers Bootstrap pour la mise en page responsive -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Lien vers Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Global styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        h1 {
            color: #5D5C61;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
            color: #555;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Styling du message de popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            width: 300px;
        }

        .popup .popup-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .popup.success {
            border-left: 5px solid #28a745;
        }

        .popup.error {
            border-left: 5px solid #dc3545;
        }

        .popup p {
            font-size: 16px;
            margin: 0;
        }

        /* Overlay (semi-transparent background) */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1><i class="fas fa-cloud-upload-alt"></i> Formulaire d'upload de fichier PDF</h1>

        <!-- Formulaire -->
        <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
            <!-- Label avec icône PDF -->
            <label for="pdfFile">
                <i class="fas fa-file-pdf"></i> Choisissez un fichier PDF :
            </label>
            <input type="file" name="pdfFile" id="pdfFile" accept="application/pdf" required>

            <!-- Bouton avec icône d'upload -->
            <button type="submit" name="submit">
                <i class="fas fa-upload"></i> Uploader
            </button>
        </form>
    </div>

    <!-- Popup pour afficher les messages de succès ou d'erreur -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popupMessage">
        <div id="popupIcon" class="popup-icon"></div>
        <p id="popupText"></p>
    </div>

    <?php
    $niveau = isset($_GET['niveau']) ? $_GET['niveau'] : null;
    $matiere = isset($_GET['matiere']) ? $_GET['matiere'] : null;

    // Configuration de la connexion à la base de données
    $host = 'localhost'; // Remplacez par l'adresse de votre serveur
    $dbname = 'etudiants'; // Remplacez par le nom de votre base
    $username = 'root'; // Remplacez par votre nom d'utilisateur
    $password = 'Keyce-2024'; // Remplacez par votre mot de passe

    // Connexion à la base de données avec MySQLi
    $conn = new mysqli($host, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
        $matiere = isset($_GET['matiere']) ? $_GET['matiere'] : null;
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['pdfFile']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = mime_content_type($_FILES['pdfFile']['tmp_name']);

        if ($fileType === 'application/pdf') {
            if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $targetFile)) {
                // Vérification si la matière existe déjà
                $stmt = $conn->prepare("SELECT id FROM matieres WHERE nom_matiere = ?");
                $stmt->bind_param("s", $matiere);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Mise à jour de la colonne cours
                    $row = $result->fetch_assoc();
                    $stmt = $conn->prepare("UPDATE matieres SET cours = ? WHERE id = ?");
                    $stmt->bind_param("si", $fileName, $row['id']);
                    $stmt->execute();
                    echo "<script>showPopup('success', 'Le fichier a été téléchargé et enregistré pour la matière existante : $matiere.');</script>";
                } else {
                    // Insertion d'une nouvelle matière
                    $stmt = $conn->prepare("INSERT INTO matieres (nom_matiere, cours) VALUES (?, ?)");
                    $stmt->bind_param("ss", $matiere, $fileName);
                    $stmt->execute();
                    echo "<script>showPopup('success', 'Le fichier a été téléchargé et une nouvelle matière a été créée : $matiere.');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>showPopup('error', 'Erreur lors du téléchargement du fichier.');</script>";
            }
        } else {
            echo "<script>showPopup('error', 'Le fichier téléchargé n\'est pas un PDF valide.');</script>";
        }
    }

    // Fermeture de la connexion
    $conn->close();
    ?>

    <script>
        // Fonction pour afficher le popup
        function showPopup(type, message) {
            const popup = document.getElementById('popupMessage');
            const popupIcon = document.getElementById('popupIcon');
            const popupText = document.getElementById('popupText');
            const overlay = document.getElementById('overlay');

            popup.style.display = 'block';
            overlay.style.display = 'block';

            // Définir l'icône et le message en fonction du type
            if (type === 'success') {
                popup.classList.add('success');
                popup.classList.remove('error');
                popupIcon.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i>';
            } else if (type === 'error') {
                popup.classList.add('error');
                popup.classList.remove('success');
                popupIcon.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i>';
            }

            popupText.textContent = message;
        }

        // Fermeture du popup lorsqu'on clique sur l'overlay
        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('popupMessage').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        });
    </script>

</body>

</html>
