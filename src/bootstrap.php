<?php
require '../vendor/autoload.php';

function e404 () {
    require '../public/404.php';
    exit();
}

function dd(...$vars) {
    foreach($vars as $var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

function get_pdo (): PDO {
    $dbname = "09007_ressourceb";
    $serveur = "sql01.ouvaton.coop";
    $login = "09007_ressourceb";
    $pass = "LaRessourcerieDeBrie77170!";
    return new PDO("mysql:host=localhost;dbname=objets;charset=utf8;", "root", "root", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}

function h(?string $value): string {
    if ($value === null) {
        return '';
    }
    return htmlentities($value);
}

function render(string $view, $parameters = []) {
    extract($parameters);
    include "../views/{$view}.php";
}

// Retourne le début du document HTML en fonction du titre de la page et du numéro de page pour la coloration du bouton.

function entete(string $titreonglet, string $titre, string $page){
    global $admin;
    echo '<!DOCTYPE HTML>';
    echo '<html>';
    $titreonglet;
    require '../includes/head.php';
    $titre;
    require '../includes/header.php';
    $page;
    require '../includes/nav.php';
    echo '<body>';
}

function everyOtherDayTime(string $day, string $timestart, string $timeend) : array{
        
        $timezone = new DateTimeZone('Europe/Paris');

        $datestart = new DateTime('now',$timezone);
        $dateend = new DateTime(''.$datestart->format('Y-m-d G:i').'+ 6 months',$timezone);
        $premierjourdebut = new DateTime('next '.$day.' '.$timestart.'', $timezone);
        $premierjourfin = new DateTime('next '.$day.' '.$timeend.'', $timezone);

        
        $day = [[$premierjourdebut, $premierjourfin]];
        $i=0;
        While($day[$i][0]< $dateend){
        $k = ($i+1) * 14;
        $otherdayslot = [new DateTime(''.$day[0][0]->format('Y-m-d G:i').'+ '.$k.' days', $timezone),new DateTime(''.$day[0][1]->format('Y-m-d G:i').'+ '.$k.' days', $timezone)];
        $day[$i+1] = $otherdayslot;
        $i++;
        }
        
        return $day;
    
}


