<?php
require 'EmploiDuTemps.php';

$dsn = 'mysql:host=localhost;dbname=gestion_emploi_du_temps';
$dsn1 = 'mysql:host=localhost;dbname=etudiants';
$username = 'root';
$password = 'Keyce-2024';

try {
    $db = new PDO($dsn, $username, $password);
    $db1 = new PDO($dsn1, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $emploiDuTemps = new EmploiDuTemps($db, $db1); // Passez les deux objets PDO

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
        $horaires = ['matin', 'soir'];

        foreach ($jours as $jour) {
            foreach ($horaires as $horaire) {
                $cours = $_POST[$jour . '_' . $horaire];
                if (!empty($cours)) {
                    $emploiDuTemps->ajouterCours($jour, $horaire, $cours,strtoupper($_GET['niveau']));
                }
            }
        }

        header('Location: index.php?niveau=' . htmlspecialchars($_GET['niveau']));
        exit;
    }
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>
