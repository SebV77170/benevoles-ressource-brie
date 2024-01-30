<?php
session_start();
require '../actions/users/securityAction.php';

require '../src/bootstrap.php';
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

entete('Documents','Documents','5');
?>

<div class="doc">
    <ul>

        <h2>Association</h2>
        <a href="../fichiers/reglement_interieur.pdf" target="_blank"><li id="bleu">Règlement intérieur</li></a>
        <a href="../fichiers/statuts.pdf" target="_blank"><li id="bleu">Statuts de l'association</li></a>

        <h2>Véhicules</h2>
        <a href="../fichiers/conducteur.pdf" target="_blank"><li id="bleu">Accréditation conducteur</li></a>
        <a href="../fichiers/utilisation_vehicule.pdf" target="_blank"><li id="bleu">Ordre de mission véhicule</li></a>

        <h2>Dépenses</h2>
        <a href="../fichiers/remboursement.pdf" target="_blank"><li id="bleu_twolines">Demande de remboursement de dépenses</li></a>
        <a href="../fichiers/engagement.pdf" target="_blank"><li id="bleu_twolines">Demande d'engagement de dépenses</li></a>

        <h2>Bricoleurs</h2>
        <a href="../fichiers/habilitation.pdf" target="_blank"><li id="bleu">Habilitation bricoleur</li></a>

        
    </ul>
</div>

<?php require '../includes/footer.php'; ?>
