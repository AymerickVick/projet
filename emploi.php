<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Notes</title>
    <!-- Intégration de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #475be8;
            --secondary-color: #6c83f7;
            --background-color: #f4f4f9;
            --text-color: #333;
            --white: #ffffff;
            --success-color: #28a745;
            --info-color: #17a2b8;
        }

        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: var(--primary-color);
            color: var(--white);
            width: 250px;
            padding: 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            border-radius: 0 10px 10px 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar a {
            text-decoration: none;
            color: var(--white);
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--secondary-color);
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        h1 {
            margin-bottom: 30px;
            color: var(--primary-color);
        }

        .btn-group {
            display: flex;
            gap: 20px;
        }

        .btn {
            text-decoration: none;
            color: var(--white);
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
            background-color: var(--primary-color);
        }

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-info {
            background-color: var(--info-color);
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <h2><i class="fas fa-bars"></i> Menu</h2>
            <nav>
                <a href="gestionEtu.php"><i class="fas fa-user-graduate"></i> Étudiants</a>
                <a href="gestionVer.php"><i class="fas fa-money-bill-wave"></i> Versements</a>
                <a href="gestionEnsei.php"><i class="fas fa-chalkboard-teacher"></i> Enseignants</a>
                <a href="emploi.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Emploie</a>
                <a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
                <a href="gestionMati.php"><i class="fas fa-book"></i> Matières</a>
                <a href="rien1.php"><i class="fas fa-star"></i> Notes</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </nav>
        </aside>

        <main class="main-content">
            <h1>Choisissez le niveau</h1>
            <div class="btn-group" role="group" aria-label="Choix du niveau">
                <a href="empolie/index.php?niveau=B1" class="btn btn-primary">
                    <i class="fas fa-graduation-cap"></i> B1
                </a>
                <a href="empolie/index.php?niveau=B2" class="btn btn-success">
                    <i class="fas fa-graduation-cap"></i> B2
                </a>
                <a href="empolie/index.php?niveau=B3" class="btn btn-info">
                    <i class="fas fa-graduation-cap"></i> B3
                </a>
            </div>
        </main>
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