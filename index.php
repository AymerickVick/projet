<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';

$database = new Database();
$db = $database->getConnection();
$etudiantObj = new Etudiant($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = trim($_POST['matricule'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Vérifier les informations de connexion pour un étudiant
    $etudiant = $etudiantObj->verifierConnexion($matricule, $password);
    if ($etudiant) {
        $sql = "SELECT statut FROM connexion WHERE matricule = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $stmt->bind_result($statut);
        $stmt->fetch();
        $stmt->close();
        $db->close();

        if ($statut) {
            if ($statut === 'Etudiant') {
                if ($matricule === $password) {
                    // Rediriger vers la page de modification du mot de passe
                    header("Location: etudiant/modifierPass.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                } else {
                    // Rediriger vers la page d'information de l'étudiant
                    header("Location: informationEtudiant.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                }
            } elseif ($statut === 'Administration') {
                header("Location: gestionEtu.php");
                exit();
            } elseif ($statut === 'Professeur') {
                if ($matricule === $password) {
                    // Rediriger vers la page de modification du mot de passe
                    header("Location: etudiant/modifierProf.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                } else {
                    // Rediriger vers la page d'information de l'étudiant
                    header("Location: infoProf.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                }
            }
        } else {
            $message = "Matricule ou mot de passe incorrect.";
        }
    } else {
        $message = "Matricule ou mot de passe incorrect.";
    }
}

// Fonction pour afficher un message en toute sécurité
function afficherMessage($message)
{
    return htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #ffffff;
            color: #333;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-container input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .login-container input:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .login-container button {
            padding: 12px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-container button:hover {
            background-color: #2575fc;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .login-container .error {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #e74c3c;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container .error img {
            margin-right: 10px;
        }

        @media (max-width: 500px) {
            .login-container {
                padding: 20px;
            }

            .login-container h2 {
                font-size: 20px;
            }

            .login-container button {
                font-size: 14px;
            }
        }

        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 15px 0;
        }

        select:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .error {
            display: flex;
            margin: 10px;
            padding: 10px;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2><strong>Connexion</strong></h2>
        <?php if (!empty($message)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <p class="error"><?= afficherMessage($message); ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="matricule" placeholder="Matricule" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>
