<?php
namespace App;

class Admins extends Users {
               
    
    public function __construct(array $data, \PDO $pdo){
        
        
        $this->id = $data['uuid_users'];
        require('../actions/db.php');
        $sql='SELECT * FROM users WHERE uuid_users = '.$data['uuid_users'].'';
        $sth=$db->query($sql);
        $result=$sth->fetch();
        $this->nom = $result['nom'];
        $this->prenom = $result['prenom'];
        $this->pseudo = $result['pseudo'];
        $this->admin = $result['admin'];
        $this->mail = $result['mail'];
        $this->tel = $result['tel'];
        
        $this->date_inscription = $data['date_inscription'];
        $this->date_visite = $data['date_derniere_visite'];
        $this->date_last_creneau = $data['date_dernier_creneau'];
        $this->date_next_creneau = $data['date_prochain_creneau'];
        $this->pdo = $pdo;
        
    }
    
    public function getAllUsers(){
        $sql = 'SELECT * FROM users';
        $sth = $this->pdo->query($sql);
        $results=$sth->fetchAll();
        
        return $results;
    }
    
    public function getOneUser(int $id): array{
        $sql = 'SELECT * FROM users
                INNER JOIN date_users ON users.uuid_users = date_users.id_user
                WHERE uuid_users = ?';
        $sth = $this->pdo->prepare($sql);
        $sth->execute(array($id));
        $results=$sth->fetchAll();
        
        
        return $results;
    }
    
    public function getAllUsersAndDateWaiting(){
        $sql = "SELECT * FROM users
                INNER JOIN date_users ON users.uuid_users = date_users.id_user
                WHERE admin = 0";
        $sth = $this->pdo->query($sql);
        $results = $sth->fetchAll();
        return $results;
    }
    
    public function getAllUsersAndDate(): array{
        $sql = "SELECT * FROM users
                INNER JOIN date_users ON users.uuid_users = date_users.id_user";
        $sth = $this->pdo->query($sql);
        $results = $sth->fetchAll();
        return $results;
    }
    public function updateHabilitation(int $id,int $habilitation){
        $sql = "UPDATE users
                SET admin = ".$habilitation."
                WHERE uuid_users = ".$id."";
        $sth = $this->pdo->query($sql);
    }
}