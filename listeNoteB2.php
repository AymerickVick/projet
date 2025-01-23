<?php
require_once 'Class/Database.php';
require 'fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Récupérer les notes pour chaque classe
$classes = ['B2'];
$notes = [];

foreach ($classes as $classe) {
    $table_notes = "exam_".strtolower($classe);
    $table_tp = "tp_" . strtolower($classe);
    $table_cc =  "note_".strtolower($classe);

    $query_notes = "SELECT * FROM $table_notes"." ORDER BY nom ASC, prenom ASC";
    $result_notes = $db->query($query_notes);
    if ($result_notes) {
        $notes[$classe]['notes'] = $result_notes->fetch_all(MYSQLI_ASSOC);
    }

    $query_tp = "SELECT * FROM $table_tp"." ORDER BY nom ASC, prenom ASC";
    $result_tp = $db->query($query_tp);
    if ($result_tp) {
        $notes[$classe]['tp'] = $result_tp->fetch_all(MYSQLI_ASSOC);
    }

    $query_exam = "SELECT * FROM $table_cc"." ORDER BY nom ASC, prenom ASC";
    $result_exam = $db->query($query_exam);
    if ($result_exam) {
        $notes[$classe]['cc'] = $result_exam->fetch_all(MYSQLI_ASSOC);
    }
}

// Fonction pour récupérer les notes d'un étudiant
function getStudentNotes($conn, $matricule, $classe)
{
    $table_notes = "note_" . strtolower($classe);
    $table_tp = "tp_" . strtolower($classe);
    $table_exam = "exam_" . strtolower($classe);

    $notes = null;
    $tp = null;
    $exam = null;

    $query_notes = "SELECT * FROM $table_notes WHERE matricule_etudiant = ?";
    $stmt_notes = $conn->prepare($query_notes);
    $stmt_notes->bind_param('s', $matricule);
    if ($stmt_notes->execute()) {
        $result_notes = $stmt_notes->get_result();
        $notes = $result_notes->fetch_assoc();
    }

    $query_tp = "SELECT * FROM $table_tp WHERE matricule_etudiant = ?";
    $stmt_tp = $conn->prepare($query_tp);
    $stmt_tp->bind_param('s', $matricule);
    if ($stmt_tp->execute()) {
        $result_tp = $stmt_tp->get_result();
        $tp = $result_tp->fetch_assoc();
    }

    $query_exam = "SELECT * FROM $table_exam WHERE matricule_etudiant = ?";
    $stmt_exam = $conn->prepare($query_exam);
    $stmt_exam->bind_param('s', $matricule);
    if ($stmt_exam->execute()) {
        $result_exam = $stmt_exam->get_result();
        $exam = $result_exam->fetch_assoc();
    }

    return [
        'notes' => $notes,
        'tp' => $tp,
        'exam' => $exam
    ];
}

// Fonction pour calculer la moyenne générale
function calculateAverage($notes)
{
    $sum = 0;
    $count = 0;
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
            $sum += $value;
            $count++;
        }
    }
    return $count > 0 ? $sum / $count : 0;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Notes</title>
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
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class='bx bxs-dashboard'></i> Menu</h2>
        <a href="gestionEtu.php"><i class='bx bxs-user-detail'></i> Étudiants</a>
        <a href="gestionVer.php"><i class='bx bx-money'></i> Versements</a>
        <a href="gestionEnsei.php"><i class='bx bxs-user-badge'></i> Enseignants</a>
        <a href="statistiques.php"><i class='bx bx-bar-chart-alt-2'></i> Statistiques</a>
        <a href="gestionMati.php"><i class='bx bxs-book'></i> Matières</a>
        <a class="etu" href="rien1.php"><i class='bx bxs-notepad'></i> Notes</a>
        <a href="index.php" class="logout-btn"><i class='bx bx-log-out'></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php foreach ($classes as $classe): ?>
            <?php if (isset($notes[$classe]) && !empty($notes[$classe])): ?>
                <div class="table-container">
                    <h2>Notes des étudiants de la classe <?php echo htmlspecialchars($classe); ?></h2>
                    <button class="ajout" onclick="window.location.href='envoyer_note.php?classe=<?php echo urlencode($classe); ?>'">Envoyer les notes</button>
                    <table>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <?php foreach (array_keys($notes[$classe]['notes'][0]) as $column): ?>
                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                    <th><?php echo htmlspecialchars(ucwords($column)); ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($notes[$classe]['notes'] as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                <?php foreach ($student as $column => $value): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <h2>Notes de CC des étudiants de la classe <?php echo htmlspecialchars($classe); ?></h2>
                    <table>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <?php foreach (array_keys($notes[$classe]['cc'][0]) as $column): ?>
                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                    <th><?php echo htmlspecialchars(ucwords($column)); ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($notes[$classe]['cc'] as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                <?php foreach ($student as $column => $value): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <h2>Notes de TP des étudiants de la classe <?php echo htmlspecialchars($classe); ?></h2>
                    <table>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <?php foreach (array_keys($notes[$classe]['tp'][0]) as $column): ?>
                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                    <th><?php echo htmlspecialchars(ucwords($column)); ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($notes[$classe]['tp'] as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                <?php foreach ($student as $column => $value): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucune note disponible pour la classe <?php echo htmlspecialchars($classe); ?>.</p>
            <?php endif; ?>
        <?php endforeach; ?>
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