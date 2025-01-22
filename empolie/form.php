<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Emplois du Temps</title>
</head>
<body>
    <h1>Ajouter un Cours</h1>
    <form action="ajouter_cours.php" method="post">
        <table border="1">
            <tr>
                <th>Horaire</th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
            </tr>
            <tr>
                <td>Matin</td>
                <td><input type="text" name="lundi_matin"></td>
                <td><input type="text" name="mardi_matin"></td>
                <td><input type="text" name="mercredi_matin"></td>
                <td><input type="text" name="jeudi_matin"></td>
                <td><input type="text" name="vendredi_matin"></td>
                <td><input type="text" name="samedi_matin"></td>
            </tr>
            <tr>
                <td>Soir</td>
                <td><input type="text" name="lundi_soir"></td>
                <td><input type="text" name="mardi_soir"></td>
                <td><input type="text" name="mercredi_soir"></td>
                <td><input type="text" name="jeudi_soir"></td>
                <td><input type="text" name="vendredi_soir"></td>
                <td><input type="text" name="samedi_soir"></td>
            </tr>
        </table>
        <button type="submit">Ajouter</button>
    </form>

    <h1>Emploi du Temps</h1>
    <table border="1">
        <tr>
            <th>Horaire</th>
            <th>Lundi</th>
            <th>Mardi</th>
            <th>Mercredi</th>
            <th>Jeudi</th>
            <th>Vendredi</th>
            <th>Samedi</th>
        </tr>
        <tr>
            <td>Matin</td>
            <td><?php echo $cours['lundi_matin']; ?></td>
            <td><?php echo $cours['mardi_matin']; ?></td>
            <td><?php echo $cours['mercredi_matin']; ?></td>
            <td><?php echo $cours['jeudi_matin']; ?></td>
            <td><?php echo $cours['vendredi_matin']; ?></td>
            <td><?php echo $cours['samedi_matin']; ?></td>
        </tr>
        <tr>
            <td>Soir</td>
            <td><?php echo $cours['lundi_soir']; ?></td>
            <td><?php echo $cours['mardi_soir']; ?></td>
            <td><?php echo $cours['mercredi_soir']; ?></td>
            <td><?php echo $cours['jeudi_soir']; ?></td>
            <td><?php echo $cours['vendredi_soir']; ?></td>
            <td><?php echo $cours['samedi_soir']; ?></td>
        </tr>
    </table>
</body>
</html>
