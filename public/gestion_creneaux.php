<?php
session_start();

require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../actions/users/userdefinition.php';
require '../src/config.php';

if (!isset($pdo)) {
    die('Erreur : La connexion à la base de données n\'a pas été établie.');
}

if ($_SESSION['admin'] < 1) {
    echo 'Vous n\'êtes pas autorisé à accéder à cette page.';
    exit();
}

$Creneau = new Calendar\Creneaux($pdo, $timezone);

// Récupération des mois et années disponibles dans la base de données
$months = $Creneau->getAvailableMonths();

$startMonth = $_GET['start_month'] ?? null;
$endMonth = $_GET['end_month'] ?? null;

if ($startMonth && $endMonth) {
    list($startMonth, $startYear) = explode('-', $startMonth);
    list($endMonth, $endYear) = explode('-', $endMonth);
    $creneaux = $Creneau->getCreneauxByDateRange($startYear, $startMonth, $endYear, $endMonth);
} else {
    $creneaux = $Creneau->getAllCreneaux();
}

// Group créneaux by id_in_day
$groupedCreneaux = [];
foreach ($creneaux as $creneau) {
    if (isset($creneau['id_in_day'])) {
        $groupedCreneaux[$creneau['id_in_day']][] = $creneau;
    }
}

// Trier les créneaux par ordre chronologique
foreach ($groupedCreneaux as &$creneauxGroup) {
    usort($creneauxGroup, function ($a, $b) {
        return strtotime($a['date'] . ' ' . $a['start']) - strtotime($b['date'] . ' ' . $b['start']);
    });
}

if (isset($_POST['confirm_delete'])) {
    $id = $_POST['id'];
    $toDelete = $Creneau->getCreneauxToDelete($id); // Récupère les créneaux à supprimer
    if ($Creneau->deleteCreneau($id)) {
        $message = "Les créneaux suivants ont été supprimés :<br>";
        foreach ($toDelete as $creneau) {
            $message .= "- {$creneau['date']} de {$creneau['start']} à {$creneau['end']}<br>";
        }
    } else {
        $message = "Erreur lors de la suppression des créneaux.";
    }
    // Refresh list after deletion
    $creneaux = $Creneau->getAllCreneaux();
    $groupedCreneaux = [];
    foreach ($creneaux as $creneau) {
        if (isset($creneau['id_in_day'])) {
            $groupedCreneaux[$creneau['id_in_day']][] = $creneau;
        }
    }
    foreach ($groupedCreneaux as &$creneauxGroup) {
        usort($creneauxGroup, function ($a, $b) {
            return strtotime($a['date'] . ' ' . $a['start']) - strtotime($b['date'] . ' ' . $b['start']);
        });
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $toDelete = $Creneau->getCreneauxToDelete($id); // Récupère les créneaux à supprimer
    $confirmationMessage = "Les créneaux suivants vont être supprimés :<br>";
    foreach ($toDelete as $creneau) {
        $confirmationMessage .= "- {$creneau['date']} de {$creneau['start']} à {$creneau['end']}<br>";
    }
    $confirmationMessage .= "Confirmez-vous cette suppression ?";
}

entete('Gestion des créneaux', 'Gestion des créneaux', '4');
?>

<div class="container">
    <h1>Gestion des créneaux</h1>
    <?php if (isset($confirmationMessage)) : ?>
        <div class="alert alert-warning">
            <?= $confirmationMessage ?>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" name="confirm_delete" class="btn btn-danger">Confirmer</button>
                <a href="gestion_creneaux.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    <?php endif; ?>
    <?php if (isset($message)) : ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <div class="calendar">
        <h2>Filtrer par plage de durée</h2>
        <form method="get" class="form-inline">
            <div class="form-group">
                <label for="start_month">Début :</label>
                <select name="start_month" id="start_month" class="form-control">
                    <?php foreach ($months as $month) : ?>
                        <option value="<?= $month['month'] ?>-<?= $month['year'] ?>" <?= isset($startMonth, $startYear) && "$startMonth-$startYear" == "{$month['month']}-{$month['year']}" ? 'selected' : '' ?>>
                            <?= $month['month_name'] ?> <?= $month['year'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="end_month">Fin :</label>
                <select name="end_month" id="end_month" class="form-control">
                    <?php foreach ($months as $month) : ?>
                        <option value="<?= $month['month'] ?>-<?= $month['year'] ?>" <?= isset($endMonth, $endYear) && "$endMonth-$endYear" == "{$month['month']}-{$month['year']}" ? 'selected' : '' ?>>
                            <?= $month['month_name'] ?> <?= $month['year'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>
    <hr>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Type</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groupedCreneaux as $id_in_day => $creneauxGroup) : ?>
                <?php foreach ($creneauxGroup as $index => $creneau) : ?>
                    <tr>
                        <td><?= $creneau['id'] ?></td>
                        <td><?= $creneau['cat_creneau'] == 0 ? '<strong>' . $creneau['date'] . '</strong>' : $creneau['date'] ?></td>
                        <td><?= $creneau['cat_creneau'] == 0 ? '<strong>' . $creneau['start'] . '</strong>' : $creneau['start'] ?></td>
                        <td><?= $creneau['cat_creneau'] == 0 ? '<strong>' . $creneau['end'] . '</strong>' : $creneau['end'] ?></td>
                        <td><?= $creneau['cat_creneau'] == 0 ? '<strong>Plage globale</strong>' : 'Sous-créneau' ?></td>
                        <td><?= !empty($creneau['name']) ? $creneau['name'] : '-' ?></td>
                        <td><?= !empty($creneau['description']) ? $creneau['description'] : '-' ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $creneau['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php render('footer'); ?>
