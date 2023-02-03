<?php
$pdo= get_pdo();
$users = new App\Users($_SESSION, $pdo);

if($users->getAdmin() == 2):
    $users = $admin = new App\Admins($_SESSION,$pdo);
endif;
?>