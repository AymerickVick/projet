<?php
// Désactiver l'affichage des erreurs dans la sortie
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

// Démarrer la mise en mémoire tampon
ob_start();

require_once 'vendor/autoload.php';
require_once 'Class/Database.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/signn/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use setasign\Fpdi\Tcpdf\Fpdi;

// Fonction pour générer un hash de vérification
function generateSignature($matricule, $nomPrenom, $notes, $moyenne, $rank)
{
    $dataToHash = $matricule . $nomPrenom . implode(',', $notes) . $moyenne . $rank;
    return hash('sha256', $dataToHash);
}

class GradeProcessor
{
    private $db;
    private $classe;
    private $base_table;
    private $all_averages = [];
    private $student_grades = [];
    private $certificate_path;
    private $private_key_path;

    public function __construct($db, $classe)
    {
        $this->db = $db;
        $this->classe = $classe;
        $this->base_table = strtolower($classe);
        $this->certificate_path = __DIR__ . '/signn/certificates/certificate.pem';
        $this->private_key_path = __DIR__ . '/signn/certificates/private_key.pem';

        // Vérification détaillée des certificats
        if (!file_exists($this->certificate_path)) {
            throw new Exception("Le fichier de certificat n'existe pas: " . $this->certificate_path);
        }
        if (!file_exists($this->private_key_path)) {
            throw new Exception("Le fichier de clé privée n'existe pas: " . $this->private_key_path);
        }
        if (!is_readable($this->certificate_path)) {
            throw new Exception("Le fichier de certificat n'est pas lisible");
        }
        if (!is_readable($this->private_key_path)) {
            throw new Exception("Le fichier de clé privée n'est pas lisible");
        }
    }

    public function processGrades()
    {
        try {
            $students = $this->getStudents();
            foreach ($students as $student) {
                $this->calculateStudentGrades($student);
            }
            $this->calculateRankings();
            $this->sendReports($students);

            // Nettoyer la sortie tampon avant la redirection
            if (ob_get_length()) ob_end_clean();

            return true;
        } catch (Exception $e) {
            error_log("Erreur dans processGrades: " . $e->getMessage());
            return false;
        }
    }

    private function getStudents()
    {
        $query = "SELECT DISTINCT e.matricule, e.nom, e.prenom, e.email, e.email_parent, e.nom_parent
                 FROM etudiants e
                 INNER JOIN note_{$this->base_table} n ON e.matricule = n.matricule";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function hasValidGrades($result)
    {
        if (!$result) return false;
        foreach ($result as $key => $value) {
            if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
                if (empty($value) || floatval($value) <= 0) {
                    return false;
                }
            }
        }
        return true;
    }

    private function calculateStudentGrades($student)
    {
        $matricule = $student['matricule'];
        $grades = [];
        $valid_grades = true;

        $tables = ["note_{$this->base_table}", "exam_{$this->base_table}", "tp_{$this->base_table}"];
        foreach ($tables as $table) {
            $query = "SELECT * FROM {$table} WHERE matricule = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('s', $matricule);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$this->hasValidGrades($result)) {
                $valid_grades = false;
                break;
            }

            if ($result) {
                foreach ($result as $subject => $grade) {
                    if (!in_array($subject, ['id', 'matricule', 'nom', 'prenom'])) {
                        $grades[$subject][] = floatval($grade);
                    }
                }
            }
        }

