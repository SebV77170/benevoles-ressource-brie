<?php

namespace Calendar;

class Creneaux {
    
    private $pdo;
    
    public $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    private $months = [
        'January'=>'Janvier',
        'February'=>'Février',
        'March'=>'Mars',
        'April'=>'Avril',
        'May'=>'Mai',
        'June'=>'Juin',
        'July'=>'Juillet',
        'August'=>'Aout',
        'September'=>'Septembre',
        'October'=>'Octobre',
        'November'=>'Novembre',
        'December'=>'Décembre'
    ];
    
    public $timezone;

    public function __construct(\PDO $pdo, $timezone)
    {
        $this->pdo = $pdo;
        $this->timezone = new \DateTimeZone($timezone);
    }
    
//    Renvoi une liste de creneaux pour les prochains 6 mois, tous les jours définis, avec un horaire de debut et un de fin

    public function findNewCreneau(string $day, string $timestart, string $timeend) : array{
        
        $timezone = $this->timezone;

        $datestart = new \DateTime('now',$timezone);
        $dateend = new \DateTime(''.$datestart->format('Y-m-d G:i').'+ 6 months',$timezone);
        
        $premierjourdebut = new \DateTime('next '.$day.' '.$timestart.'', $timezone);
        $numsemainedebut = (int)($premierjourdebut -> format('W'));
        if(!is_int($numsemainedebut/2)){
            $premierjourdebut;
        }else{
            $premierjourdebut = new \DateTime('next '.$day.' + 7 days '.$timestart.'', $timezone);
        }
        
        $premierjourfin = new \DateTime('next '.$day.' '.$timeend.'', $timezone);
        $numsemainefin = (int)($premierjourfin -> format('W'));
        if(!is_int($numsemainefin/2)){
            $premierjourfin;
        }else{
            $premierjourfin = new \DateTime('next '.$day.' + 7 days '.$timeend.'', $timezone);
        }
        
        $day = [[$premierjourdebut, $premierjourfin]];
        $i=0;
        While($day[$i][0]< $dateend){
        $k = ($i+1) * 14;
        $otherdayslot = [new \DateTime(''.$day[0][0]->format('Y-m-d G:i').'+ '.$k.' days', $timezone),new \DateTime(''.$day[0][1]->format('Y-m-d G:i').'+ '.$k.' days', $timezone)];
        $day[$i+1] = $otherdayslot;
        $i++;
        }
        
        return $day;
    
}
    
    public function CheckIfCreneauExist(string $timestart, string $timeend){
        
        $sql = 'SELECT * FROM events WHERE start = ? AND end = ? ';
        $sth = $this->pdo->prepare($sql);
        $sth ->execute(array($timestart, $timeend));
        
        $results = $sth ->fetchAll();
        $count = count($results);
        
        if($count>0){
            $answer = TRUE;
            }else{
                $answer=FALSE;
            }
            return $answer;
        
    }
    
//Insere les créneau dans la db

    public function insertCreneau(int $cat, string $day, string $timestart, string $timeend){
        
        $data = $this ->findNewCreneau($day, $timestart, $timeend);
        
        $name = 'Ouverture standard';
        $description = '';
        foreach($data as $v){
            
            $start = $v[0]->format('Y/m/d G:i');
            $end = $v[1]->format('Y/m/d G:i');
            
            if(!($this ->CheckIfCreneauExist($start,$end))){
            $sql1 = 'INSERT into events (cat_creneau, name, description, start, end) VALUES (?,?,?,?,?)';
            $sth1 = $this ->pdo-> prepare($sql1);
            $sth1 -> execute(array($cat, $name, $description, $start, $end));
            }
        }   
    }
    
    //Renvoie un tableau de tous les creneaux dans la db events ordonnés par ordre chronologique.
    
    public function getAllCreneauFromDB(){
        $sql = 'SELECT * FROM events WHERE cat_creneau=1 ORDER BY start';
        $sth = $this -> pdo -> query($sql);
        $result = $sth -> fetchAll();
        
        return $result;
    }
    
    //Permet de prendre les valeurs de la DB en Y-m-D G:i et de renvoyer la date en format jour-mois(en français)
    
    public function explodeDateInDb(string $datetime){
        
        $timezone = $this->timezone;
        $date = explode(' ', $datetime);
        $jour = $date[0];
        $jour = new \DateTime(''.$jour.'', $timezone);
        $stringjour = $jour -> format('j-F');
        $stringjourexplode = explode('-', $stringjour);
        foreach($this->months as $k=>$v){
            if($k==$stringjourexplode[1]){
                $stringjourexplode[1]=$v;
            }
        }
        $stringjourfrench=implode('-',$stringjourexplode);
        
        return $stringjourfrench;
    }
    
    //Permet de prendre les valeurs de la DB en Y-m-D G:i et de renvoyer l'heure en format G:i
    
    public function explodeHeureInDb(string $datetime){
        $timezone = $this->timezone;
        $date = explode(' ', $datetime);
        $heure = $date[1];
        $heure = new \DateTime(''.$heure.'', $timezone);
        $stringheure = $heure -> format('G:i');
        
        return $stringheure;
    }
    
//    Renvoie un booléen pour savoir si le jour en question est bien un lundi ou un autre jour.
    
    private function is_day(string $stringday /*jour en toute lettre avec la premiere lettre en majuscule*/, string $datetime /*jour et heure sortant de la db*/): bool{
        $timezone = $this->timezone;
        $date = explode(' ', $datetime);
        $jour = $date[0];
        $jour = new \DateTime(''.$jour.'', $timezone);
        $stringjour = $jour -> format('l');
        
        
        if($stringday === $stringjour){
             $answer= true;
        }else{
            $answer = false;
        }
        
        return $answer;
        
    }
    
    public function getEventsBetween (\DateTime $start, \DateTime $end, int $cat_creneau): array {
        $sql = "SELECT * FROM events WHERE start BETWEEN '{$start->format('Y-m-d 00:00:00')}' AND '{$end->format('Y-m-d 23:59:59')}' AND cat_creneau = ".$cat_creneau." ORDER BY start ASC";
        $statement = $this->pdo->query($sql);
        $results = $statement->fetchAll();
        return $results;
    }
    
    // Renvoie un tableau avec tous les creneaux qui concernent un jour en particulier sur les 3 prochains mois.
    
    public function getCreneauByDay(string $stringday){
        $timezone = $this->timezone;
        $start = new \DateTime('now', $timezone);
        $end = new \DateTime(''.$start->format('Y-m-d').' + 3 months', $timezone);
        
        $allCreneau = $this->getEventsBetween($start, $end, 1);
        $creneauByDay = [];
        foreach($allCreneau as $v){
            if($this->is_day($stringday, $v['start'])){
                $creneauByDay[]=$v;
            }
        }
        return $creneauByDay;
    }
    
    public function checkIfCreneauOutOfDate(string $datetime):bool{
        
        $timezone = $this->timezone;
        
        $jour = new \DateTime(''.$datetime.'', $timezone);
        
        if($jour <= new \DateTime('now') ){
            $answer = TRUE;
        }else{
            $answer = FALSE;
        }
        return $answer;
    }
    
    
    
}