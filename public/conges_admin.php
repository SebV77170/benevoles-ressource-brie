<?php
session_start();
require '../actions/users/securityAction.php';
include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

$conges = new App\Conges($pdo);

if(isset($_POST['approve'])){
    $conges->updateStatus((int)$_POST['id'], 'approved');
}
if(isset($_POST['reject'])){
    $conges->updateStatus((int)$_POST['id'], 'rejected');
}

$pending = $conges->getPending();
$summary = $conges->getMonthlySummary();

entete('Administration','Ressources humaines','4');
?>
<div class="doc">
    <h2>Demandes en attente</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Salarié</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pending as $p): ?>
            <tr>
                <td><?= h($p['prenom']).' '.h($p['nom']) ?></td>
                <td><?= h($p['date_debut']) ?></td>
                <td><?= h($p['date_fin']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button class="btn btn-success btn-sm" name="approve">Valider</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button class="btn btn-danger btn-sm" name="reject">Refuser</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="mt-4">Récapitulatif mensuel</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Salarié</th>
                <th>Mois</th>
                <th>Jours</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($summary as $s): ?>
            <tr>
                <td><?= h($s['prenom']).' '.h($s['nom']) ?></td>
                <td><?= h($s['mois']) ?></td>
                <td><?= h($s['nb_jours']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include('../includes/footer.php'); ?>
