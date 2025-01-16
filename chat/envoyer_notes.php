<?php
require_once 'Class/Database.php';
require 'fpdf.php';
require 'Class/vendor/phpmailer/phpmailer/src/Exception.php';
require 'Class/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'Class/vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection(); // Assurez-vous que cette méthode retourne une connexion MySQLi

// Récupérer la classe sélectionnée
$classe = $_GET['classe'];
$table_notes = strtolower($classe);

// Récupérer les notes des étudiants de la classe sélectionnée
$query = "SELECT * FROM $table_notes";
$result = $db->query($query);
if (!$result) {
    die("Erreur de requête : " . $db->error);
}
$notes = $result->fetch_all(MYSQLI_ASSOC);

// Calculer les moyennes générales et attribuer les rangs
$student_averages = [];
foreach ($notes as $student) {
    $matricule = $student['matricule'];
    $nom = $student['nom'];
    $prenom = $student['prenom'];

    // Calculer la moyenne générale
    $moyenne_generale = calculateAverage($student);

    // Ajouter la moyenne générale à la liste des étudiants
    $student_averages[$matricule] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'moyenne_generale' => $moyenne_generale,
        'notes' => $student
    ];
}

// Trier les étudiants par moyenne générale décroissante
usort($student_averages, function($a, $b) {
    return $b['moyenne_generale'] <=> $a['moyenne_generale'];
});

// Attribuer les rangs
$ranks = [];
$rank = 1;
foreach ($student_averages as $matricule => $student) {
    $ranks[$matricule] = $rank++;
}

// Envoyer les emails avec les PDF et les textes d'orientation
foreach ($student_averages as $matricule => $student) {
    $nom = $student['nom'];
    $prenom = $student['prenom'];
    $moyenne_generale = $student['moyenne_generale'];
    $notes = $student['notes'];
    $rang = $ranks[$matricule];
    $mention = getMention($moyenne_generale);

    // Récupérer les informations de l'étudiant
    $query = "SELECT email, email_parent, nom_parent FROM etudiants WHERE matricule = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $matricule);
    $stmt->execute();
    $result_etudiant = $stmt->get_result();
    $etudiant = $result_etudiant->fetch_assoc();

    if ($etudiant) {
        $email_parent = $etudiant['email_parent'];
        $email_eleve = $etudiant['email'];
        $nom_parent = $etudiant['nom_parent'];

        // Générer le PDF du relevé de notes
        $pdf_path = generateReport($matricule, $nom, $prenom, $notes, $moyenne_generale, $rang, $mention);

        // Générer le texte d'orientation via Gemini
        $orientation_text = gemini($nom, $prenom, $notes);

        // Envoyer l'email avec le PDF et le texte d'orientation
        sendEmail($email_eleve, $email_parent, $nom, $prenom, $pdf_path, $orientation_text);
    }
}

header('Location: listeNote.php');
exit();

// Fonction pour calculer la moyenne générale
function calculateAverage($notes)
{
    $sum = 0;
    $count = 0;
    $matieres = [];

    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
            if (strpos($key, '_sn') !== false) {
                $matiere = str_replace('_sn', '', $key);
                $matieres[$matiere]['sn'] = $value;
            } elseif (strpos($key, '_cc') !== false) {
                $matiere = str_replace('_cc', '', $key);
                $matieres[$matiere]['cc'] = $value;
            }
        }
    }

    foreach ($matieres as $matiere => $notes) {
        if (isset($notes['sn']) && isset($notes['cc'])) {
            $sum += ($notes['sn'] + $notes['cc']) / 2;
            $count++;
        }
    }

    return $count > 0 ? $sum / $count : 0;
}

// Fonction pour obtenir la mention en fonction de la moyenne générale
function getMention($moyenne_generale)
{
    if ($moyenne_generale < 10) {
        return 'Insuffisant';
    } elseif ($moyenne_generale >= 10 && $moyenne_generale < 12) {
        return 'Passable';
    } elseif ($moyenne_generale >= 12 && $moyenne_generale < 14) {
        return 'Assez-bien';
    } elseif ($moyenne_generale >= 14 && $moyenne_generale < 16) {
        return 'Bien';
    } elseif ($moyenne_generale >= 16 && $moyenne_generale < 18) {
        return 'Très-bien';
    } elseif ($moyenne_generale >= 18 && $moyenne_generale <= 20) {
        return 'Excellent';
    } else {
        return 'Non défini';
    }
}

