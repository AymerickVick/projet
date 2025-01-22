<?php
// Connexion à la base de données
$mysqli = new mysqli('127.0.0.1', 'root', 'Keyce-2024', 'etudiants');

if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Nombre total d'étudiants
$query = "SELECT COUNT(*) as total FROM etudiants";
$result = $mysqli->query($query);
$totalStudents = $result->fetch_assoc()['total'];

// Montant total versé
$query = "SELECT SUM(montant) as total FROM versements";
$result = $mysqli->query($query);
$totalPayments = $result->fetch_assoc()['total'];

// Nombre d'étudiants insolvables (à adapter selon vos critères d'insolvabilité)
$query = "SELECT COUNT(*) as total FROM etudiants WHERE statut = 'Insolvable'";
$result = $mysqli->query($query);
$insolventStudents = $result->fetch_assoc()['total'];

// Étudiants par statut
$query = "SELECT statut, COUNT(*) as count FROM etudiants GROUP BY statut ";
$result = $mysqli->query($query);

$statuts = [];
$counts = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $statuts[] = htmlspecialchars($row['statut']);
        $counts[] = (int)$row['count'];
    }
}

// Montants par jour
$query = "SELECT DATE(date_versement) as day, SUM(montant) as total
          FROM versements
          GROUP BY DATE(date_versement)
          ORDER BY day DESC
          LIMIT 30";
$result = $mysqli->query($query);

$days = [];
$totals = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $days[] = htmlspecialchars($row['day']);
        $totals[] = (float)$row['total'];
    }
}

// Montants par mois
$query = "SELECT DATE_FORMAT(date_versement, '%Y-%m') as month, SUM(montant) as total
          FROM versements
          GROUP BY DATE_FORMAT(date_versement, '%Y-%m')
          ORDER BY month DESC
          LIMIT 12";
$result = $mysqli->query($query);

