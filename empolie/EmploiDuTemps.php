<?php
class EmploiDuTemps {
    private $db;
    private $db1;

    public function __construct($db, $db1) {
        $this->db = $db;
        $this->db1 = $db1;
    }

    public function ajouterCours($jour, $horaire, $cours,$niveau) {
        // Ajouter le cours à l'emploi du temps
        $stmt = $this->db1->prepare("INSERT INTO cours".$niveau." (jour, horaire, cours) VALUES (?, ?, ?)");
        $stmt->execute([$jour, $horaire, $cours]);

        // Mettre à jour le nombre de séances pour la matière
        $stmt = $this->db1->prepare("UPDATE matieres SET nombre_seance = nombre_seance - 1 WHERE nom_matiere = ?");
        $stmt->execute([$cours]);
    }

    public function obtenirCours($niveau) {
        $stmt = $this->db1->query("SELECT * FROM cours".$niveau);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
