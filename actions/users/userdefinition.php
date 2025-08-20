<?php
if ($_SERVER['SERVER_NAME'] === 'benevoles') {
    $dbname = 'objets';
    $serveur = 'localhost';
    $login = 'root';
    $pass = '';
} else {
   /*  $dbname = '09007_ressourceb';
    $serveur = 'sql01.ouvaton.coop';
    $login = '09007_ressourceb';
    $pass = 'LaRessourcerieDeBrie77170!'; */
    $dbname = "ressourcebrie_bdd";
    $serveur ="mysql-ressourcebrie.alwaysdata.net";
    $login = "418153";
    $pass = "geMsos-wunxoc-1fucbu";
}
$pdo = get_pdo($dbname, $serveur, $login, $pass); // Initialize the PDO connection globally
$users = new App\Users($_SESSION, $pdo);

if($users->getAdmin() == 2):
    $users = $admin = new App\Admins($_SESSION,$pdo);
endif;
?>