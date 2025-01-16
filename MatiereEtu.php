<?php
require_once 'Class/Database.php';
require_once 'Class/Matiere.php';
require_once 'vendor/autoload.php';

session_start();

// Récupération et validation des paramètres
$niveau = isset($_GET['niveau']) ? htmlspecialchars($_GET['niveau']) : (isset($_POST['niveau']) ? htmlspecialchars($_POST['niveau']) : null);
$matricule = isset($_GET['matricule']) ? htmlspecialchars($_GET['matricule']) : (isset($_POST['matricule']) ? htmlspecialchars($_POST['matricule']) : null);

// Si les paramètres ne sont pas présents, redirigez vers la page de connexion
if (!$niveau || !$matricule) {
    // Stockez un message d'erreur si nécessaire
    $_SESSION['error_message'] = "Paramètres manquants. Veuillez vous reconnecter.";
    header('Location: index.php');
    exit();
}

use Smalot\PdfParser\Parser;

$database = new Database();
$db = $database->getConnection();
$matiere = new Matiere($db);

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$matieres = $matiere->getAllMatiereParEtu($niveau);

// Suppression
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $matiere = $matiere->supprimerMatiere($id);
    if ($matiere) {
        $message = "L'étudiant a été supprimé avec succès.";
        header("Location: gestionMati.php");
        exit();
    } else {
        $message = "Une erreur s'est produite lors de la suppression.";
    }
}

function extractTextFromPdf($filePath)
{
    if (!file_exists($filePath)) {
        return "Fichier non trouvé.";
    }
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    return $pdf->getText();
}