        if ($valid_grades && !empty($grades)) {
            $final_grades = [];
            $sum = 0;
            $count = 0;

            foreach ($grades as $subject => $notes) {
                if (count($notes) === 3) {
                    $average = array_sum($notes) / 3;
                    $final_grades[$subject] = $average;
                    $sum += $average;
                    $count++;
                }
            }

            if ($count > 0) {
                $overall_average = $sum / $count;
                $this->all_averages[$matricule] = $overall_average;
                $this->student_grades[$matricule] = $final_grades;
            }
        }
    }

    private function calculateRankings()
    {
        arsort($this->all_averages);
    }

    private function getMention($average)
    {
        if ($average >= 16) return 'EXCELLENT';
        if ($average >= 14) return 'BIEN';
        if ($average >= 12) return 'ASSEZ BIEN';
        if ($average >= 10) return 'PASSABLE';
        return 'INSUFFISANT';
    }

    private function generatePDF($student, $grades, $average, $rank)
{
    $mention = $this->getMention($average);
    
    // Créer une instance de TCPDF avec FPDI
    $pdf = new Fpdi('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Configurer le document
    $pdf->SetCreator('Keyce Informatique');
    $pdf->SetAuthor('Keyce Informatique et IA');
    $pdf->SetTitle('Relevé de Notes - ' . $student['nom'] . ' ' . $student['prenom']);
    
    // Ajouter une page
    $pdf->AddPage();
    $pdf->SetMargins(20, 20, 20);
    
    // En-tête avec logo
    if (file_exists('uploads/logo.jpg')) {
        $pdf->Image('uploads/logo.jpg', 20, 10, 40); // Logo plus grand
    }
    
    // Informations de l'école
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'KEYCE INFORMATIQUE ET IA', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, 'Excellence et Innovation', 0, 1, 'C');
    $pdf->Cell(0, 6, 'BP: XXXX Yaoundé - Cameroun', 0, 1, 'C');
    
    // Titre du document
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetFillColor(0, 51, 102); // Bleu professionnel
    $pdf->SetTextColor(255, 255, 255); // Texte blanc
    $pdf->Cell(0, 12, 'RELEVÉ DE NOTES', 0, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0); // Retour au texte noir
    
    // Année académique
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Année Académique ' . date('Y') . ' - ' . (date('Y') + 1), 0, 1, 'C');
    
    // Information de l'étudiant
    $pdf->Ln(5);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 11);
    
    // Cadre d'information étudiant
    $pdf->RoundedRect(20, $pdf->GetY(), 170, 30, 3.50, '1111', 'DF');
    $startY = $pdf->GetY() + 5;
    $pdf->SetY($startY);
    
    // Informations sur deux colonnes
    $pdf->SetX(25);
    $pdf->Cell(80, 8, 'MATRICULE: ' . $student['matricule'], 0, 0);
    $pdf->Cell(80, 8, 'NIVEAU: ' . ($student['niveau'] ?? 'Non spécifié'), 0, 1);
    $pdf->SetX(25);
    $pdf->Cell(80, 8, 'NOM: ' . $student['nom'], 0, 0);
    $pdf->Cell(80, 8, 'PRÉNOM: ' . $student['prenom'], 0, 1);
    
    // Tableau des notes
    $pdf->Ln(10);
    // En-tête du tableau
    $pdf->SetFillColor(0, 51, 102);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(90, 10, 'MATIÈRE', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'COEF', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'NOTE', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'APPRÉCIATION', 1, 1, 'C', true);
    
    // Contenu du tableau
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(248, 248, 248);
    $fill = false;
    
    foreach ($grades as $subject => $grade) {
        $coef = $grade['coefficient'] ?? 1;
        $note = $grade['note'] ?? $grade;
        $appreciation = $this->getMention($note);
        
        $subject = iconv('UTF-8', 'windows-1252//TRANSLIT', $subject);
        $pdf->Cell(90, 8, $subject, 1, 0, 'L', $fill);
        $pdf->Cell(25, 8, $coef, 1, 0, 'C', $fill);
        $pdf->Cell(25, 8, number_format($note, 2), 1, 0, 'C', $fill);
        $pdf->Cell(30, 8, $appreciation, 1, 1, 'C', $fill);
        $fill = !$fill;
    }
    
    // Résultats finaux dans un cadre élégant
    $pdf->Ln(10);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 11);
    
    // Cadre des résultats
    $pdf->RoundedRect(20, $pdf->GetY(), 170, 35, 3.50, '1111', 'DF');
    $startY = $pdf->GetY() + 5;
    $pdf->SetY($startY);
    
    $pdf->SetX(25);
    $pdf->Cell(80, 8, 'MOYENNE GÉNÉRALE:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(80, 8, number_format($average, 2) . ' / 20', 0, 1);
    
    $pdf->SetX(25);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(80, 8, 'RANG:', 0, 0);
    $pdf->Cell(80, 8, $rank . ' sur ' . ($total_students ?? 'N/A'), 0, 1);
    
    $pdf->SetX(25);
    $pdf->Cell(80, 8, 'MENTION:', 0, 0);
    $pdf->SetTextColor(0, 102, 204); // Bleu pour la mention
    $pdf->Cell(80, 8, $mention, 0, 1);
    $pdf->SetTextColor(0, 0, 0);

    $signature_image_path= 'signn/certificates/signature.png';
    
    // Signature et validation
    try {
        $this->addSignature($pdf, $student, $signature_image_path);
    } catch (Exception $e) {
        error_log("Erreur de signature: " . $e->getMessage());
    }
    
    // Pied de page
    $pdf->SetY(-30);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 10, 'Document officiel - Keyce Informatique et IA', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Généré le ' . date('d/m/Y à H:i'), 0, 1, 'C');
    
    // Enregistrement et retour
    $releves_dir = __DIR__ . '/releves';
    if (!is_dir($releves_dir)) {
        mkdir($releves_dir, 0755, true);
    }
    
    $pdf_path = $releves_dir . "/releve_{$student['matricule']}.pdf";
    $pdf->Output($pdf_path, 'F');
    
    return [$pdf_path];
}

