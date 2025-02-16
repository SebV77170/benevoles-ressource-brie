<?php
session_start();
require '../actions/users/securityAction.php';

require '../src/bootstrap.php';
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

entete('Documents','Documents','5');
?>

<style>
    .centered {
        text-align: center;
        margin: 0 auto;
        max-width: 600px; /* Vous pouvez ajuster cette valeur selon vos besoins */
    }
</style>

<article class="doc centered">
    <h1>Documents en cours de mise à jour</h1>
    <p>Les documents que vous cherchez sont actuellement en cours de mise à jour. Merci de revenir plus tard.</p>
</article>

<?php require '../includes/footer.php'; ?>