<?php
namespace App;

class Conges {
    private $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function request(int $userId, string $start, string $end): void {
        $sql = "INSERT INTO conges (user_id, date_debut, date_fin) VALUES (:user, :start, :end)";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([
            ':user' => $userId,
            ':start' => $start,
            ':end' => $end
        ]);
    }

    public function getByUser(int $userId): array {
        $sth = $this->pdo->prepare("SELECT * FROM conges WHERE user_id = ? ORDER BY date_debut DESC");
        $sth->execute([$userId]);
        return $sth->fetchAll();
    }

    public function getPending(): array {
        $sql = "SELECT conges.*, users.prenom, users.nom FROM conges JOIN users ON users.id = conges.user_id WHERE status = 'pending' ORDER BY date_debut";
        $sth = $this->pdo->query($sql);
        return $sth->fetchAll();
    }

    public function updateStatus(int $id, string $status): void {
        $sth = $this->pdo->prepare("UPDATE conges SET status = :status WHERE id = :id");
        $sth->execute([':status' => $status, ':id' => $id]);
    }

    public function getMonthlySummary(): array {
        $sql = "SELECT users.prenom, users.nom, DATE_FORMAT(date_debut, '%Y-%m') AS mois,\n                       SUM(DATEDIFF(date_fin, date_debut) + 1) AS nb_jours\n                FROM conges\n                JOIN users ON users.id = conges.user_id\n                WHERE status = 'approved'\n                GROUP BY users.id, mois\n                ORDER BY mois DESC";
        $sth = $this->pdo->query($sql);
        return $sth->fetchAll();
    }
}
