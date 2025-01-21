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
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f4f4f9;
        }

        .sidebar {
            background-color: #475be8;
            color: white;
            width: 160px;
            padding: 20px 10px;
            position: fixed;
            top: 0;
            bottom: 0;
            height: 100vh;
            border-radius: 10px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #6c83f7;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar .etu {
            background-color: #6c83f7;
        }

        .main-content {
            margin-left: 180px;
            margin-top: 20px;
            padding: 20px;
            width: calc(100% - 180px);
            text-align: center;
        }

        .table-card {
            margin-bottom: 30px;
        }

        .table-card h2 {
            color: #4a4a4a;
        }

        .send-notes-button {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-notes-button:hover {
            background-color: #357abd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #66a6ff;
            color: #fff;
        }

        .table-container {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="gestionEtu.php"><img src="uploads/student.gif" alt="">Étudiants</a>
        <a href="gestionVer.php"><img src="uploads/alms.gif" alt="">Versements</a>
        <a href="gestionEnsei.php"><img src="uploads/graduated (1).png" alt="">Enseignant</a>
        <a href="statistiques.php"><img src="uploads/analytics.gif" alt="">Statistiques</a>
        <a href="gestionMati.php"><img src="uploads/analytics.gif" alt="">Matieres</a>
        <a class="etu" href="rien1.php"><img src="uploads/graduated (1).png" alt="">Notes</a>
        <a href="logout.php">Déconnexion</a>
    </div>
    <main class="main-content">

        <?php foreach ($classes as $classe): ?>
            <?php if (isset($notes[$classe]) && !empty($notes[$classe])): ?>
                <div class="table-card">
                    <div class="table-container">
                        <h2>Notes des étudiants de la classe <?php echo htmlspecialchars($classe); ?></h2>
                        <button class="send-notes-button" onclick="window.location.href='envoyer.php?classe=<?php echo urlencode($classe); ?>'">Envoyer les notes</button>
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
                    </div>
                </div>
            <?php else: ?>
                <p>Aucune note disponible pour la classe <?php echo htmlspecialchars($classe); ?>.</p>
            <?php endif; ?>
        <?php endforeach; ?>

    </main>
</body>

</html>