<?php

$niveau = strval($_GET['niveau']);
$matricule = strval($_GET['matricule']);

// Fonction pour envoyer un message à Gemini
function gemini($message)
{
    $GKey = "AIzaSyA7OteJoYw6GM-IM7bgRdT2nglSvo_HZn0";
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

    $requestData = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $message]
                ]
            ]
        ]
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return "Erreur cURL : " . curl_error($ch);
    }

    curl_close($ch);

    $responseObject = json_decode($response, true);

    if (isset($responseObject['candidates'][0]['content']['parts'][0]['text'])) {
        return str_replace('*', ' ', $responseObject['candidates'][0]['content']['parts'][0]['text']);
    }

    return "Réponse non disponible ou mal formatée.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $userMessage = $_POST['message'];
    $response = gemini($userMessage);
    echo json_encode(['response' => $response]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Gemini</title>
    <!-- Lien vers Bootstrap pour la mise en page responsive -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Times New Roman', sans-serif;
            margin: 0;
            padding: 10px;
            display: flex;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        }

        .sidebar {
            background: linear-gradient(#fff, #74ebd5, #fff);
            color: black;
            height: 93vh;
            position: fixed;
            width: 200px;
            padding: 20px;
            border-radius: 10px;
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar h2 {
            margin: 0 0 20px 0;
            font-size: 1.5em;
            text-align: center;
        }

        .sidebar a {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            transform: translateY(-10%);
        }

        .sidebar .etu {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        }

        .chat-container {
            width: 90%;
            max-width: 500px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-left: 500px;
        }

        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f9f9f9;
        }

        .message {
            margin: 10px 0;
            display: flex;
            align-items: center;
            position: relative;
        }

        .user-message {
            justify-content: flex-end;
        }

        .bot-message {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            font-size: 14px;
            position: relative;
        }

        .user-message .message-content {
            background: #d1e7ff;
            color: #004085;
            border-radius: 10px 10px 0 10px;
        }

        .bot-message .message-content {
            background: #e2ffe5;
            color: #155724;
            border-radius: 10px 10px 10px 0;
        }

        .message-icon {
            width: 40px;
            height: 40px;
            margin: 0 10px;
        }

        form {
            display: flex;
            align-items: center;
            border-top: 1px solid #ccc;
            padding: 10px;
            background: white;
        }

        .pause-icon {
            display: none;
            width: 30px;
            height: 30px;
            cursor: pointer;
            margin-right: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .loading-spinner {
            display: none;
            font-size: 20px;
            color: #007bff;
            position: absolute;
            bottom: -15px;
            left: 4%;
            transform: translateX(-50%);
        }

        .loading-spinner.active {
            display: block;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2><i class="fas fa-bars"></i> Menu </h2>
        <?php if (!empty($matricule)): ?>
            <a href="informationEtudiant.php?matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-user"></i> Mes Informations</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a href="MatiereEtu.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-book"></i> Mes Matieres</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a href="NoteEtudiant.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-graduation-cap"></i> Mes Notes</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a class="etu" href="rien.php?niveau=<?= htmlspecialchars($niveau); ?>&matricule=<?= htmlspecialchars($matricule); ?>"><i class="fas fa-robot"></i> KEYCE AI</a>
        <?php endif; ?>
        <a href="index.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <div class="chat-container">
        <div class="header">
            Keyce AI
        </div>
        <div class="messages" id="messages">
            <!-- Messages affichés ici -->
        </div>
        <form id="chat-form">
            <img id="pause-icon" class="pause-icon" src="uploads/worker.gif" alt="Pause">
            <input type="text" id="message-input" placeholder="Entrez votre message..." required>
            <button type="submit">Envoyer</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const messages = document.getElementById('messages');
        const pauseIcon = document.getElementById('pause-icon');
        let speechSynthesisUtterance;

        // Fonction pour ajouter un message au chat
        function addMessage(text, sender) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', `${sender}-message`);

            const icon = document.createElement('img');
            icon.className = 'message-icon';
            icon.src =
                sender === 'user' ?
                'uploads/worker.gif' :
                'uploads/ai-assistant.gif';

            const content = document.createElement('div');
            content.className = 'message-content';

            messageElement.appendChild(sender === 'user' ? content : icon);
            messageElement.appendChild(sender === 'user' ? icon : content);

            messages.appendChild(messageElement);
            messages.scrollTop = messages.scrollHeight;

            // Animation: Afficher le texte lettre par lettre
            let currentIndex = 0;
            const interval = 50; // Temps en ms entre chaque lettre

            function typeLetter() {
                if (currentIndex < text.length) {
                    content.textContent += text[currentIndex];
                    currentIndex++;
                    setTimeout(typeLetter, interval);
                }
            }

            typeLetter(); // Démarrer l'animation

            return messageElement;
        }

        // Fonction pour faire parler le robot
        function speakText(text) {
            if (speechSynthesis.speaking) {
                speechSynthesis.cancel();
            }

            speechSynthesisUtterance = new SpeechSynthesisUtterance(text);
            speechSynthesisUtterance.lang = 'fr-FR';
            speechSynthesis.speak(speechSynthesisUtterance);
        }

        // Message de bienvenue
        window.addEventListener('DOMContentLoaded', () => {
            const welcomeMessage = "Bonjour et bienvenue sur Keyce AI, j'espère que vous avez passé de bonnes fêtes en famille ? Que puis-je faire pour vous ?";
            addMessage(welcomeMessage, 'bot');
            speakText(welcomeMessage);
        });

        // Gestion de l'envoi des messages
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const userMessage = messageInput.value;
            const messageElement = addMessage(userMessage, 'user');

            // Afficher l'icône de chargement tournante
            const loadingSpinner = document.createElement('i');
            loadingSpinner.classList.add('fas', 'fa-spinner', 'fa-spin', 'loading-spinner', 'active');
            messageElement.appendChild(loadingSpinner);

            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    message: userMessage,
                }),
            });

            const data = await response.json();
            const botResponse = data.response || 'Erreur : Réponse indisponible.';
            addMessage(botResponse, 'bot');
            speakText(botResponse);

            // Masquer l'icône de chargement tournante
            loadingSpinner.classList.remove('active');
            messageElement.removeChild(loadingSpinner);

            messageInput.value = '';
        });
    </script>
</body>

</html>
