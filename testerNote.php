<?php
require_once 'Class/Database.php';
require('fpdf.php');
session_start();

$matricule = $_GET['matricule'];

// Récupération des informations de l'étudiant
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT * FROM etudiants WHERE matricule = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $matricule);
$stmt->execute();
$result = $stmt->get_result();
$studentInfo = $result->fetch_assoc();

if (!$studentInfo) {
    echo "Étudiant non trouvé.";
    exit();
}

$classe = $studentInfo['niveau'];
$table_notes = "note_" . strtolower($classe);

// Récupération des notes
$query = "SELECT * FROM $table_notes WHERE matricule = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $matricule);
$stmt->execute();
$result = $stmt->get_result();
$notes = $result->fetch_assoc();

// Fonction pour générer le PDF
function generateReleveNotes($matricule, $nom, $prenom, $classe, $notes) {
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);

    $pdf->SetFillColor(240, 240, 255);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetTextColor(50, 50, 50);

    $pdf->SetXY(20, 40);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(170, 10, 'Relevé de Notes', 0, 1, 'C', true);
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, 'Matricule: ' . $matricule, 0, 1, 'C');
    $pdf->Cell(0, 10, 'Nom: ' . $nom, 0, 1, 'C');
    $pdf->Cell(0, 10, 'Prénom: ' . $prenom, 0, 1, 'C');
    $pdf->Cell(0, 10, 'Classe: ' . $classe, 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Notes:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($notes as $matiere => $note) {
        if (!in_array($matiere, ['id', 'matricule', 'nom', 'prenom'])) {
            $pdf->Cell(0, 10, ucwords(str_replace('_', ' ', $matiere)) . ": " . $note, 0, 1, 'L');
        }
    }

    $pdf->SetXY(15, 35);
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(15, 35, 180, 160, 'D');

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="releve_notes_' . $matricule . '.pdf"');
    $pdf->Output('D');
    exit();
}

