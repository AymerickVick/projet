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
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Styles de la sidebar */
        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
            width: 200px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            border-radius: 0 10px 10px 0;
        }

        .sidebar h2 {
            margin: 0 0 30px 0;
            font-size: 1.5em;
            text-align: center;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 8px 0;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar a i {
            font-size: 1.2rem;
            margin-right: 12px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar a:hover {
            background-color: #6c83f7;
            transform: translateX(5px);
        }

        .sidebar .etu {
            background-color: #6c83f7;
        }

        .logout-btn {
            margin-top: auto;
            color: #ff6b6b;
        }

        .logout-btn:hover {
            background-color: rgba(255, 107, 107, 0.1);
        }

        /* Styles du contenu principal */
        .content {
            margin-left: 220px;
            padding: 20px;
            background-color: #343a40;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .header {
            background-color: #495057;
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin-left: 10px;
            color: white;
        }

        .header input {
            width: 250px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .header input::placeholder {
            color: #aaa;
        }

        .header button {
            background-color: #007bff;
            color: white;
            margin: 5px;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .header button:hover {
            background-color: #0056b3;
        }

        .table-container {
            margin-top: 20px;
            background-color: #495057;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #2c3e50;
            text-align: left;
            color: wheat;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #495057;
        }

        tr:hover {
            background-color: #6c83f7;
            transition: background-color 0.3s ease;
        }

        .actions button {
            margin: 4px;
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
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

        .actions button:hover {
            opacity: 0.8;
            transform: scale(1.05);
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
            text-decoration: none;
        }

        .ajout:hover {
            background-color: #0056b3;
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

        /* Styles pour l'image */
        img {
            width: 1cm; /* Diamètre de 4 cm */
            height: 1cm; /* Diamètre de 4 cm */
            border-radius: 50%; /* Rendre l'image ronde */
            object-fit: cover; /* Assure que l'image couvre toute la zone sans déformation */
            display: block; /* Centrer l'image */
            margin: auto; /* Centrer l'image */
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
        <h2><i class='bx bxs-dashboard'></i> Menu</h2>
        <a class="etu" href="gestionEtu.php"><i class='bx bxs-user-detail'></i> Étudiants</a>
        <a href="gestionVer.php"><i class='bx bx-money'></i> Versements</a>
        <a href="gestionEnsei.php"><i class='bx bxs-user-badge'></i> Enseignants</a>
        <a href="statistiques.php"><i class='bx bx-bar-chart-alt-2'></i> Statistiques</a>
        <a href="gestionMati.php"><i class='bx bxs-book'></i> Matières</a>
        <a href="emploi.php"><i class='bx bxs-calendar'></i> Emploi du temps</a>
        <a href="rien1.php"><i class='bx bxs-notepad'></i> Notes</a>
        <a href="logout.php" class="logout-btn"><i class='bx bx-log-out'></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <div class="haut">
                <h2><i class='bx bxs-user-detail'></i> Liste des étudiants</h2>
            </div>
            <form method="GET" action="gestionEtu.php">
                <input name="search" type="text" placeholder="Rechercher..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit"><i class='bx bx-search'></i> Rechercher</button>
            </form>
        </div>

        <?php if (!empty($message)): ?>
            <p style="color: green;"> <?= htmlspecialchars($message); ?> </p>
        <?php endif; ?>

        <div class="table-container">
            <a href="AjouterEtu.php"><button class="ajout"><i class='bx bx-plus'></i> Ajouter un étudiant</button></a>
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
                                    <button class="edit" onclick="location.href='edit.php?id=<?= $etudiant['id']; ?>'"><i class='bx bx-edit'></i></button>
                                    <button class="delete" onclick="openPopup(<?= $etudiant['id']; ?>)"><i class='bx bx-trash'></i></button>
                                    <button class="details" onclick="location.href='informationEtudiant.php?matricule=<?= $etudiant['matricule']; ?>'"><i class='bx bx-info-circle'></i></button>
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