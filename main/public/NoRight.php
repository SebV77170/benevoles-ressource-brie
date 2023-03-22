<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../actions/users/userdefinition.php';
entete('Pas Accès','Pas accès','0');
?>
        
        
<h1>Désolé, votre inscription n'a pas encore été validée. Nous faisons au mieux compte tenu de nos emplois du temps afin de valider au plus vite. A bientôt.</h1>


<?php
    
    include('../includes/footer.php');
    ?>