if (isset($_POST['export'])) {
    generateReleveNotes(
        $studentInfo['matricule'],
        $studentInfo['nom'],
        $studentInfo['prenom'],
        $studentInfo['niveau'],
        $notes
    );
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #7986CB;
            --secondary-color: #9FA8DA;
            --accent-color: #5C6BC0;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #E8EAF6, #C5CAE9);
            min-height: 100vh;
        }

        .side-bar {
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            padding: 2rem 1rem;
            color: white;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .admin-section {
            text-align: center;
            padding: 1rem 0;
        }

        .admin-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.2);
            padding: 3px;
            background: white;
            transition: transform 0.3s ease;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin-top: 2rem;
        }

        .menu-item {
            margin-bottom: 0.5rem;
            position: relative;
        }

        .menu-link {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(10px);
        }

        .dropdown-menu {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-left: 2rem;
            padding: 0.5rem 0;
            display: none;
            height: 0;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            display: block;
            height: auto;
            opacity: 1;
        }

        .card-container {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .student-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            max-width: 800px;
            margin: 2rem auto;
            transition: transform 0.3s ease;
        }

        .btn-custom {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.5);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .conseil-content {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .conseil-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .conseil-content strong {
            color: var(--primary-color);
        }

        footer {
            margin-left: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.1);
            color: #333;
            padding: 1rem;
            text-align: center;
            backdrop-filter: blur(5px);
        }

        @media (max-width: 768px) {
            .side-bar {
                transform: translateX(-100%);
            }
            .card-container, footer {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <aside class="side-bar">
        <div class="admin-section">
            <h4 class="mt-3 mb-4">Etudiant</h4>
        </div>
        <ul class="menu">
            <li class="menu-item">
                <a href="#" class="menu-link dropdown-toggle">
                    <i class="fas fa-graduation-cap menu-icon"></i> Mes notes
                </a>
                <div class="dropdown-menu">
                    <a href="note_eleve_cc.php" class="dropdown-item">Notes cc</a>
                    <a href="note_eleve_exam.php" class="dropdown-item">Notes exam</a>
                    <a href="note_eleve_tp.php" class="dropdown-item">Notes tp</a>
                </div>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link dropdown-toggle">
                    <i class="fas fa-book menu-icon"></i> Mes cours
                </a>
                <div class="dropdown-menu">
                    <a href="mes_cours.php" class="dropdown-item">Liste des cours</a>
                </div>
            </li>
            <li class="menu-item">
                <a href="card.php" class="menu-link">
                    <i class="fas fa-user menu-icon"></i> Mes informations
                </a>
            </li>
            <li class="menu-item">
                <a href="chatbot.php" class="menu-link">
                    <i class="fas fa-robot menu-icon"></i> ChatBot
                </a>
            </li>
            <li class="menu-item">
                <a href="logout.php" class="menu-link">
                    <i class="fas fa-sign-out-alt menu-icon"></i> Déconnexion
                </a>
            </li>
        </ul>
    </aside>

    <div class="card-container">
        <div class="student-card">
            <div class="text-end mb-4">
                <button class="btn btn-custom" onclick="location.href='logout.php'">
                    <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                </button>
            </div>

            <h2 class="text-center mb-4">
                <i class="fas fa-file-alt me-2"></i>Relevé de Notes
            </h2>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-item">
                        <i class="fas fa-fingerprint me-2"></i>
                        <strong>Matricule:</strong> <?php echo $studentInfo['matricule']; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <i class="fas fa-user me-2"></i>
                        <strong>Nom:</strong> <?php echo $studentInfo['nom']; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <i class="fas fa-user me-2"></i>
                        <strong>Prénom:</strong> <?php echo $studentInfo['prenom']; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <i class="fas fa-graduation-cap me-2"></i>
                        <strong>Classe:</strong> <?php echo $studentInfo['niveau']; ?>
                    </div>
                </div>
            </div>

            <h3 class="text-center mt-4 mb-3">Notes:</h3>
            <?php if ($notes): ?>
                <button class="btn btn-primary conseil-button mb-3" id="conseilButton">
                    <i class="fas fa-lightbulb me-2"></i>Recevoir des conseils
                </button>

                <table class="table table-bordered table-hover mx-auto">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notes as $matiere => $note): ?>
                            <?php if (!in_array($matiere, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                <tr>
                                    <td><?php echo ucwords(str_replace('_', ' ', $matiere)); ?></td>
                                    <td><?php echo $note; ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">Aucune note disponible pour le moment.</p>
            <?php endif; ?>

            <form method="POST" action="" class="text-center mt-4">
                <button type="submit" name="export" class="btn btn-custom">
                    <i class="fas fa-download me-2"></i>Exporter le relevé de notes
                </button>
            </form>
        </div>
    </div>

    <!-- Modal pour les conseils -->
    <div class="modal fade" id="conseilModal" tabindex="-1" aria-labelledby="conseilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="conseilModalLabel">
                        <i class="fas fa-lightbulb me-2"></i>Conseils Personnalisés
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="conseilText" class="p-3"></div>
                </div>
                <div<div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p class="mb-0">&copy; 2024 Université KEYCE INFORMATIQUE ET IA. Tous droits réservés.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const conseilButton = document.getElementById('conseilButton');
            const conseilModal = new bootstrap.Modal(document.getElementById('conseilModal'));
            const conseilText = document.getElementById('conseilText');

            // Gestion des menus déroulants
            document.querySelectorAll('.dropdown-toggle').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.classList.toggle('show');
                });
            });

            // Gestion du bouton de conseil
            conseilButton.addEventListener('click', async function() {
                try {
                    // Collecter les notes
                    const notes = {};
                    document.querySelectorAll('table tbody tr').forEach(row => {
                        const matiere = row.cells[0].textContent.trim();
                        const note = parseFloat(row.cells[1].textContent.trim());
                        if (!isNaN(note)) {
                            notes[matiere] = note;
                        }
                    });

                    // Vérifier s'il y a des notes
                    if (Object.keys(notes).length === 0) {
                        throw new Error('Aucune note disponible pour générer des conseils.');
                    }

                    // Afficher le modal avec l'indicateur de chargement
                    conseilText.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Génération des conseils en cours...</p></div>';
                    conseilModal.show();

                    // Construire la description des notes
                    let notesDescription = Object.entries(notes)
                        .map(([matiere, note]) => `${matiere}: ${note}/20`)
                        .join(', ');

                    const nom = "<?php echo $studentInfo['nom']; ?>";
                    const prenom = "<?php echo $studentInfo['prenom']; ?>";

                    // Préparer le prompt pour l'API
                    const prompt = `En tant que conseiller pédagogique, analyse les résultats suivants pour l'étudiant ${nom} ${prenom} :

Notes: ${notesDescription}

Fournis des conseils personnalisés et constructifs pour chaque matière en te concentrant sur :
1. Les points forts à maintenir
2. Les axes d'amélioration spécifiques
3. Des méthodes de travail concrètes

Utilise un ton encourageant et professionnel. Structure ta réponse de manière claire.`;

                    // Appel à l'API Gemini
                    const response = await fetch(`https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=AIzaSyA7OteJoYw6GM-IM7bgRdT2nglSvo_HZn0`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            contents: [{
                                parts: [{
                                    text: prompt
                                }]
                            }]
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Erreur lors de la communication avec l\'API');
                    }

                    const data = await response.json();
                    const conseil = data.candidates[0].content.parts[0].text;

                    // Formater et afficher les conseils
                    const formattedConseil = formatConseil(conseil);
                    conseilText.innerHTML = formattedConseil;

                } catch (error) {
                    console.error('Erreur:', error);
                    conseilText.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${error.message || 'Une erreur est survenue lors de la génération des conseils. Veuillez réessayer.'}
                        </div>`;
                }
            });
        });

        function formatConseil(conseil) {
    // Nettoyer et formater le texte
    let formattedText = conseil
        // Supprimer tous les astérisques
        .replace(/\*/g, '')
        // Convertir les listes numériques en HTML
        .replace(/(\d+\.\s+)/g, '<br><strong>$1</strong>')
        // Mettre en gras les noms des matières
        .replace(/([A-Za-zÀ-ÿ\s]+):/g, '<strong>$1:</strong>')
        // Convertir les sauts de ligne simples en HTML
        .replace(/\n/g, '<br>')
        // Nettoyer les sauts de ligne multiples
        .replace(/<br\s*\/?><br\s*\/?>/g, '<br>')
        // Ajouter des marges entre les paragraphes
        .replace(/<br>/g, '</p><p>');

    // Wrapper le texte dans des balises de paragraphe
    formattedText = `<p class="mb-3">${formattedText}</p>`;

    // Ajouter des styles Bootstrap
    return `
        <div class="conseil-content">
            ${formattedText}
        </div>`;
}
    </script>
</body>
</html>