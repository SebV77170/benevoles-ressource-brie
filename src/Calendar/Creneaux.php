<?php

namespace Calendar;

class Creneaux {
    
    private $pdo;
    
    public $days = ['Lundi'=>'Monday', 'Mardi'=>'Tuesday', 'Mercredi'=>'Wednesday', 'Jeudi'=>'Thursday', 'Vendredi'=>'Friday', 'Samedi'=>'Saturday', 'Dimanche'=>'Sunday'];

    public $hours = [
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
    ];
    
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
    
//    Renvoi une liste de creneaux pour la prochaine période définie dans la variable $timing, tous les jours définis, avec un horaire de debut et un de fin

    public function findNewCreneau(string $day, string $timestart, string $timeend, string $timing, string $frequency) : array{
        
        $timezone = $this->timezone;

        $datestart = new \DateTime('now',$timezone);
        $dateend = new \DateTime(''.$datestart->format('Y-m-d G:i').'+ '.$timing.'',$timezone);
        
        $premierjourdebut = new \DateTime('next '.$day.' '.$timestart.'', $timezone);
        $premierjourfin = new \DateTime('next '.$day.' '.$timeend.'', $timezone);

        if($frequency=='everyotherweeksimpaire'):
            $numsemainedebut = (int)($premierjourdebut -> format('W'));
            if(!is_int($numsemainedebut/2)){
                $premierjourdebut;
            }else{
                $premierjourdebut = new \DateTime('next '.$day.' + 7 days '.$timestart.'', $timezone);
            }
            
            
            $numsemainefin = (int)($premierjourfin -> format('W'));
            if(!is_int($numsemainefin/2)){
                $premierjourfin;
            }else{
                $premierjourfin = new \DateTime('next '.$day.' + 7 days '.$timeend.'', $timezone);
            }
        elseif($frequency=='everyotherweekspaire'):
            $numsemainedebut = (int)($premierjourdebut -> format('W'));
            if(is_int($numsemainedebut/2)){
                $premierjourdebut;
            }else{
                $premierjourdebut = new \DateTime('next '.$day.' + 7 days '.$timestart.'', $timezone);
            }
            
            
            $numsemainefin = (int)($premierjourfin -> format('W'));
            if(is_int($numsemainefin/2)){
                $premierjourfin;
            }else{
                $premierjourfin = new \DateTime('next '.$day.' + 7 days '.$timeend.'', $timezone);
            }
        endif;
        
        $day = [[$premierjourdebut, $premierjourfin]];
        $i=0;

        While($day[$i][0]< $dateend){
            if($frequency=='everyotherweekspaire' OR $frequency=='everyotherweeksimpaire'):
                $k = ($i+1) * 14;
            else:
                $k = ($i+1) * 7;
            endif;
            $otherdayslot = [new \DateTime(''.$day[0][0]->format('Y-m-d G:i').'+ '.$k.' days', $timezone),new \DateTime(''.$day[0][1]->format('Y-m-d G:i').'+ '.$k.' days', $timezone)];
            $day[$i+1] = $otherdayslot;
            $i++;
        }
        
        return $day;
    
}

    public function spitCreneauIntoSousCreneau(array $data, int $numberOfSousCreneau): array{

        $timezone = $this->timezone;
        $start = $data[0];
        $end = $data[1];

        $starttimestamp = $start->format('U');
        $endtimestamp = $end->format('U');

        $result = ($endtimestamp - $starttimestamp)/$numberOfSousCreneau;

        $i=0;
        while($i<$numberOfSousCreneau):
            ${"souscrentimestamp".$i} = $starttimestamp + $i*$result;
            ${"souscrentimestampend".$i} = $starttimestamp + ($i+1)*$result;

            ${"souscren".$i} = new \DateTime();
            ${"souscren".$i} = ${"souscren".$i}->setTimezone($timezone);
            ${"souscren".$i} = ${"souscren".$i}->setTimestamp(${"souscrentimestamp".$i});

            ${"souscrenend".$i} = new \DateTime();
            ${"souscrenend".$i} = ${"souscrenend".$i}->setTimezone($timezone);
            ${"souscrenend".$i} = ${"souscrenend".$i}->setTimestamp(${"souscrentimestampend".$i});

            $data[2][$i]=[${"souscren".$i},${"souscrenend".$i}];
            $i++;
        endwhile;

        return $data;

    }

    public function TransformArray(array $data):array{

        $timezone = $this->timezone;

        foreach($data["listdate"] as $v):
            $explode1 = explode("/",$v);
            $startcren = new \DateTime($explode1[0],$timezone);

            $explode2 = explode(" ", $explode1[0]);
            $implode1 = implode(" ",[$explode2[0],$explode1[1]]);

            $endcren = new \DateTime($implode1,$timezone);

            $explode3 = explode("-", $explode1[2]);
            $implode2 = implode(" ",[$explode2[0], $explode3[0]]);

            $startsouscren = new \DateTime($implode2, $timezone);

            $implode3 = implode(" ",[$explode2[0], $explode3[1]]);

            $endsouscren = new \DateTime($implode3, $timezone);

            $newdata[][0]=[$startcren, $endcren, 'nom'=>$data['nom']];
            $newdata[][1]=[$startsouscren, $endsouscren, 'nom'=>$data['nom']];
            
        endforeach;

        return $newdata;
    }

    public function TransformArray2(array $data):array{
        $timezone = $this->timezone;

        // Format fr => format us
        $format_fr = $data['jour'];
        $format_us = implode('-',array_reverse  (explode('-',$format_fr)));
        $jour=$format_us;
        $jourheuredebut = implode(" ",[$jour,$data['opening']]);
        $jourheurefin = implode(" ",[$jour,$data['closing']]);

        $start = new \DateTime($jourheuredebut,$timezone);
        $end = new \DateTime($jourheurefin, $timezone);

        $cren=[$start,$end];

        $completedata = $this->spitCreneauIntoSousCreneau($cren, $data['souscren']);

        return $completedata;
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

    public function insertCreneau(array $data, int $cat, int $public){
        
        $description = '';
        foreach($data as $clef=>$valeur){
            
            $start = $valeur[0]->format('Y/m/d G:i');
            $end = $valeur[1]->format('Y/m/d G:i');
            $name = $valeur['nom'];
            
            if(!($this ->CheckIfCreneauExist($start,$end))){
                if($public==1):
                    if($cat==0):
                        $sql1 = 'INSERT into events (cat_creneau, name, description, start, end, public) VALUES (?,?,?,?,?,?)';
                        $sth1 = $this ->pdo-> prepare($sql1);
                        $sth1 -> execute(array($cat, $name, $description, $start, $end, $public));
                    else:
                        $sql1 = 'INSERT into events (cat_creneau, name, description, start, end, public) VALUES (?,?,?,?,?,?)';
                        $sth1 = $this ->pdo-> prepare($sql1);
                        $sth1 -> execute(array($cat, $name, $description, $start, $end, 0));
                    endif;
                else:
                    $sql1 = 'INSERT into events (cat_creneau, name, description, start, end, public) VALUES (?,?,?,?,?,?)';
                    $sth1 = $this ->pdo-> prepare($sql1);
                    $sth1 -> execute(array($cat, $name, $description, $start, $end, 0));
                endif;
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
    
    public function tranlateday(string $day):string{

        foreach($this->days as $k=>$v):
            if($day==$v):
                $translation=$k;
            endif;
        endforeach;

        return $translation;

    }
    
}