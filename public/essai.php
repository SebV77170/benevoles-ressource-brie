<?php
session_start();
require '../src/bootstrap.php';
require('../src/config.php');
entete('prout','Pouet pouet','3');

$pdo = get_pdo();

$users = new App\Admins($_SESSION, $pdo);

$Creneau = new Calendar\Creneaux($pdo,$timezone);

$start = new \DateTime("14:00");
$end = new \DateTime("18:00");

$starttimestamp = $start->format('U');
$endtimestamp = $end->format('U');

$result = ($endtimestamp - $starttimestamp)/4;

$souscren1timestamp = $starttimestamp + $result;

$souscren1 = new \DateTime();
$souscren1 = $souscren1->setTimestamp($souscren1timestamp);


dd($souscren1, $start, $end);








?>

