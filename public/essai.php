<?php
session_start();
require '../src/bootstrap.php';
require('../src/config.php');
entete('prout','Pouet pouet','3');

$pdo = get_pdo();

$users = new App\Admins($_SESSION, $pdo);

$Creneau = new Calendar\Creneaux($pdo,$timezone);


$check = $users->getAllUsersAndDateWaiting();


dd($check);








?>

