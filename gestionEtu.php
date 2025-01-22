<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';

$database = new Database();
$db = $database->getConnection();
$etudiant = new Etudiant($db);

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$etudiants = $etudiant->getAllEtudiants($search);

// Suppression
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($etudiant->supprimerEtudiant($id) && $etudiant->supprimerConnexion($id)) {
        $message = "L'étudiant a été supprimé avec succès.";
        header("Location: gestionEtu.php");
        exit();
    } else {
        $message = "Une erreur s'est produite lors de la suppression.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
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
            position: relative;
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

        .popup-actions .close {
            background: #2196F3;
            color: #fff;
        }

        .popup-actions .close:hover {
            background: #1976D2;
        }

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
            padding: 8px;
            border: 1px solid #2c3e50;
            text-align: left;
            color: wheat;
        }

        th {
            background-color: #2c3e50;
            color: white;
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
    </style>
</head>

<body>

    <?php if (!empty($message)): ?>
        <div class="popup-overlay" id="popup">
            <div class="popup-content">
                <h3>Message</h3>
                <p><?= htmlspecialchars($message); ?></p>
                <div class="popup-actions">
                    <button class="close" onclick="closePopup()">Fermer</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fas fa-bars"></i> Menu</h2>
        <a class="etu" href="gestionEtu.php"><i class="fas fa-user-graduate"></i> Étudiants</a>
        <a href="gestionVer.php"><i class="fas fa-money-bill-wave"></i> Versements</a>
        <a href="gestionEnsei.php"><i class="fas fa-chalkboard-teacher"></i> Enseignant</a>
        <a href="emploi.php"><i class="fas fa-chalkboard-teacher"></i> Emploie</a>
        <a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="gestionMati.php"><i class="fas fa-book"></i> Matieres</a>
        <a href="rien1.php"><i class="fas fa-file-alt"></i> Notes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <div class="haut">
                <h2><i class="fas fa-list"></i> Liste des étudiants</h2>
            </div>
            <form method="GET" action="gestionEtu.php">
                <input name="search" type="text" placeholder="Rechercher..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Rechercher</button>
            </form>
        </div>

        <?php if (!empty($message)): ?>
            <p style="color: green;"> <?= htmlspecialchars($message); ?> </p>
        <?php endif; ?>

        <div class="table-container">
            <a href="AjouterEtu.php"><button class="ajout"><i class="fas fa-plus"></i> Ajouter un étudiant</button></a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Matricule</th>
                        <th>Photo</th>
                        <th>Email</th>
                        <th>Niveau</th>
                        <th>Âge</th>
                        <th>Montant à Payer</th>
                        <th>Nom Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($etudiants)): ?>
                        <?php foreach ($etudiants as $etudiant): ?>
                            <tr>
                                <td><?= htmlspecialchars($etudiant['id']); ?></td>
                                <td><?= htmlspecialchars($etudiant['nom']); ?></td>
                                <td><?= htmlspecialchars($etudiant['prenom']); ?></td>
                                <td><?= htmlspecialchars($etudiant['matricule']); ?></td>
                                <td>
                                    <?php if (!empty($etudiant['photo'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($etudiant['photo']); ?>" alt="Photo">
                                    <?php else: ?>
                                        <span>Aucune photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($etudiant['email']); ?></td>
                                <td><?= htmlspecialchars($etudiant['niveau']); ?></td>
                                <td><?= htmlspecialchars($etudiant['age']); ?></td>
                                <td><?= htmlspecialchars($etudiant['montant']); ?></td>
                                <td><?= htmlspecialchars($etudiant['nom_parent']); ?></td>
                                <td class="actions">
                                    <button class="edit" onclick="location.href='edit.php?id=<?= $etudiant['id']; ?>'"><i class="fas fa-edit"></i></button>
                                    <button class="delete" onclick="openPopup(<?= $etudiant['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    <button class="details" onclick="location.href='informationEtudiant.php?matricule=<?= $etudiant['matricule']; ?>'"><i class="fas fa-info-circle"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12">Aucun étudiant trouvé.</td>
                        </tr>
                    <?php endif; ?>
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
