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

    $emploiDuTemps = new EmploiDuTemps($db,$db1);
    $cours = $emploiDuTemps->obtenirCours(strtoupper($_GET['niveau']));

    $coursParJour = [
        'lundi_matin' => '',
        'mardi_matin' => '',
        'mercredi_matin' => '',
        'jeudi_matin' => '',
        'vendredi_matin' => '',
        'samedi_matin' => '',
        'lundi_soir' => '',
        'mardi_soir' => '',
        'mercredi_soir' => '',
        'jeudi_soir' => '',
        'vendredi_soir' => '',
        'samedi_soir' => ''
    ];

    foreach ($cours as $c) {
        $coursParJour[$c['jour'] . '_' . $c['horaire']] = $c['cours'];
    }

    $niveau = $_GET['niveau'];

    // Récupérer les matières de niveau B1
    $stmt = $db1->prepare("SELECT nom_matiere FROM matieres WHERE niveau_matiere = 'B3'");
    $stmt->execute();
    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Emplois du Temps</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: black;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            background-color: #343a40;
            color: white;
            height: 93vh;
            position: fixed;
            width: 175px;
            padding: 20px;
            border-radius: 10px;
        }

        .sidebar h2 {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #495057;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar .etu {
            background-color: #495057;
        }

        /* Content */
        .content {
            margin-left: 220px;
            margin-right: 20px;
            padding: 20px;
            background-color: #343a40;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #495057;
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
        }

        .header h2:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .header h2 {
            margin-left: 10px;
            justify-content: left;
            color: white;
        }

        .header input {
            width: 300px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .header input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .header input::placeholder {
            color: #aaa;
        }

        .header button {
            background-color: #007bff;
            color: white;
            margin: 5px;
            padding: 9px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .header button:hover {
            background-color: #0056b3;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #495057;
            border-radius: 10px;
        }

        .table-container {
            margin-top: 20px;
            background-color: #495057;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 24px;
            /* Augmenter le padding pour rendre les lignes plus grandes */
            border: 1px solid #2c3e50;
            text-align: left;
            color: wheat;
        }

        th {
            background-color: #2c3e50;
            color: white;
            height: 72px;
            /* Augmenter la hauteur des en-têtes pour correspondre aux lignes */
        }

        tr:nth-child(even) {
            background-color: #495057;
        }

        tr:hover {
            background-color: #495057;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        img {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 50px;
            width: 100%;
        }

        .rien {
            max-width: 25px;
            max-height: 25px;
            justify-content: center;
        }

        .actions button {
            margin: .2px;
            padding: 3px 6px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .actions button:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .actions .edit {
            background-color: #007bff;
            color: white;
        }

        .actions .details {
            background-color: #28a745;
            color: white;
        }

        .actions .delete {
            background-color: #dc3545;
            color: white;
        }

        .table-container a {
            text-decoration: none;
        }

        .ajout {
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            margin: 10px 0px;
            padding: 9px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            justify-content: center;
            position: relative;
            text-decoration: none;
        }

        .ajout:hover {
            background-color: #0056b3;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .table-container .hover .ajout:hover {
            background-color: #0056b3;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
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
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
            max-width: 400px;
            width: 90%;
        }

        .popup-content h3 {
            margin: 0 0 10px;
        }

        .popup-content p {
            margin: 0 0 20px;
        }

        .popup-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px;
        }

        .popup-actions .cancel {
            background: #dc3545;
            color: #fff;
        }

        .popup-actions .confirm {
            background: #28a745;
            color: #fff;
        }

        .popup-actions .cancel:hover {
            background: #c82333;
        }

        .popup-actions .confirm:hover {
            background: #218838;
        }

        .haut {
            padding: 10px;
        }

        .haut h2 {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        /* Style for TPE cells */
        .tpe {
            background-color: green;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fas fa-bars"></i> Menu</h2>
        <a class="etu" href="gestionEtu.php"><i class="fas fa-user-graduate"></i> Étudiants</a>
        <a href="gestionVer.php"><i class="fas fa-money-bill-wave"></i> Versements</a>
        <a href="gestionEnsei.php"><i class="fas fa-chalkboard-teacher"></i> Enseignant</a>
        <a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="gestionMati.php"><i class="fas fa-book"></i> Matieres</a>
        <a href="rien1.php"><i class="fas fa-file-alt"></i> Notes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <div class="haut">
                <h2><i class="fas fa-list"></i> Gestion des Emplois du Temps</h2>
            </div>
        </div>

        <div class="table-container">
            <a href="ajouterEMP.php?niveau=<?php echo htmlspecialchars($_GET['niveau']); ?>">
                <button class="ajout">
                    <i class="fas fa-plus"></i> Ajouter un cours
                </button>
            </a>

            <table>
                <thead>
                    <tr>
                        <th>Horaire</th>
                        <th>Lundi</th>
                        <th>Mardi</th>
                        <th>Mercredi</th>
                        <th>Jeudi</th>
                        <th>Vendredi</th>
                        <th>Samedi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>08 h 30 - 12 h 30</td>
                        <td class="<?php echo $coursParJour['lundi_matin'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['lundi_matin']; ?></td>
                        <td class="<?php echo $coursParJour['mardi_matin'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['mardi_matin']; ?></td>
                        <td class="<?php echo $coursParJour['mercredi_matin'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['mercredi_matin']; ?></td>
                        <td class="<?php echo $coursParJour['jeudi_matin'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['jeudi_matin']; ?></td>
                        <td class="<?php echo $coursParJour['vendredi_matin'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['vendredi_matin']; ?></td>
                        <td class="<?php echo $coursParJour['samedi_matin'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['samedi_matin']; ?></td>
                    </tr>
                    <tr>
                        <td>13 h 30 - 17 h 30</td>
                        <td class="<?php echo $coursParJour['lundi_soir'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['lundi_soir']; ?></td>
                        <td class="<?php echo $coursParJour['mardi_soir'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['mardi_soir']; ?></td>
                        <td class="<?php echo $coursParJour['mercredi_soir'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['mercredi_soir']; ?></td>
                        <td class="<?php echo $coursParJour['jeudi_soir'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['jeudi_soir']; ?></td>
                        <td class="<?php echo $coursParJour['vendredi_soir'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['vendredi_soir']; ?></td>
                        <td class="<?php echo $coursParJour['samedi_soir'] === 'TPE' ? 'tpe' : ''; ?>"><?php echo $coursParJour['samedi_soir']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popup -->
    <div id="popup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <h3>Confirmation</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet étudiant ?</p>
            <div class="popup-actions">
                <button class="cancel" onclick="closePopup()">Annuler</button>
                <button class="confirm" id="confirm-delete">Confirmer</button>
            </div>
        </div>
    </div>

    <script>
        function openPopup(deleteId) {
            const popup = document.getElementById('popup');
            popup.style.display = 'flex';
            const confirmButton = document.getElementById('confirm-delete');
            confirmButton.onclick = function() {
                location.href = '?delete_id=' + deleteId;
            };
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>

</html>