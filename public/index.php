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
    echo '<h1>Merci d’avoir créé votre compte !</h1>
          <p>Votre inscription est bien enregistrée mais n’a pas encore été validée.<br>
          Nous faisons au mieux, en fonction de nos disponibilités, pour valider rapidement toutes les nouvelles inscriptions.</p>
          <p>À très bientôt !</p>';
}

    
    include('../includes/footer.php');
    ?>
            
   