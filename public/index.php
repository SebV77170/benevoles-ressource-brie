<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../actions/users/userdefinition.php';

entete('Planning bénévoles','PGB (Programme Gestion Bénévoles) v1.0','');

?>

<?php
if($_SESSION['admin'] > 0){
    if (!isset($_SESSION['politique'])):
        include('../includes/modal.php');
        $_SESSION['politique']=TRUE;
    endif;
?>
        
        

<h1>Bienvenue <?php echo $_SESSION['prenom']; ?></h1>

<div class="doc">
    <ul>
        <a href="calendrier.php"><li id="bleu">Accéder au planning</li></a>
        <!--<a href="accueil_depot.php"><li id="vert">Débuter ou reprendre un dépot</li></a>-->
        <a href="inscription.php"><li id="bleu">S'inscrire sur le planning</li></a>
        <a href="informations.php"><li id="bleu">Vos informations</li></a>
        
        
    </ul>
</div>



<?php
    }else{
        echo '<h1>Désolé, votre inscription n\'a pas encore été validée. Nous faisons au mieux compte tenu de nos emplois du temps afin de valider au plus vite. A bientôt.</h1>';
    }
    
    include('../includes/footer.php');
    ?>
            
   