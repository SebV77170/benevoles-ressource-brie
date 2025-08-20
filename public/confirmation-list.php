<?php
session_start();

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

entete('Inscription créneau','Inscrire un bénévole','4');
$errorMsg = null;
$successMsg = null;

if (isset($admin)):
  // On récupère d'abord la liste (sert aussi à valider le POST)
  $listOfUsers = $admin->getAllUsersAndDateWaiting();

  // Soumission du formulaire
  if (isset($_POST['ok'])) {

    // Vérification : toutes les habilitations doivent être remplies
    $missing = [];
    foreach ($listOfUsers as $u) {
      $uuid = $u['uuid_user'];
      if (!isset($_POST['habilitation'][$uuid]) || $_POST['habilitation'][$uuid] === '') {
        // Pour le message, on affiche pseudo si dispo, sinon nom/prénom
        $label = $u['pseudo'] ?: trim(($u['prenom'] ?? '').' '.($u['nom'] ?? ''));
        $missing[] = $label !== '' ? $label : $uuid;
      }
    }

    if (!empty($missing)) {
      $errorMsg = "Pour valider, merci de renseigner l’habilitation pour tous les comptes. Manquants : "
                . htmlspecialchars(implode(', ', $missing));
    } else {
      // Tout est rempli : on met à jour
      foreach ($listOfUsers as $u) {
        $uuid  = (string)$u['uuid_user'];
        $level = (int)($_POST['habilitation'][$uuid]);
        $admin->updateHabilitation($uuid, $level);
      }
      $successMsg = "Habilitations mises à jour avec succès.";
      // Après mise à jour, on recharge la liste (les comptes validés disparaissent si admin != 0)
      $listOfUsers = $admin->getAllUsersAndDateWaiting();
    }
  }
?>

<div class="container bg-light rounded mt-4 mb-4">
  <?php if ($errorMsg): ?>
    <div class="alert alert-warning"><?= $errorMsg ?></div>
  <?php elseif ($successMsg): ?>
    <div class="alert alert-success"><?= $successMsg ?></div>
  <?php endif; ?>

  <form method="post">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Pseudo</th>
          <th>Habilitation</th>
          <th>Date d'inscription</th>
          <th>Date de dernière visite</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listOfUsers as $v): 
          $uuid = $v['uuid_user'];
          // Pré-remplissage si retour erreur
          $current = $_POST['habilitation'][$uuid] ?? '';
        ?>
          <tr>
            <td><?= htmlspecialchars((string)($v['nom'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($v['prenom'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($v['pseudo'] ?? '')) ?></td>
            <td class="w-25">
              <select class="form-select" name="habilitation[<?= htmlspecialchars($uuid) ?>]" required>
                <option value="" <?= $current === '' ? 'selected' : '' ?>></option>
                <option value="0" <?= $current === '0' ? 'selected' : '' ?>>0</option>
                <option value="1" <?= $current === '1' ? 'selected' : '' ?>>1</option>
                <option value="2" <?= $current === '2' ? 'selected' : '' ?>>2</option>
              </select>
            </td>
            <td><?= htmlspecialchars((string)($v['date_inscription'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($v['date_derniere_visite'] ?? '')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="text-end mt-3">
      <button type="submit" name="ok" class="btn btn-success">Valider</button>
    </div>
  </form>
</div>

<?php
else:
  echo 'Vous n\'êtes pas administrateur, vous n\'avez pas accès à cette page, merci.';
endif;

include('../includes/footer.php');
?>
