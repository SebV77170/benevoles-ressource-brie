<?php
require '../src/bootstrap.php';
http_response_code(404);
entete('Erreur 404','Erreur 404','');
?>

<h1>Page introuvable</h1>

<?php require '../includes/footer.php'; ?>
