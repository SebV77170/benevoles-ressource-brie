<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../actions/users/userdefinition.php';
require '../src/config.php';

if (!isset($pdo)) {
    die("Erreur : La connexion à la base de données n'a pas été établie.");
}

if ($_SESSION['admin'] < 1) {
    echo "Vous n'êtes pas autorisé à accéder à cette page.";
    exit();
}

$creneaux = new Calendar\Creneaux($pdo, $timezone);
$month = new Calendar\Month($_GET['month'] ?? null, $_GET['year'] ?? null);
$start = $month->getStartingDay();
$weeks = $month->getWeeks();
$end = (clone $start)->modify('+' . (6 + 7 * ($weeks - 1)) . ' days');

// Jours contenant au moins une plage globale
$global = $creneaux->getEventsBetween($start, $end, 0);
$daysWithEvents = [];
foreach ($global as $event) {
    $date = explode(' ', $event['start'])[0];
    $daysWithEvents[$date][] = $event;
}

// Récupération des créneaux d'une journée
$selectedDate = $_GET['date'] ?? null;
$groupedCreneaux = [];
if ($selectedDate) {
    $creneauxJour = $creneaux->getCreneauxByDate($selectedDate);
    foreach ($creneauxJour as $creneau) {
        $groupedCreneaux[$creneau['id_in_day']][] = $creneau;
    }
}

if (isset($_POST['confirm_delete'])) {
    $id = $_POST['id'];
    $toDelete = $creneaux->getCreneauxToDelete($id);
    if ($creneaux->deleteCreneau($id)) {
        $message = "Les créneaux suivants ont été supprimés :<br>";
        foreach ($toDelete as $cr) {
            $message .= "- {$cr['date']} de {$cr['start']} à {$cr['end']}<br>";
        }
        if ($selectedDate) {
            $creneauxJour = $creneaux->getCreneauxByDate($selectedDate);
            $groupedCreneaux = [];
            foreach ($creneauxJour as $creneau) {
                $groupedCreneaux[$creneau['id_in_day']][] = $creneau;
            }
        }
    } else {
        $message = "Erreur lors de la suppression des créneaux.";
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $toDelete = $creneaux->getCreneauxToDelete($id);
    $confirmationMessage = "Les créneaux suivants vont être supprimés :<br>";
    foreach ($toDelete as $cr) {
        $confirmationMessage .= "- {$cr['date']} de {$cr['start']} à {$cr['end']}<br>";
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
                <a href="gestion_creneaux.php?month=<?= $month->month; ?>&year=<?= $month->year; ?><?php if($selectedDate) echo '&date='.$selectedDate; ?>" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    <?php endif; ?>
    <?php if (isset($message)) : ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="calendar mb-4">
        <div class="d-flex flex-row align-items-center justify-content-between mx-sm-3">
            <h2><?= $month->toString(); ?></h2>
            <div>
                <a href="gestion_creneaux.php?month=<?= $month->previousMonth()->month; ?>&year=<?= $month->previousMonth()->year; ?>" class="btn btn-primary">&lt;</a>
                <a href="gestion_creneaux.php?month=<?= $month->nextMonth()->month; ?>&year=<?= $month->nextMonth()->year; ?>" class="btn btn-primary">&gt;</a>
            </div>
        </div>
        <table class="calendar__table calendar__table--<?= $weeks; ?>weeks">
            <?php for ($i = 0; $i < $weeks; $i++): ?>
                <tr>
                    <?php foreach($month->days as $k => $day):
                        $date = (clone $start)->modify('+' . ($k + $i * 7) . ' days');
                        $isToday = date('Y-m-d') === $date->format('Y-m-d');
                        $hasEvent = isset($daysWithEvents[$date->format('Y-m-d')]);
                    ?>
                    <td class="<?= $month->withinMonth($date) ? '' : 'calendar__othermonth'; ?> <?= $isToday ? 'is-today' : ''; ?>">
                        <?php if ($i === 0): ?>
                            <div class="calendar__weekday"><?= $day; ?></div>
                        <?php endif; ?>
                        <?php if ($hasEvent): ?>
                            <a class="calendar__day" href="gestion_creneaux.php?month=<?= $month->month; ?>&year=<?= $month->year; ?>&date=<?= $date->format('Y-m-d'); ?>">
                                <?= $date->format('d'); ?>
                            </a>
                        <?php else: ?>
                            <span class="calendar__day text-muted">
                                <?= $date->format('d'); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
            <?php endfor; ?>
        </table>
    </div>

    <?php if ($selectedDate): ?>
        <h2>Créneaux du <?= (new DateTime($selectedDate))->format('d-m-Y'); ?></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Type</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($groupedCreneaux as $id_in_day => $crGroup): ?>
                <?php foreach ($crGroup as $cr): ?>
                    <tr>
                        <td><?= $cr['id']; ?></td>
                        <td><?= $cr['start']; ?></td>
                        <td><?= $cr['end']; ?></td>
                        <td><?= $cr['cat_creneau'] == 0 ? '<strong>Plage globale</strong>' : 'Sous-créneau'; ?></td>
                        <td><?= !empty($cr['name']) ? $cr['name'] : '-'; ?></td>
                        <td><?= !empty($cr['description']) ? $cr['description'] : '-'; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $cr['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                            <form method="get" action="edit_creneau.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $cr['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php render('footer'); ?>
