<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../actions/users/userdefinition.php';
entete('Administration','Administration','4');
?>

<?php
if($_SESSION['admin'] >= 0){
?>
        
        



<div class="doc">
    <ul>
        <a href="ajout_creneau_bene.php"><li id="bleu">Ajouter un créneau pour un benévole</li></a>
        
        <a href="ajout_creneau.php"><li id="bleu">Ajouter des créneaux</li></a>
        
        <a href="confirmation-list.php"><li id="bleu">Personne en attente de validation</li></a>
        
        <a href="list.php"><li id="bleu">Personnes inscrites sur le site</li></a>
        
        <a href="compta.php"><li id="bleu">Comptabilité bénévolat</li></a>

        <a href="conges_admin.php"><li id="bleu">Ressources humaines</li></a>

        <a href="gestion_creneaux.php"><li id="bleu">Gestion des créneaux</li></a>
        
    </ul>
</div>



<?php
    }else{
        echo 'Vous n\'êtes pas administrateur, veuillez contacter le webmaster svp';
    }
    
    include('../includes/footer.php');
    ?>

