<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            background-color: #475be8;
            color: white;
            height: 745px;
            width: 180px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .sidebar h2 {
            margin: 0 0 20px 0;
            font-size: 1.5em;
            text-align: center;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar a:hover {
            background-color: #6c83f7;
            transform: translateY(-2px);
        }

        .sidebar .etu {
            background-color: #6c83f7;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            overflow-y: auto;
        }



        /* Ajoutez ou modifiez ces styles dans votre CSS existant */
        .graph-container {
            display: flex;
            flex-wrap: nowrap;
            /* Empêche le passage des éléments à une nouvelle ligne */
            justify-content: space-between;
            gap: 10px;
            overflow-x: auto;
            /* Ajoute un défilement horizontal si nécessaire */
            /* padding-bottom: 10px;
            padding: 5px; */
        }

        canvas {
            flex: 1 1 auto;
            /* Permet aux graphiques de s'adapter à l'espace disponible */
            max-width: 300px;
            /* Limite la largeur maximale */
            height: 500px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }



        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                text-align: center;
            }

            .main-content {
                padding: 10px;
            }

            canvas {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="gestionEtu.php"><img src="uploads/student.gif" alt="Étudiants">Étudiants</a>
        <a href="gestionVer.php"><img src="uploads/alms.gif" alt="Versements">Versements</a>
        <a href="gestionEnsei.php"><img src="uploads/graduated.png" alt="Enseignants">Enseignants</a>
        <a class="etu" href="statistiques.php"><img src="uploads/analytics.gif" alt="">Statistiques</a>
        <a href="gestionMati.php"><img src="uploads/analytics.gif" alt="">Matieres</a>
        <a href="rien1.php"><img src="uploads/graduated (1).png" alt="">Notes</a>
        <a href="logout.php">Déconnexion</a>
    </div>

    <div class="main-content">
        <h1>Statistiques</h1>
        <div class="graph-container">
            <canvas id="studentsByStatusChart"></canvas>
            <canvas id="paymentsByDayChart"></canvas>
            <canvas id="paymentsByMonthChart"></canvas>
        </div>
    </div>

    <?php
    // Connexion à la base de données
    $mysqli = new mysqli('127.0.0.1', 'root', 'Keyce-2024', 'etudiants');

    if ($mysqli->connect_error) {
        die("Erreur de connexion : " . $mysqli->connect_error);
    }

    // Étudiants par statut
    $query = "SELECT statut, COUNT(*) as count FROM etudiants GROUP BY statut";
    $result = $mysqli->query($query);

    $statuts = [];
    $counts = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $statuts[] = htmlspecialchars($row['statut']);
            $counts[] = (int)$row['count'];
        }
    }

    echo "<script>
        const studentStatusLabels = " . json_encode($statuts) . ";
        const studentStatusData = " . json_encode($counts) . ";
    </script>";

    // Montants par jour
    $query = "SELECT DATE(date_versement) as day, SUM(montant) as total 
              FROM versements 
              GROUP BY DATE(date_versement)";
    $result = $mysqli->query($query);

    $days = [];
    $totals = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $days[] = htmlspecialchars($row['day']);
            $totals[] = (float)$row['total'];
        }
    }

    echo "<script>
        const paymentDaysLabels = " . json_encode($days) . ";
        const paymentTotalsData = " . json_encode($totals) . ";
    </script>";

    // Montants par mois
    $query = "SELECT DATE_FORMAT(date_versement, '%Y-%m') as month, SUM(montant) as total 
              FROM versements 
              GROUP BY DATE_FORMAT(date_versement, '%Y-%m')";
    $result = $mysqli->query($query);

    $months = [];
    $monthlyTotals = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $months[] = htmlspecialchars($row['month']);
            $monthlyTotals[] = (float)$row['total'];
        }
    }

    echo "<script>
        const paymentMonthsLabels = " . json_encode($months) . ";
        const paymentMonthlyTotalsData = " . json_encode($monthlyTotals) . ";
    </script>";

    $mysqli->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('studentsByStatusChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: studentStatusLabels,
                datasets: [{
                    data: studentStatusData,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]
            }
        });

        new Chart(document.getElementById('paymentsByDayChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentDaysLabels,
                datasets: [{
                    label: 'Montants versés par jour',
                    data: paymentTotalsData,
                    backgroundColor: '#4CAF50',
                }]
            }
        });

        new Chart(document.getElementById('paymentsByMonthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentMonthsLabels,
                datasets: [{
                    label: 'Montants versés par mois',
                    data: paymentMonthlyTotalsData,
                    backgroundColor: '#FF5733',
                }]
            }
        });
    </script>
</body>

</html>