// Méthode auxiliaire pour la signature
private function addSignature($pdf, $student, $signature_image_path)
{
    if (!file_exists($this->certificate_path) || !file_exists($this->private_key_path)) {
        throw new Exception("Certificat ou clé privée manquante");
    }
    
    $certificate = file_get_contents($this->certificate_path);
    $privateKey = file_get_contents($this->private_key_path);
    
    if (!$certificate || !$privateKey) {
        throw new Exception("Impossible de lire le certificat ou la clé privée");
    }
    
    $info = [
        'Name' => 'Keyce Informatique et IA',
        'Location' => 'Yaoundé, Cameroun',
        'Reason' => 'Authentification du relevé de notes',
        'ContactInfo' => 'contact@keyce.edu'
    ];
    
    $pdf->setSignature($certificate, $privateKey, '', '', 2, $info, 'A');
    
    // Zone de signature
    $pdf->SetY(-60);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 10, 'Le Directeur', 0, 1, 'R');
    $pdf->setSignatureAppearance(130, $pdf->GetY(), 50, 20);
    
    if (file_exists($signature_image_path)) {
        $pdf->Image($signature_image_path, 130, $pdf->GetY(), 50, 20);
    }
}

    private function getOrientationText($nom, $prenom, $grades)
    {
        $GKey = "AIzaSyA7OteJoYw6GM-IM7bgRdT2nglSvo_HZn0";

        $notes_description = "";
        foreach ($grades as $matiere => $note) {
            $notes_description .= "$matiere: $note/20, ";
        }
        $notes_description = rtrim($notes_description, ", ");

        $prompt = "En tant que conseiller d'orientation professionnel, analysez les résultats suivants pour l'étudiant " .
            $nom . " " . $prenom . ".\n\n" .
            "Notes: " . $notes_description . "\n\n" .
            "Rédigez un email professionnel, concis et sans partie à remplir, destiné aux parents. L'email sera adressé aux parents. Le nom de l'établissement c'est Keyce Informatique et IA et le message est envoyé de la part de la scolarité de l'établissement. Soyez un peu plus large dans le message et utilisez un langage professionnel.";

        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ??
            "Une erreur est survenue lors de la génération du conseil d'orientation.";
    }

    private function sendEmail($to, $cc, $nom, $prenom, $pdf_info, $orientation_text)
    {
        list($pdf_path, $signature) = $pdf_info;
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gaza45palestine@gmail.com';
            $mail->Password = 'tira vtly vbec schk';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('gaza45palestine@gmail.com', 'Keyce Informatique');
            $mail->addAddress($to);
            $mail->addCC($cc);
            $mail->addAttachment($pdf_path);

            $mail->isHTML(true);
            $mail->Subject = "Relevé de Notes - " . $nom . " " . $prenom;

            $mail->Body = "<div style='font-family: helvetica, sans-serif;'>" .
                "<h2>RELEVE DE NOTES ET CONSEIL D'ORIENTATION</h2>" .
                "<div style='margin: 20px 0;'>" . nl2br(htmlspecialchars($orientation_text)) . "</div>" .
                "<p style='font-style: italic;'>Signature numérique: " . $signature . "</p>" .
                "<p style='color: #666;'>Message automatique - Ne pas répondre</p>" .
                "</div>";

            $mail->AltBody = strip_tags($orientation_text) . "\n\nSignature numérique: " . $signature;

            $mail->send();
        } catch (Exception $e) {
            error_log("Erreur d'envoi email pour {$nom} {$prenom}: {$mail->ErrorInfo}");
        }
    }

    public function sendReports($students)
    {
        $rank = 1;
        foreach ($this->all_averages as $matricule => $average) {
            $student = array_filter($students, function ($s) use ($matricule) {
                return $s['matricule'] === $matricule;
            });
            $student = reset($student);

            if ($student && isset($this->student_grades[$matricule])) {
                $grades = $this->student_grades[$matricule];
                $pdf_info = $this->generatePDF($student, $grades, $average, $rank);
                $orientation_text = $this->getOrientationText($student['nom'], $student['prenom'], $grades);
                $this->sendEmail(
                    $student['email_parent'],
                    $student['email'],
                    $student['nom'],
                    $student['prenom'],
                    $pdf_info,
                    $orientation_text
                );
                $rank++;
            }
        }
    }
}

// Exécution principale
try {
    $database = new Database();
    $processor = new GradeProcessor($database->getConnection(), $_GET['classe']);
    $processor->processGrades();
    header('Location: listeNote' . $_GET['classe'] . '.php');
    exit();
} catch (Exception $e) {
    error_log("Erreur lors du traitement des notes: " . $e->getMessage());
    // Rediriger vers une page d'erreur ou afficher un message
    header('Location: error.php?message=' . urlencode("Une erreur est survenue lors du traitement des notes."));
    exit();
}
