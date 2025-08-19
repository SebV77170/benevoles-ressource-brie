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
    
    public function getId(){
        return $this->id;
    }
    
    public function getNom(){
        return $this->nom;
    }
    
    public function getPrenom(){
        return $this->prenom;
    }
    
    public function getPseudo(){
        return $this->pseudo;
    }
    
    public function getAdmin(){
        return $this->admin;
    }
    
    public function getMail(){
        return $this->mail;
    }
    
    public function getTel(){
        return $this->tel;
    }
    
    public function getInscription(){
        return $this->date_inscription;
    }
    
    public function getLastVisit(){
        return $this->date_visite;
    }
    
    public function getLastCreneau(){
        return $this->date_creneau;
    }
    
    
    
    public function getCreneauUser(){
        $id = $this -> getId();
        $sql =
                'SELECT * FROM inscription_creneau 
                INNER JOIN events ON inscription_creneau.id_event = events.id
                WHERE inscription_creneau.id_user = '.$id.' ORDER BY events.start ASC';
        $sth = $this -> pdo -> query($sql);
        $results = $sth -> fetchAll();
        
        return $results;
    }
    
    //Vérifie si un utilisateur est déjà inscrit sur un créneau.
    
    public function CheckIfCreneauExist($id_creneau) : bool{
        
        $id = $this -> getId();
        $id_users = $this ->id;
        $sql = 'SELECT * FROM inscription_creneau WHERE id_event = '.$id_creneau.' AND id_user = '.$id_users.'';
        $sth = $this ->pdo->query($sql);
        $results = $sth ->fetchAll();
        if(count($results) > 0){
            $answer = TRUE;
        }else{
            $answer = FALSE;
        }
        
        return $answer;
    }
    
    //Insere les creneaux dans la db inscription_creneau et retourne le nombre créneaux ajoutés
    
    public function insertCreneauUser(array $data) : int{
        $id = $this ->getId();
        
        $fonction = 'N/A';
        $collect =[];
        foreach($data as $k=>$v){
            
            $sql = 'INSERT INTO inscription_creneau (id_user, id_event, fonction) VALUES (?,?,?)';
            $sth = $this ->pdo->prepare($sql);
            $result = $sth->execute(array($id, $v, $fonction));
            $result = $sth ->rowCount();
            $collect[] = $result;
            
        }
        
        return count($collect);
    }
    
    //Supprime les creneaux dans la db inscription_creneau et retourne le nombre créneaux supprimés
    
    public function deleteCreneauUser(array $data) :int {
        $id = $this ->getId();
        
        $collect =[];
        foreach($data as $k=>$v){
            
            $sql = 'DELETE FROM inscription_creneau WHERE id_user = ? AND id_event = ? ';
            $sth = $this ->pdo->prepare($sql);
            $result = $sth->execute(array($id, $v));
            $result = $sth ->rowCount();
            $collect[] = $result;
            
        }
        
        return count($collect);
    }
    
    public function getAllUsersByCreneau(array $data):array{
        $collect=[];
        foreach($data as $v){
        $sql = 'SELECT events.start, events.end, inscription_creneau.id_user, inscription_creneau.id_event, inscription_creneau.fonction, users.prenom, users.nom, users.pseudo FROM inscription_creneau
                INNER JOIN users ON inscription_creneau.id_user = users.uuid_users
                INNER JOIN events ON inscription_creneau.id_event = events.id
                WHERE inscription_creneau.id_event = ? ';
        $sth =$this->pdo->prepare($sql);
        $sth ->execute(array($v['id']));
        $results = $sth->fetchAll();
        $collect[] = $results;
        }
        
        return $collect;
    }
    
    public function getAllUsersByCreneau2(array $data):array{
        $collect=[];
        foreach($data as $v){
        $sql = 'SELECT events.start, events.end, events.id, inscription_creneau.id_user, inscription_creneau.id_event, inscription_creneau.fonction FROM events
                LEFT OUTER JOIN inscription_creneau ON events.id = inscription_creneau.id_event
                WHERE events.id = ? ';
        $sth =$this->pdo->prepare($sql);
        $sth ->execute(array($v['id']));
        $results = $sth->fetchAll();
        $collect[] = $results;
        }
        
        return $collect;
    }
    
    public function countAllUsersByCreneau(array $data):array{
        $collect=[];
        foreach($data as $k=>$v){
            
            foreach($v as $n){
                $debut=new \DateTime(''.$n['start'].'');
                $fin=new \DateTime(''.$n['end'].'');
                $debutstring = $debut ->format('G:i');
                $finstring = $fin ->format('G:i');
                $date = ''.$debutstring.' '.$finstring.'';
                
                if($n['id_user']){
                    $collect[''.$date.'']=count($v);
                }else{
                    $collect[''.$date.'']=0;
                }
            } 
        }
        return $collect;
    }
    
    public function checkIfFunctionExists(int $id):bool{
        $sql='SELECT * FROM inscription_creneau WHERE id_inscription='.$id.'';
        $sth=$this->pdo->query($sql);
        $results=$sth->fetch();
        if($results['fonction']==='N/A'):
            $answer = FALSE;
        else :
            $answer = TRUE;
        endif;
        return $answer;
    }
    
    public function getAllFunctions(){
        $sql='SELECT fonction FROM fonction';
        $sth=$this->pdo->query($sql);
        $results=$sth->fetchAll();
        return $results;
    }
    
    public function updateFunction(int $id, string $fonction){
        $sql='UPDATE inscription_creneau SET fonction = "'.$fonction.'" WHERE id_inscription = '.$id.'';
        $sth=$this->pdo->query($sql);
    }
    
    public function updateMailUser($mail){
        $mail=htmlspecialchars($mail);
        $sql='UPDATE users SET mail = "'.$mail.'" WHERE uuid_users = ?';
        $sth=$this->pdo->prepare($sql);
        $sth->execute(array($this->id));
    }

    public function updateTelUser($tel){
        $tel=htmlspecialchars($tel);
        $sql='UPDATE users SET tel = "'.$tel.'" WHERE uuid_users = ?';
        $sth=$this->pdo->prepare($sql);
        $sth->execute(array($this->id));
    }
     
    public function getNbtotalbenevolat($startDate, $endDate) {
        $sql = 'SELECT users.nom, users.prenom, users.pseudo, 
                       SUM(TIMESTAMPDIFF(MINUTE, events.start, events.end)) DIV 60 as Heuretotal
                FROM events
                JOIN inscription_creneau ON inscription_creneau.id_event = events.id
                JOIN users ON users.uuid_users = inscription_creneau.id_user
                WHERE events.start >= :startDate AND events.end <= :endDate
                GROUP BY inscription_creneau.id_user
                ORDER BY heuretotal DESC' ;
        
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':startDate', $startDate);
        $sth->bindParam(':endDate', $endDate);
        $sth->execute();
        $result = $sth->fetchAll();

        // Calculer la somme totale des heures
        $totalHeures = 0;
        foreach ($result as $row) {
            $totalHeures += $row['Heuretotal'];
        }
    
    return ['result' => $result, 'totalHeures' => $totalHeures];
    }
         

}