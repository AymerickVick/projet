<?php
require_once 'Class/Database.php';
require 'fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection(); // Assurez-vous que la classe Database retourne un objet mysqli

$niveau = strval($_GET['niveau']);
$matricule = strval($_GET['matricule']);
// Classes disponibles
$classes = [$niveau];
$notes = [];

try {
    foreach ($classes as $classe) {
        $table_notes = "exam_".strtolower($classe);
        $table_tp = "tp_" . strtolower($classe);
        $table_cc =  "note_".strtolower($classe);
    
        $query_notes = "SELECT * FROM $table_notes";
        $result_notes = $db->query($query_notes);
        if ($result_notes) {
            $notes[$classe]['notes'] = $result_notes->fetch_all(MYSQLI_ASSOC);
        }
    
        $query_tp = "SELECT * FROM $table_tp";
        $result_tp = $db->query($query_tp);
        if ($result_tp) {
            $notes[$classe]['tp'] = $result_tp->fetch_all(MYSQLI_ASSOC);
        }
    
        $query_exam = "SELECT * FROM $table_cc";
        $result_exam = $db->query($query_exam);
        if ($result_exam) {
            $notes[$classe]['cc'] = $result_exam->fetch_all(MYSQLI_ASSOC);
        }
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

// Fonction pour récupérer les notes d'un étudiant
function getStudentNotes($conn, $matricule, $classe)
{
    $table = strtolower($classe);
    $query = "SELECT * FROM `$table` WHERE matricule = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param('s', $matricule);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Fonction pour calculer la moyenne générale
function calculateAverage($notes)
{
    $sum = 0;
    $count = 0;
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule_etudiant', 'nom', 'prenom'])) {
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
    <!-- Lien vers Bootstrap pour la mise en page responsive -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Times New Roman', sans-serif;
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        }

        .sidebar {
            background: linear-gradient(#fff, #74ebd5, #fff);
            color: black;
            height: 93vh;
            position: fixed;
            width: 185px;
            padding: 20px;
            border-radius: 10px;
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar h2 {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.8em;
        }

        .sidebar a {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .sidebar .etu {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        }

        .main-content {
            margin-left: 170px;
            margin-top: 20px;
            padding: 20px;
            width: calc(100% - 170px);
            text-align: center;
        }

        .table-card {
            margin-bottom: 30px;
        }

        .table-card h2 {
            color: #4a4a4a;
        }

        .send-notes-button {
            background: linear-gradient(#fff, #74ebd5, #fff), no-repeat;
            color: black;
            border: none;
            padding: 10px 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-notes-button:hover {
            background: #357abd;
            color: white;
            border-radius: 5px;
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
            background: linear-gradient(#fff, #74ebd5, #fff);
            color: black;
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
        <h2><i class="fas fa-bars"></i> Menu</h2>
        <?php if (!empty($matricule)): ?>
            <a href="informationEtudiant.php?matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-user"></i> Mes Informations</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a href="MatiereEtu.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-book"></i> Mes Matieres</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a class="etu" href="NoteEtudiant.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-graduation-cap"></i> Mes Notes</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a href="rien.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-robot"></i> KEYCE AI</a>
        <?php endif; ?>
        <a href="index.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <main class="main-content">
        <?php foreach ($classes as $classe): ?>
            <?php if (isset($notes[$classe]) && !empty($notes[$classe])): ?>
                <div class="table-card">
                    <div class="table-container">
                        <h2>Vos différentes Notes de SN</h2>
                        <button class="send-notes-button" onclick="window.location.href='envoyer_notes.php?classe=<?php echo urlencode($classe); ?>'"><i class="fas fa-paper-plane"></i> Envoyer les notes</button>
                        <table>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <?php foreach (array_keys($notes[$classe]['notes'][0]) as $column): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($notes[$classe]['notes'] as $student): ?>
                                <tr>
                                    <?php if (!empty($matricule)): ?>
                                        <?php if ($student['matricule'] === $matricule): ?>
                                            <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                            <?php foreach ($student as $column => $value): ?>
                                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                                    <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <br>
                        <br>

                        <h2>Vos différentes Notes de CC </h2>
                        
                        <table>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <?php foreach (array_keys($notes[$classe]['cc'][0]) as $column): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($notes[$classe]['cc'] as $student): ?>
                                <tr>
                                    <?php if (!empty($matricule)): ?>
                                        <?php if ($student['matricule'] === $matricule): ?>
                                            <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                            <?php foreach ($student as $column => $value): ?>
                                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                                    <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                        <br>
                        <br>


                        <h2>Vos différentes Notes de TP</h2>
                        
                        <table>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <?php foreach (array_keys($notes[$classe]['tp'][0]) as $column): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($notes[$classe]['tp'] as $student): ?>
                                <tr>
                                    <?php if (!empty($matricule)): ?>
                                        <?php if ($student['matricule'] === $matricule): ?>
                                            <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                            <?php foreach ($student as $column => $value): ?>
                                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                                    <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
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