// Fonction pour générer le PDF du relevé de notes
function generateReport($matricule, $nom, $prenom, $notes, $moyenne_generale, $rang, $mention)
{
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);

    // Ajout du logo
    $logo_path = 'logo.jpeg'; // Chemin du logo
    if (file_exists($logo_path)) {
        $pdf->Image($logo_path, 10, 10, 30, 30); // Logo en haut à gauche
    }

    // Contour de la page
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(10, 10, 190, 270, 'D'); // Bordure autour du contenu

    // Titre principal
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetXY(50, 20); // Position centrée
    $pdf->Cell(110, 10, 'Releve de Notes', 0, 1, 'C');

    $pdf->Ln(20);

    // Informations de l'étudiant
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, "Matricule : $matricule", 0, 1, 'C');
    $pdf->Cell(0, 10, "Nom et Prenom : $nom $prenom", 0, 1, 'C');
    $pdf->Cell(0, 10, "Moyenne Générale : $moyenne_generale", 0, 1, 'C');
    $pdf->Cell(0, 10, "Rang : $rang", 0, 1, 'C');
    $pdf->Cell(0, 10, "Mention : $mention", 0, 1, 'C');

    $pdf->Ln(10);

    // Notes par matière - Tableau
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Notes par Matiere", 0, 1, 'L');

    // En-tête du tableau
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(200, 220, 255); // Couleur de fond pour l'en-tête
    $pdf->Cell(100, 10, 'Matière', 1, 0, 'C', true);
    $pdf->Cell(90, 10, 'Note', 1, 1, 'C', true);

    // Contenu du tableau
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetFillColor(245, 245, 245); // Couleur alternée pour les lignes
    $fill = false;

    $matieres = [];
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
            if (strpos($key, '_sn') !== false) {
                $matiere = str_replace('_sn', '', $key);
                $matieres[$matiere]['sn'] = $value;
            } elseif (strpos($key, '_cc') !== false) {
                $matiere = str_replace('_cc', '', $key);
                $matieres[$matiere]['cc'] = $value;
            }
        }
    }

    foreach ($matieres as $matiere => $notes) {
        if (isset($notes['sn']) && isset($notes['cc'])) {
            $moyenne_matiere = ($notes['sn'] + $notes['cc']) / 2;
            $pdf->Cell(100, 10, ucfirst($matiere), 1, 0, 'L', $fill);
            $pdf->Cell(90, 10, $moyenne_matiere, 1, 1, 'C', $fill);
            $fill = !$fill; // Alterner la couleur de fond
        }
    }

    $pdf->Ln(10);

    // Texte informatif (footer)
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, 'Produit par Keyce Informatique et Intelligence Artificielle', 0, 1, 'C');

    // Sortie PDF
    $pdf_path = "releves/releve_$matricule.pdf";
    $pdf->Output('F', $pdf_path);

    return $pdf_path;
}

// Fonction pour générer un texte d'orientation via l'API Gemini
function gemini($nom, $prenom, $notes)
{
    // Clé API
    $GKey = "AIzaSyCppIR7-I0eqmDgYYXv_EqONSxD3z6eLYc";

    // Construire le texte des notes
    $notes_text = "";
    $matieres = [];
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
            if (strpos($key, '_sn') !== false) {
                $matiere = str_replace('_sn', '', $key);
                $matieres[$matiere]['sn'] = $value;
            } elseif (strpos($key, '_cc') !== false) {
                $matiere = str_replace('_cc', '', $key);
                $matieres[$matiere]['cc'] = $value;
            }
        }
    }

    foreach ($matieres as $matiere => $notes) {
        if (isset($notes['sn']) && isset($notes['cc'])) {
            $moyenne_matiere = ($notes['sn'] + $notes['cc']) / 2;
            $notes_text .= ucwords(str_replace('_', ' ', $matiere)) . ": " . $moyenne_matiere . ", ";
        }
    }
    $notes_text = rtrim($notes_text, ", ");

    // Définir la question
    $question = "Peux-tu me produire un mail professionnel, concis et sans partie à remplir pour donner un conseil d'orientation à un étudiant dont le nom est $nom, le prénom $prenom, et dont les notes sont les suivantes : $notes_text. Le mail sera adressé aux parents. Le nom de l'établissement c'est Keyce Informatique et IA et le message est envoyé de la part de la scolarité de l'établissement. Sois clair et dit dans quelle filière il devrait pointer dans le message et utilise un langage professionnel.";

    // Construire l'URL de l'API
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

    // Préparer les données de la requête
    $requestData = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $question]
                ]
            ]
        ]
    ]);

    // Initialiser cURL
    $ch = curl_init($url);

    // Configurer les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);

    // Désactiver la vérification SSL (utiliser uniquement pour les tests)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Envoyer la requête et récupérer la réponse
    $response = curl_exec($ch);

    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        die("Erreur cURL : " . curl_error($ch));
    }

    // Fermer la connexion cURL
    curl_close($ch);

    // Décoder la réponse JSON
    $responseObject = json_decode($response, true);

    if (isset($responseObject['candidates']) && count($responseObject['candidates']) > 0) {
        // Obtenir le contenu du premier candidat
        $content = $responseObject['candidates'][0]['content'] ?? null;

        // Vérifier si le contenu existe
        if ($content && isset($content['parts']) && count($content['parts']) > 0) {
            return $content['parts'][0]['text'];
        } else {
            return "Aucune partie trouvée dans le contenu sélectionné.";
        }
    } else {
        return "Aucun candidat trouvé dans la réponse JSON.";
    }
}

// Fonction pour envoyer un email avec PHPMailer
function sendEmail($to, $cc, $nom, $prenom, $attachment, $orientation_text)
{
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Remplacez par le serveur SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'mininoulilou@gmail.com';
    $mail->Password = 'hpqz zoke gzcq bkfb';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('mininoulilou@gmail.com', 'Keyce Informatique');
    $mail->addAddress($to);
    $mail->addCC($cc);
    $mail->Subject = 'Votre relevé de notes';
    $mail->Body = $orientation_text;
    $mail->addAttachment($attachment);

    if (!$mail->send()) {
        echo 'Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo;
    } else {
        return 'Carte envoyée par email avec succès.';
    }
}
?>
