<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../src/config.php';
require '../actions/users/userdefinition.php';

$creneaux = new Calendar\Creneaux($pdo, $timezone);

if (!isset($_GET['id'])) {
    header('Location: gestion_creneaux.php');
    exit();
}

$id = (int)$_GET['id'];
$creneau = $creneaux->getCreneauById($id);
if (!$creneau) {
    echo "Créneau introuvable";
    exit();
}

if (isset($_POST['save'])) {
    if (!empty($_POST['date']) && !empty($_POST['start']) && !empty($_POST['end'])) {
        $date_us = implode('-', array_reverse(explode('-', $_POST['date'])));
        $start = $date_us.' '.$_POST['start'];
        $end = $date_us.' '.$_POST['end'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        if ($creneaux->updateCreneau($id, $name, $description, $start, $end)) {
            $message = "Le créneau a été mis à jour.";
            $creneau = $creneaux->getCreneauById($id);
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

entete('Modifier un créneau', 'Modifier un créneau', '4');
?>
<div class="container mt-4">
    <h1>Modifier un créneau</h1>
    <?php if(isset($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label" for="name">Nom</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($creneau['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" name="description" id="description"><?= htmlspecialchars($creneau['description']) ?></textarea>
        </div>
        <div class="row">
            <div class="col">
                <label class="form-label" for="date">Date</label>
                <input type="date" class="form-control" name="date" id="date" value="<?= $creneau['date'] ?>" required>
            </div>
            <div class="col">
                <label class="form-label" for="start">Début</label>
                <input type="time" class="form-control" name="start" id="start" value="<?= $creneau['start'] ?>" required>
            </div>
            <div class="col">
                <label class="form-label" for="end">Fin</label>
                <input type="time" class="form-control" name="end" id="end" value="<?= $creneau['end'] ?>" required>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
            <a href="gestion_creneaux.php" class="btn btn-secondary">Retour</a>
        </div>
    </form>
</div>
<?php include('../includes/footer.php'); ?>