function gemini($message, $filePath = null, $musicStyle = null)
{
    $GKey = "AIzaSyA7OteJoYw6GM-IM7bgRdT2nglSvo_HZn0";
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

    $fileContent = $filePath ? extractTextFromPdf($filePath) : null;

    if ($musicStyle) {
        $message = "Transforme ce contenu en une chanson style $musicStyle en français avec le format suivant:
        [Intro]
        [Verse 1]
        [Chorus]
        [Verse 2]
        [Chorus]
        [Outro]

        Assure-toi de garder les informations importantes du cours dans les paroles.

        Contenu à transformer: " . $fileContent;
    } elseif (strpos($message, 'quizz') !== false) {
        $message = "En te basant sur le contenu du fichier fourni, crée un quiz (de 10 questions) au format HTML suivant ce modèle exact:
        <div class='quiz-container'>
            <form id='quizForm'>
                <div class='quiz-question'>
                    <p>Question 1</p>
                    <div class='quiz-options'>
                        <label><input type='radio' name='q1' value='correct'> Bonne réponse</label>
                        <label><input type='radio' name='q1' value='incorrect'> Mauvaise réponse 1</label>
                        <label><input type='radio' name='q1' value='incorrect'> Mauvaise réponse 2</label>
                    </div>
                </div>
                <button type='submit' class='quiz-submit'>Valider les réponses</button>
            </form>
            <div class='score-display'></div>
        </div>";
    }

    $requestData = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $message]
                ]
            ]
        ]
    ];

    if ($fileContent) {
        $requestData['contents'][0]['parts'][] = ['text' => $fileContent];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return json_encode(['error' => "Erreur cURL : " . curl_error($ch)]);
    }

    curl_close($ch);
    $responseObject = json_decode($response, true);

    if (isset($responseObject['candidates'][0]['content']['parts'][0]['text'])) {
        return json_encode(['summary' => $responseObject['candidates'][0]['content']['parts'][0]['text']]);
    }

    return json_encode(['error' => "Réponse non disponible ou mal formatée."]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    $question = $_POST['question'];
    $filePath = "uploads/" . $_POST['course_file'];
    $musicStyle = isset($_POST['music_style']) ? $_POST['music_style'] : null;

    if (file_exists($filePath)) {
        $response = str_replace('*','',gemini($question, $filePath, $musicStyle)) ;
        echo $response;
    } else {
        echo json_encode(['summary' => "Fichier non disponible."]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matieres</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
            font-family: 'Times New Roman', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5), no-repeat;
        }

        /* Sidebar */
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

        /* Content */
        .content {
            margin-left: 170px;
            margin-right: -20px;
            padding: 20px;
            padding: 20px;
        }

        .header {
            background-color: #fff;
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
            background-color: whitesmoke;
            color: black;
            margin: 5px;
            padding: 9px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .header button:hover {
            background-color: wheat;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #fff;
            border-radius: 10px;
        }

        .table-container {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-container a {
            text-decoration: none;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: wheat;
            color: black;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        h1 {
            text-align: center;
            color: #2196F3;
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
            background-color: whitesmoke;
            color: white;
        }

        .actions .details {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: black;
            margin: 10px;
            padding: 10px;
        }

        .details {
            display: inline-block;
            padding: 10px 15px;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: black;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .details:hover {
            background: #4CAF50;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-5%);
        }

        .actions .delete {
            background-color: whitesmoke;
            color: white;
        }

        .ajout {
            background-color: #2196F3;
            color: white;
            display: flex;
            align-items: center;
            margin: 5px 0px;
            padding: 12px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            justify-content: center;
            position: relative;
            text-decoration: none;
        }

        .ajout:hover {
            background-color: #475be8;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .table-container .hover .ajout:hover {
            background-color: #475be8;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .export-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .export-btn:hover {
            background-color: #45a049;
        }

        .audio-btn {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            margin: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .audio-btn:hover {
            background-color: #1976D2;
            transform: scale(1.05);
        }

        .audio-btn i {
            font-size: 18px;
        }

        .audio-btn.playing {
            background-color: #F44336;
        }

        .audio-btn.playing i {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        #audioControls {
            margin: 15px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 8px;
            display: none;
        }

        .volume-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .volume-control input[type="range"] {
            flex: 1;
            height: 4px;
            background: #2196F3;
            border-radius: 2px;
        }

        .rate-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .rate-control select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .music-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .music-style-select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: white;
            font-family: 'Poppins', sans-serif;
        }

        .music-btn {
            background-color: #6200ea;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .music-btn:hover {
            background-color: #3700b3;
            transform: scale(1.05);
        }

        .music-btn i {
            font-size: 16px;
        }

        .popup {
            display: none;
            position: fixed;
            z-index: 1002;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .popup-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .quiz-container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .quiz-question {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .quiz-options label {
            display: block;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quiz-options label:hover {
            background-color: #f0f0f0;
        }

        .correct-answer {
            background-color: #c8e6c9 !important;
            border-color: #4caf50 !important;
        }

        .incorrect-answer {
            background-color: #ffcdd2 !important;
            border-color: #f44336 !important;
        }

        .quiz-submit {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .quiz-submit:hover {
            background-color: #45a049;
        }

        .score-display {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 4px;
            text-align: center;
            font-size: 18px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .haut {
            padding: 10px;
        }

        .haut h2 {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar .etu {
            background-color: #6c83f7;
        }

        .score-display button.quiz-submit {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .score-display button.quiz-submit:hover {
            background-color: #45a049;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            position: fixed;
            z-index: 9999;
            height: 2em;
            width: 2em;
            overflow: show;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        .loading-spinner:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3));
            background: radial-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3));
            z-index: -1;
        }

        .loading-spinner:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin: 1px;
            border-radius: 0.5em;
            border: 1.1em solid rgba(0, 0, 0, 0.2);
            border-left-color: #09f;
            animation: spinner 1.1s infinite linear;
        }

        @keyframes spinner {
            to {
                transform: rotate(.5turn);
            }
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
        <h2>Menu</h2>
        <?php if (!empty($matieres)): ?>
            <a href="informationEtudiant.php?matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-user"></i> Mes Information</a>
        <?php endif; ?>
        <?php if (!empty($matieres)): ?>
            <a class="etu" href="MatiereEtu.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-book"></i> Mes Matieres</a>
        <?php endif; ?>
        <?php if (!empty($matieres)): ?>
            <a href="NoteEtudiant.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-graduation-cap"></i> Mes Notes</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a href="rien.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-robot"></i> KEYCE AI</a>
        <?php endif; ?>
        <a href="index.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <div class="haut">
                <h2>Liste des Matieres</h2>
            </div>
            <form method="GET" action="gestionVer.php">
                <input name="search" type="text" placeholder="Rechercher..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <?php if (!empty($message)): ?>
            <p style="color: green;"> <?= htmlspecialchars($message); ?> </p>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <!-- <th>ID</th>
                        <th>Matricule Professeur</th> -->
                        <th>Nom professeur</th>
                        <th>Nom de Matiere</th>
                        <th>Niveau Matiere</th>
                        <th>Cours</th>
                        <th>Action</th>
                        <th>Resume</th>
                        <th>Quizz</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($matieres)): ?>
                        <?php foreach ($matieres as $matiere): ?>
                            <tr>
                                <!-- <td><?= htmlspecialchars($matiere['id']); ?></td>
                                <td><?= htmlspecialchars($matiere['matricule_prof']); ?></td> -->
                                <td><?= htmlspecialchars($matiere['nom_prof']); ?></td>
                                <td><?= str_replace('_', ' ', htmlspecialchars($matiere['nom_matiere'])); ?></td>
                                <td><?= htmlspecialchars($matiere['niveau_matiere']); ?></td>
                                <td>
                                    <?php if (!empty($matiere['cours'])): ?>
                                        <?= htmlspecialchars($matiere['cours']); ?>
                                    <?php else: ?>
                                        <span>Aucun cours disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <?php if (!empty($matiere['cours'])): ?>
                                        <!-- Bouton pour télécharger le cours -->
                                        <a href="uploads/<?= htmlspecialchars($matiere['cours']); ?>" download="<?= htmlspecialchars($matiere['cours']); ?>" class="details">
                                            <i class="fas fa-download"></i> Télécharger
                                        </a>
                                    <?php else: ?>
                                        <span>Aucun cours disponible</span>
                                    <?php endif; ?>
                                    <button class="details">
                                        <?php if (!empty($matiere['cours'])): ?>
                                            <a href="uploads/<?= htmlspecialchars($matiere['cours']); ?>" target="_blank"><i class="fas fa-eye"></i> Voir le cours</a>
                                        <?php endif; ?>
                                    </button>
                                </td>
                                <td>
                                    <button class="export-btn" onclick="generateSummary(<?php echo htmlspecialchars($matiere['id']); ?>, 'Donne-moi un résumé de ce Cours.', '<?php echo htmlspecialchars($matiere['cours']); ?>')"><i class="fas fa-file-alt"></i> Résumé</button>
                                </td>
                                <td>
                                    <button class="export-btn" onclick="generateSummary(<?php echo htmlspecialchars($matiere['id']); ?>, 'quizz', '<?php echo htmlspecialchars($matiere['cours']); ?>')"><i class="fas fa-question"></i> Quizz</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Aucune matière trouvée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <div id="summaryContent"></div>
            <div id="audioControls">
                <button class="audio-btn" onclick="toggleSpeech()">
                    <i class="fas fa-play"></i>
                    <span>Lire</span>
                </button>
                <div class="volume-control">
                    <i class="fas fa-volume-down"></i>
                    <input type="range" id="volumeControl" min="0" max="1" step="0.1" value="1">
                    <i class="fas fa-volume-up"></i>
                </div>
                <div class="rate-control">
                    <label for="rateControl">Vitesse:</label>
                    <select id="rateControl">
                        <option value="0.5">0.5x</option>
                        <option value="1" selected>1x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2">2x</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner"><h6 id="loadingSpinner" style="color: white;  font-family: 'Times New Roman', sans-serif; position: fixed;">chargement</h6></div>

    <script>
        function getUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            return {
                niveau: urlParams.get('niveau'),
                matricule: urlParams.get('matricule')
            };
        }

        function generateSummary(courseId, question, courseFile) {
            const urlParams = getUrlParameters();
            const xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.pathname + '?' + new URLSearchParams(urlParams).toString(), true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Show loading spinner
            document.getElementById('loadingSpinner').style.display = 'block';

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    // Hide loading spinner
                    document.getElementById('loadingSpinner').style.display = 'none';

                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.summary) {
                                showPopup(response.summary);
                            } else {
                                showPopup("Erreur: " + (response.error || "Réponse non disponible"));
                            }
                        } catch (e) {
                            showPopup("Erreur: Impossible de traiter la réponse");
                        }
                    } else {
                        showPopup("Erreur: Problème de communication avec le serveur");
                    }
                }
            };
            xhr.send('course_id=' + encodeURIComponent(courseId) +
                '&course_file=' + encodeURIComponent(courseFile) +
                '&question=' + encodeURIComponent(question));
        }

        function showPopup(content) {
            const popup = document.getElementById('popup');
            const summaryContent = document.getElementById('summaryContent');
            const audioControls = document.getElementById('audioControls');

            // Arrêter toute lecture en cours
            stopSpeech();

            if (content.includes('quiz-container')) {
                summaryContent.innerHTML = content;
                audioControls.style.display = 'none';

                const quizForm = summaryContent.querySelector('#quizForm');
                if (quizForm) {
                    quizForm.onsubmit = function(e) {
                        e.preventDefault();

                        quizForm.querySelectorAll('.quiz-options label').forEach(label => {
                            label.classList.remove('correct-answer', 'incorrect-answer');
                        });

                        let correct = 0;
                        const questions = quizForm.querySelectorAll('.quiz-question');
                        const totalQuestions = questions.length;

                        questions.forEach((question) => {
                            const selectedAnswer = question.querySelector('input[type="radio"]:checked');
                            if (selectedAnswer) {
                                const label = selectedAnswer.closest('label');

                                if (selectedAnswer.value === 'correct') {
                                    correct++;
                                    label.classList.add('correct-answer');
                                } else {
                                    label.classList.add('incorrect-answer');
                                    question.querySelectorAll('input[value="correct"]').forEach(input => {
                                        input.closest('label').classList.add('correct-answer');
                                    });
                                }
                            }
                        });

                        let scoreDisplay = document.querySelector('.score-display');
                        if (!scoreDisplay) {
                            scoreDisplay = document.createElement('div');
                            scoreDisplay.className = 'score-display';
                            quizForm.appendChild(scoreDisplay);
                        }

                        const percentage = (correct / totalQuestions) * 100;
                        scoreDisplay.innerHTML = `
                            <h3>Résultats du Quiz</h3>
                            <p>Score: ${correct}/${totalQuestions} (${percentage.toFixed(2)}%)</p>
                            <p>${correct === totalQuestions ? 'Parfait !' :
                                correct >= totalQuestions * 0.7 ? 'Très bien !' :
                                correct >= totalQuestions * 0.5 ? 'Continuez vos efforts !' :
                                'Révisez encore un peu.'}</p>
                            <button type="button" onclick="reloadQuiz()" class="quiz-submit">Réessayer</button>
                        `;
                        scoreDisplay.style.display = 'block';

                        quizForm.querySelectorAll('input[type="radio"]').forEach(input => {
                            input.disabled = true;
                        });

                        quizForm.querySelector('button[type="submit"]').style.display = 'none';
                    };
                }
            } else {
                summaryContent.innerHTML = `<pre style="white-space: pre-wrap; font-family: 'Poppins', sans-serif;">${content}</pre>`;
                audioControls.style.display = 'block';
            }

            popup.style.display = 'block';
        }

        function reloadQuiz() {
            const params = getUrlParameters();
            if (params.niveau && params.matricule) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('niveau', params.niveau);
                currentUrl.searchParams.set('matricule', params.matricule);
                window.location.href = currentUrl.toString();
            }
        }

        window.onclick = function(event) {
            const popup = document.getElementById('popup');
            if (event.target === popup) {
                closePopup();
            }
        };

        function closePopup() {
            stopSpeech();
            document.getElementById('popup').style.display = 'none';
        }

        let speechSynthesis = window.speechSynthesis;
        let utterance = null;
        let isPlaying = false;

        function updatePlayButton(playing) {
            const btn = document.querySelector('.audio-btn');
            const icon = btn.querySelector('i');
            const span = btn.querySelector('span');

            if (playing) {
                icon.className = 'fas fa-pause';
                span.textContent = 'Pause';
                btn.classList.add('playing');
            } else {
                icon.className = 'fas fa-play';
                span.textContent = 'Lire le résumé';
                btn.classList.remove('playing');
            }
        }

        function stopSpeech() {
            if (utterance) {
                speechSynthesis.cancel();
                isPlaying = false;
                updatePlayButton(false);
            }
        }

        function toggleSpeech() {
            const summaryContent = document.getElementById('summaryContent');
            const text = summaryContent.textContent;

            if (!isPlaying) {
                utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'fr-FR';
                utterance.volume = document.getElementById('volumeControl').value;
                utterance.rate = document.getElementById('rateControl').value;

                utterance.onend = function() {
                    isPlaying = false;
                    updatePlayButton(false);
                };

                speechSynthesis.speak(utterance);
                isPlaying = true;
                updatePlayButton(true);
            } else {
                speechSynthesis.pause();
                isPlaying = false;
                updatePlayButton(false);
            }
        }

        document.getElementById('volumeControl').addEventListener('input', function(e) {
            if (utterance) {
                utterance.volume = e.target.value;
            }
        });

        document.getElementById('rateControl').addEventListener('change', function(e) {
            if (utterance) {
                utterance.rate = e.target.value;
            }
        });
    </script>
</body>

</html>