$months = [];
$monthlyTotals = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $months[] = htmlspecialchars($row['month']);
        $monthlyTotals[] = (float)$row['total'];
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #343a40;
            color: #333;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            background-color: #495057;
            color: white;
            height: 95vh;
            width: 220px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
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
            font-size: 1.5rem;
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

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .main-content h1 {
            color: white;
            margin-bottom: 30px;
            font-size: 2em;
            border-bottom: 3px solid #6c83f7;
            padding-bottom: 10px;
            width: fit-content;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 0 20px;
            
        }

        .stat-card {
            background: #495057;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5em;
            color: #6c83f7;
            margin-bottom: 10px;
        }

        .stat-card h3 {
            color: white;
            font-size: 1.8em;
            margin: 10px 0;
        }

        .stat-card p {
            color: white;
            font-size: 1em;
            margin: 0;
        }

        .graph-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .chart-wrapper {
            background: #495057;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            min-height: 400px;
            color: white;
        }

        .chart-wrapper:hover {
            transform: translateY(-5px);
        }

        canvas {
            width: 100% !important;
            height: 350px !important;
            margin: auto;
        }

        @media (max-width: 1200px) {
            .graph-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                padding: 15px;
            }

            .main-content {
                padding: 15px;
            }

            .graph-container {
                padding: 10px;
            }

            .chart-wrapper {
                min-height: 350px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2><i class='bx bxs-dashboard'></i> Menu</h2>
        <a href="gestionEtu.php">
            <i class='bx bxs-user-detail'></i>
            Étudiants
        </a>
        <a href="gestionVer.php">
            <i class='bx bx-money'></i>
            Versements
        </a>
        <a href="gestionEnsei.php">
            <i class='bx bxs-user-badge'></i>
            Enseignants
        </a>
        <a class="etu" href="statistiques.php">
            <i class='bx bx-bar-chart-alt-2'></i>
            Statistiques
        </a>
        <a href="gestionMati.php">
            <i class='bx bxs-book'></i>
            Matières
        </a>
        <a href="emploi.php">
            <i class='bx bxs-calendar'></i>
            Emploi du temps
        </a>

        <a href="rien1.php">
            <i class='bx bxs-notepad'></i>
            Notes
        </a>
        <a href="logout.php" class="logout-btn">
            <i class='bx bx-log-out'></i>
            Déconnexion
        </a>
    </div>

    <div class="main-content">
        <h1><i class='bx bxs-chart'></i> Statistiques</h1>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <i class='bx bxs-user-detail'></i>
                <h3><?php echo number_format($totalStudents, 0, ',', ' '); ?></h3>
                <p>Étudiants inscrits</p>
            </div>

            <div class="stat-card">
                <i class='bx bx-euro'></i>
                <h3><?php echo number_format($totalPayments, 0, ',', ' '); ?> €</h3>
                <p>Montant total versé</p>
            </div>

            <div class="stat-card">
                <i class='bx bx-error-circle'></i>
                <h3><?php echo number_format($insolventStudents, 0, ',', ' '); ?></h3>
                <p>Étudiants insolvables</p>
            </div>
        </div>

        <div class="graph-container">
            <div class="chart-wrapper">
                <canvas id="studentsByStatusChart"></canvas>
            </div>
            <div class="chart-wrapper">
                <canvas id="paymentsByDayChart"></canvas>
            </div>
            <div class="chart-wrapper">
                <canvas id="paymentsByMonthChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données PHP vers JavaScript
        const studentStatusLabels = <?php echo json_encode($statuts); ?>;
        const studentStatusData = <?php echo json_encode($counts); ?>;
        const paymentDaysLabels = <?php echo json_encode($days); ?>;
        const paymentTotalsData = <?php echo json_encode($totals); ?>;
        const paymentMonthsLabels = <?php echo json_encode($months); ?>;
        const paymentMonthlyTotalsData = <?php echo json_encode($monthlyTotals); ?>;

        // Configuration des couleurs
        const colors = {
            pie: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
            bar1: {
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
            },
            bar2: {
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderColor: 'rgba(153, 102, 255, 1)',
            }
        };

        // Configuration commune pour les graphiques
        const commonOptions = {
            color: 'white',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                color: 'white',
                legend: {
                    position: 'bottom',
                    color: 'white',
                    labels: {
                        padding: 20,
                        color: 'white',
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    color: 'white',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        bottom: 30
                        
                    }
                }
            }
        };

        // Fonction pour formater les montants
        const formatMoney = (value) => {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        };

        // Graphique circulaire - Étudiants par statut
        new Chart(document.getElementById('studentsByStatusChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: studentStatusLabels,
                datasets: [{
                    data: studentStatusData,
                    backgroundColor: colors.pie,
                    borderWidth: 2,
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Répartition des étudiants par statut'
                    }
                }
            }
        });

        // Graphique en barres - Versements par jour
        new Chart(document.getElementById('paymentsByDayChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentDaysLabels.map(date => new Date(date).toLocaleDateString('fr-FR')),
                datasets: [{
                    label: 'Montants versés',
                    data: paymentTotalsData,
                    backgroundColor: colors.bar1.backgroundColor,
                    borderColor: colors.bar1.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Montants versés par jour (30 derniers jours)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => formatMoney(value)
                        }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function(context) {
                            return formatMoney(context.parsed.y);
                        }
                    }
                }
            }
        });

        // Graphique en barres - Versements par mois
        new Chart(document.getElementById('paymentsByMonthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: paymentMonthsLabels.map(month => {
                    const [year, monthNum] = month.split('-');
                    return new Date(year, monthNum - 1).toLocaleDateString('fr-FR', {
                        month: 'long',
                        year: 'numeric'
                    });
                }),
                datasets: [{
                    label: 'Montants versés',
                    data: paymentMonthlyTotalsData,
                    backgroundColor: colors.bar2.backgroundColor,
                    borderColor: colors.bar2.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Montants versés par mois (12 derniers mois)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => formatMoney(value)
                        }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function(context) {
                            return formatMoney(context.parsed.y);
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>