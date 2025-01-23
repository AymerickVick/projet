<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "Keyce-2024";
$dbname = "etudiants";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête pour compter le nombre de matières par niveau
$sql = "SELECT niveau_matiere, COUNT(*) as count FROM matieres WHERE niveau_matiere IN ('B1', 'B2', 'B3') GROUP BY niveau_matiere";
$result = $conn->query($sql);

$matiereCounts = [
    'B1' => 0,
    'B2' => 0,
    'B3' => 0
];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $matiereCounts[$row['niveau_matiere']] = $row['count'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Matières</title>
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

        /* Style for TPE cells */
        .tpe {
            background-color: green;
            color: white;
        }

        /* Cartes pour afficher le nombre de matières par niveau */
        .card-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            width: 100%;
            margin-bottom: 30px;
        }

        .card {
            flex: 1;
            background-color: #495057;
            color: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .card-text {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .card i {
            font-size: 1.5rem;
        }

        /* Couleurs spécifiques pour chaque carte */
        .card-b1 {
            background-color: #17a2b8;
            color: white;
        }

        .card-b2 {
            background-color: #28a745;
            color: white;
        }

        .card-b3 {
            background-color: #ffc107;
            color: white;
        }

        /* Boutons pour choisir le niveau */
        .btn-group {
            margin-left: 435px;
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .btn {
            text-decoration: none;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-info {
            background-color: #17a2b8;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        
        <a href="statistiques.php" class="active"><i class='bx bx-bar-chart-alt-2'></i> Statistiques</a>
        <a href="gestionMati.php"><i class='bx bxs-book'></i> Matières</a>
        <a href="emploi.php" class="etu"><i class='bx bxs-calendar'></i> Emploi du temps</a>
        <a href="rien1.php"><i class='bx bxs-notepad'></i> Notes</a>
        <a href="index.php" class="logout-btn"><i class='bx bx-log-out'></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Statistiques des matières</h1>

        <!-- Cartes pour afficher le nombre de matières par niveau -->
        <div class="card-container">
            <div class="card card-b1">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class='bx bxs-book'></i> Matières en B1
                    </h5>
                    <p class="card-text"><?php echo $matiereCounts['B1']; ?></p>
                </div>
            </div>
            <div class="card card-b2">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class='bx bxs-book'></i> Matières en B2
                    </h5>
                    <p class="card-text"><?php echo $matiereCounts['B2']; ?></p>
                </div>
            </div>
            <div class="card card-b3">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class='bx bxs-book'></i> Matières en B3
                    </h5>
                    <p class="card-text"><?php echo $matiereCounts['B3']; ?></p>
                </div>
            </div>
        </div>

        <!-- Boutons pour choisir le niveau -->
        <div class="btn-group" role="group" aria-label="Choix du niveau">
            <a href="empolie/index.php?niveau=B1" class="btn btn-primary">
                <i class='bx bxs-graduation'></i> B1
            </a>
            <a href="empolie/index.php?niveau=B2" class="btn btn-success">
                <i class='bx bxs-graduation'></i> B2
            </a>
            <a href="empolie/index.php?niveau=B3" class="btn btn-info">
                <i class='bx bxs-graduation'></i> B3
            </a>
        </div>
    </div>

    <!-- Intégration des icônes Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Intégration de jQuery et Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar a');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            const buttons = document.querySelectorAll('.btn-group .btn');

            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    buttons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>

</html>