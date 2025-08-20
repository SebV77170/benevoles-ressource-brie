<?php
namespace App;

class Users {

    protected $id;
    protected $nom;
    protected $prenom;
    protected $pseudo;
    protected $admin;
    protected $mail;
    protected $tel;
    protected $pdo;
    protected $date_inscription;
    protected $date_visite;
    protected $date_last_creneau;
    protected $date_next_creneau;

    public function __construct(array $data, \PDO $pdo){
        // on garde l'UUID passé par $data
        $this->id = $data['uuid_user'];
        $this->pdo = $pdo;

        // Récupération de l'utilisateur en REQUÊTE PRÉPARÉE
        $sql = 'SELECT * FROM users WHERE uuid_user = :uuid';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([':uuid' => $this->id]);
        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        // Hydratation (avec valeurs par défaut si absent)
        $this->nom    = $result['nom']    ?? null;
        $this->prenom = $result['prenom'] ?? null;
        $this->pseudo = $result['pseudo'] ?? null;
        $this->admin  = $result['admin']  ?? 0;
        $this->mail   = $result['mail']   ?? null;
        $this->tel    = $result['tel']    ?? null;

        $this->date_inscription   = $data['date_inscription']      ?? null;
        $this->date_visite        = $data['date_derniere_visite']  ?? null;
        $this->date_last_creneau  = $data['date_dernier_creneau']  ?? null;
        $this->date_next_creneau  = $data['date_prochain_creneau'] ?? null;
    }

    public function getId(){ return $this->id; }
    public function getNom(){ return $this->nom; }
    public function getPrenom(){ return $this->prenom; }
    public function getPseudo(){ return $this->pseudo; }
    public function getAdmin(){ return $this->admin; }
    public function getMail(){ return $this->mail; }
    public function getTel(){ return $this->tel; }
    public function getInscription(){ return $this->date_inscription; }
    public function getLastVisit(){ return $this->date_visite; }

    // bug fixé (propriété correcte)
    public function getLastCreneau(){ return $this->date_last_creneau; }

    public function getCreneauUser(){
        $sql = 'SELECT * FROM inscription_creneau 
                INNER JOIN events ON inscription_creneau.id_event = events.id
                WHERE inscription_creneau.id_user = :id
                ORDER BY events.start ASC';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([':id' => $this->id]);
        return $sth->fetchAll();
    }

    // Vérifie si un utilisateur est déjà inscrit sur un créneau.
    public function CheckIfCreneauExist($id_creneau) : bool{
        $sql = 'SELECT 1 FROM inscription_creneau
                WHERE id_event = :idevent AND id_user = :iduser
                LIMIT 1';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([':idevent' => $id_creneau, ':iduser' => $this->id]);
        return (bool) $sth->fetchColumn();
    }

    // Insère des créneaux
    public function insertCreneauUser(array $data) : int{
        $fonction = 'N/A';
        $sql = 'INSERT INTO inscription_creneau (id_user, id_event, fonction) VALUES (:id, :event, :fonction)';
        $sth = $this->pdo->prepare($sql);

        $count = 0;
        foreach($data as $v){
            $sth->execute([':id' => $this->id, ':event' => $v, ':fonction' => $fonction]);
            $count += $sth->rowCount();
        }
        return $count;
    }

    // Supprime des créneaux
    public function deleteCreneauUser(array $data) : int {
        $sql = 'DELETE FROM inscription_creneau WHERE id_user = :id AND id_event = :event';
        $sth = $this->pdo->prepare($sql);

        $count = 0;
        foreach($data as $v){
            $sth->execute([':id' => $this->id, ':event' => $v]);
            $count += $sth->rowCount();
        }
        return $count;
    }

    public function getAllUsersByCreneau(array $data): array{
        $sql = 'SELECT events.start, events.end, inscription_creneau.id_user, inscription_creneau.id_event, inscription_creneau.fonction,
                       users.prenom, users.nom, users.pseudo
                FROM inscription_creneau
                INNER JOIN users  ON inscription_creneau.id_user = users.uuid_user
                INNER JOIN events ON inscription_creneau.id_event = events.id
                WHERE inscription_creneau.id_event = :event';
        $sth = $this->pdo->prepare($sql);

        $collect=[];
        foreach($data as $v){
            $sth->execute([':event' => $v['id']]);
            $collect[] = $sth->fetchAll();
        }
        return $collect;
    }

    public function getAllUsersByCreneau2(array $data): array{
        $sql = 'SELECT events.start, events.end, events.id,
                       inscription_creneau.id_user, inscription_creneau.id_event, inscription_creneau.fonction
                FROM events
                LEFT OUTER JOIN inscription_creneau ON events.id = inscription_creneau.id_event
                WHERE events.id = :event';
        $sth = $this->pdo->prepare($sql);

        $collect=[];
        foreach($data as $v){
            $sth->execute([':event' => $v['id']]);
            $collect[] = $sth->fetchAll();
        }
        return $collect;
    }

    public function countAllUsersByCreneau(array $data): array{
        $collect=[];
        foreach($data as $k=>$v){
            foreach($v as $n){
                $debut = new \DateTime($n['start']);
                $fin   = new \DateTime($n['end']);
                $date  = $debut->format('G:i').' '.$fin->format('G:i');
                $collect[$date] = $n['id_user'] ? count($v) : 0;
            }
        }
        return $collect;
    }

    public function checkIfFunctionExists(int $id): bool{
        $sql = 'SELECT fonction FROM inscription_creneau WHERE id_inscription = :id LIMIT 1';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([':id' => $id]);
        $row = $sth->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return false;
        return $row['fonction'] !== 'N/A';
    }

    public function getAllFunctions(){
        $sql='SELECT fonction FROM fonction';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function updateFunction(int $id, string $fonction){
        $sql='UPDATE inscription_creneau SET fonction = :fonction WHERE id_inscription = :id';
        $sth=$this->pdo->prepare($sql);
        $sth->execute([':fonction' => $fonction, ':id' => $id]);
    }

    public function updateMailUser($mail){
        $mail = htmlspecialchars($mail);
        $sql  = 'UPDATE users SET mail = ? WHERE uuid_user = ?';
        $sth  = $this->pdo->prepare($sql);
        $sth->execute([$mail, $this->id]);
        $this->mail = $mail;
    }

    public function updateTelUser($tel){
        $tel = htmlspecialchars($tel);
        $sql = 'UPDATE users SET tel = ? WHERE uuid_user = ?';
        $sth = $this->pdo->prepare($sql);
        $sth->execute([$tel, $this->id]);
        $this->tel = $tel;
    }

    public function getNbtotalbenevolat($startDate, $endDate) {
        $sql = 'SELECT users.nom, users.prenom, users.pseudo,
                       SUM(TIMESTAMPDIFF(MINUTE, events.start, events.end)) DIV 60 AS Heuretotal
                FROM events
                JOIN inscription_creneau ON inscription_creneau.id_event = events.id
                JOIN users ON users.uuid_user = inscription_creneau.id_user
                WHERE events.start >= :startDate AND events.end <= :endDate
                GROUP BY inscription_creneau.id_user
                ORDER BY Heuretotal DESC';
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':startDate', $startDate);
        $sth->bindParam(':endDate', $endDate);
        $sth->execute();
        $result = $sth->fetchAll();

        $totalHeures = 0;
        foreach ($result as $row) {
            $totalHeures += (int)$row['Heuretotal'];
        }
        return ['result' => $result, 'totalHeures' => $totalHeures];
    }
}
