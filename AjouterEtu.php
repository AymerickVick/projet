<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';
require_once 'Class/B1.php';
require_once 'Class/B2.php';
require_once 'Class/B3.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Data validation and sanitization
        $nom = htmlspecialchars(trim($_POST['nom']));
        $prenom = htmlspecialchars(trim($_POST['prenom']));
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ?: '';
        $matricule = htmlspecialchars(trim($_POST['matricule']));
        $niveau = htmlspecialchars(trim($_POST['niveau']));
        $montant = filter_var($_POST['montant'], FILTER_VALIDATE_INT);
        $nom_parent = htmlspecialchars(trim($_POST['nom_parent']));
        $email_parent = filter_var($_POST['email_parent'], FILTER_VALIDATE_EMAIL) ?: '';
        $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
        $montant_paye = 0;
        $reste = filter_var($_POST['reste'], FILTER_VALIDATE_INT);
        $statut = "Insolvable";
        $date_naissance = htmlspecialchars(trim($_POST['date_naissance']));

        if (!$email || !$email_parent) {
            throw new Exception("Email invalide.");
        }

        // Photo upload handling
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $photo_name = basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            throw new Exception("Erreur lors de l'upload de la photo.");
        }

        // Student creation
        $etudiant = new Etudiant($db);
        $b1 = new B1($db);
        $b2 = new B2($db);
        $b3 = new B3($db);

        $b1->matricule = $matricule;
        $b1->nom = $nom;
        $b1->prenom = $prenom;

        $b2->matricule = $matricule;
        $b2->nom = $nom;
        $b2->prenom = $prenom;

        $b3->matricule = $matricule;
        $b3->nom = $nom;
        $b3->prenom = $prenom;

        $etudiant->nom = $nom;
        $etudiant->prenom = $prenom;
        $etudiant->matricule = $matricule;
        $etudiant->photo = $photo_name;
        $etudiant->email = $email;
        $etudiant->niveau = $niveau;
        $etudiant->montant = $montant;
        $etudiant->nom_parent = $nom_parent;
        $etudiant->email_parent = $email_parent;
        $etudiant->age = $age;
        $etudiant->montant_paye = $montant_paye;
        $etudiant->reste = $reste;
        $etudiant->statut = $statut;
        $etudiant->date_naissance = $date_naissance;

        if ($niveau === "B1") {
            $success = $b1->ajouterEtudiant() && $b1->ajouterEtudiantCC() && $b1->ajouterEtudiantTp();
        } elseif ($niveau === "B2") {
            $success = $b2->ajouterEtudiant() && $b2->ajouterEtudiantCC() && $b2->ajouterEtudiantTp();
        } elseif ($niveau === "B3") {
            $success = $b3->ajouterEtudiant() && $b3->ajouterEtudiantCC() && $b3->ajouterEtudiantTp();
        }

        // Add student and send matriculation number by email
        $success = $etudiant->ajouterEtudiant() && $etudiant->ajouterEtudiantCon("Etudiant", $matricule, $matricule);
        if ($success) {
            $repond = $etudiant->envoyerMatriculeParEmail($matricule, $email, $nom);
            $message = $repond
                ? "Étudiant ajouté et email envoyé avec succès."
                : "Étudiant ajouté, mais erreur lors de l'envoi de l'email.";
        } else {
            throw new Exception("Erreur lors de l'ajout de l'étudiant.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['niveau'])) {
    try {
        $niveau = htmlspecialchars($_GET['niveau']);
        $database = new Database();
        $db = $database->getConnection();

        // Get the number of students in the level
        $query = "SELECT COUNT(*) AS count FROM etudiants WHERE niveau = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $niveau);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'] + 1;

        // Generate matriculation number
        $currentYear = date("Y");
        $matricule = $currentYear . $niveau . str_pad($count, 3, "0", STR_PAD_LEFT);

        echo htmlspecialchars($matricule);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo "Erreur : " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un étudiant</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --background-color: #ecf0f1;
            --text-color: #34495e;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--secondary-color);
        }

        .form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        input,
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            padding: 12px 20px;
            font-size: 16px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .form-actions a button {
            background-color: var(--accent-color);
        }

        .form-actions a button:hover {
            background: #c0392b;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
        }

        .popup-content h3 {
            margin: 0 0 20px;
            color: var(--secondary-color);
        }

        .popup-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            background: var(--primary-color);
            color: #fff;
            transition: all 0.3s ease;
        }

        .popup-actions button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            .form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> Ajouter un étudiant</h1>
        <form action="AjouterEtu.php" method="POST" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label for="nom"><i class="fas fa-user"></i> Nom:</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom"><i class="fas fa-user"></i> Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="matricule"><i class="fas fa-id-card"></i> Matricule:</label>
                <input type="text" id="matricule" name="matricule" readonly required>
            </div>
            <div class="form-group">
                <label for="photo"><i class="fas fa-camera"></i> Photo:</label>
                <input type="file" id="photo" name="photo" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="niveau"><i class="fas fa-graduation-cap"></i> Niveau:</label>
                <select id="niveau" name="niveau" required onchange="generateMatricule()">
                    <option value="">-- Sélectionnez le niveau --</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="B3">B3</option>
                </select>
            </div>
            <div class="form-group">
                <label for="date_naissance"><i class="fas fa-birthday-cake"></i> Date de Naissance:</label>
                <input type="date" id="date_naissance" name="date_naissance" max="2011-12-31" required>
            </div>
            <div class="form-group">
                <label for="montant"><i class="fas fa-money-bill-wave"></i> Montant à payer:</label>
                <input type="number" id="montant" name="montant" readonly required>
            </div>
            <div class="form-group">
                <label for="reste"><i class="fas fa-money-bill-wave"></i> Reste à payer:</label>
                <input type="number" id="reste" name="reste" readonly required>
            </div>
            <div class="form-group">
                <label for="nom_parent"><i class="fas fa-user-friends"></i> Nom du parent:</label>
                <input type="text" id="nom_parent" name="nom_parent" required>
            </div>
            <div class="form-group">
                <label for="email_parent"><i class="fas fa-envelope"></i> Email du parent:</label>
                <input type="email" id="email_parent" name="email_parent" required>
            </div>
            <div class="form-group">
                <label for="age"><i class="fas fa-birthday-cake"></i> Âge:</label>
                <input type="number" id="age" name="age" required>
            </div>
            <div class="form-actions">
                <button type="submit"><i class="fas fa-save"></i> Ajouter l'étudiant</button>
                <a href="gestionEtu.php"><button type="button"><i class="fas fa-times"></i> Annuler</button></a>
            </div>
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div id="popup" class="popup-overlay">
            <div class="popup-content">
                <h3><?= htmlspecialchars($message); ?></h3>
                <div class="popup-actions">
                    <button onclick="closePopup()"><i class="fas fa-times"></i> Fermer</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function generateMatricule() {
            const niveau = document.getElementById('niveau').value;
            if (niveau) {
                fetch(`AjouterEtu.php?niveau=${niveau}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('matricule').value = data;
                    })
                    .catch(error => console.error('Erreur:', error));

                // Update amount based on level
                const montantInput = document.getElementById('montant');
                const resteInput = document.getElementById('reste');
                if (niveau === 'B1') {
                    montantInput.value = 1000000;
                    resteInput.value = 1000000;
                } else if (niveau === 'B2') {
                    montantInput.value = 2000000;
                    resteInput.value = 2000000;
                } else if (niveau === 'B3') {
                    montantInput.value = 3000000;
                    resteInput.value = 3000000;
                }
            }
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>

</html>