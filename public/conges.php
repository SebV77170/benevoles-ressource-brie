<?php
session_start();
require '../actions/users/securityAction.php';
include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

$conges = new App\Conges($pdo);

if(!empty($_POST['start']) && !empty($_POST['end'])){
    $conges->request($_SESSION['id'], $_POST['start'], $_POST['end']);
    $message = 'Votre demande a été enregistrée';
}

$mesConges = $conges->getByUser($_SESSION['id']);

entete('Congés','Demande de congés','6');
?>
<div class="doc">
    <h2>Demander un congé</h2>
    <?php if(isset($message)) echo '<div class="alert alert-success text-center" role="alert">'.$message.'</div>'; ?>
    <form method="post">
        <div class="form-group">
            <label for="start">Date début</label>
            <input type="date" name="start" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="end">Date fin</label>
            <input type="date" name="end" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Envoyer</button>
    </form>

    <h2 class="mt-4">Mes demandes</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($mesConges as $c): ?>
            <tr>
                <td><?= h($c['date_debut']) ?></td>
                <td><?= h($c['date_fin']) ?></td>
                <td><?= h($c['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include('../includes/footer.php'); ?>
