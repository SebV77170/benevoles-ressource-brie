<?php

session_start();

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

entete('Inscription créneau','Inscrire un bénévole','4');
 
if(isset($admin)):

$searchTerm = trim($_GET['search'] ?? '');
$successMessage = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'], $_POST['uuid_user'])) {
    $actionType = (string) $_POST['action_type'];
    $uuid = trim((string) $_POST['uuid_user']);

    if ($uuid !== '') {
        if ($actionType === 'update_habilitation' && isset($_POST['habilitation'])) {
            $habilitation = (int) $_POST['habilitation'];

            if (in_array($habilitation, [1, 2], true)) {
                $admin->updateHabilitation($uuid, $habilitation);
                $successMessage = 'Habilitation mise à jour.';
            }
        }

        if ($actionType === 'delete_user') {
            try {
                $admin->deleteUser($uuid);
                $successMessage = 'Bénévole supprimé.';
            } catch (Throwable $exception) {
                $errorMessage = 'Impossible de supprimer ce bénévole.';
            }
        }
    }
}

$listOfUsers = $admin->getAllUsersAndDate();

if ($searchTerm !== '') {
    $listOfUsers = array_filter($listOfUsers, static function (array $user) use ($searchTerm): bool {
        $needle = mb_strtolower($searchTerm);
        $nom = mb_strtolower((string) ($user['nom'] ?? ''));
        $prenom = mb_strtolower((string) ($user['prenom'] ?? ''));

        return mb_strpos($nom, $needle) !== false || mb_strpos($prenom, $needle) !== false;
    });
}

if ($searchTerm !== '') {
    $listOfUsers = array_filter($listOfUsers, static function (array $user) use ($searchTerm): bool {
        $needle = mb_strtolower($searchTerm);
        $nom = mb_strtolower((string) ($user['nom'] ?? ''));
        $prenom = mb_strtolower((string) ($user['prenom'] ?? ''));

        return mb_strpos($nom, $needle) !== false || mb_strpos($prenom, $needle) !== false;
    });
}

?>

<div class="container bg-light rounded mt-4 mb-4 p-3">
    <form method="get" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="search" class="form-label">Rechercher un bénévole (nom ou prénom)</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Exemple : Dupont ou Marie"
                >
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
            <div class="col-auto">
                <a href="list.php" class="btn btn-outline-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

    <?php if ($successMessage !== null): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage !== null): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Nom</th>
            <th scope="col">Prénom</th>
            <th scope="col">Pseudo</th>
            <th scope="col">Habilitation</th>
            <th scope="col">Actions</th>
            <th scope="col">Date d'inscription</th>
            <th scope="col">Date de dernière visite</th>
            <!--<th scope="col">Date de dernier créneau</th>-->
          </tr>
        </thead>
        <tbody>
            <?php foreach($listOfUsers as $v): ?>
            <tr>
                <td><?= htmlspecialchars((string) $v['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $v['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $v['pseudo'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <form method="post" class="d-flex gap-2 align-items-center mb-0">
                        <input type="hidden" name="action_type" value="update_habilitation">
                        <input type="hidden" name="uuid_user" value="<?= htmlspecialchars((string) $v['uuid_user'], ENT_QUOTES, 'UTF-8') ?>">
                        <select name="habilitation" class="form-select form-select-sm" style="max-width: 95px;">
                            <option value="1" <?= ((int) $v['admin'] === 1) ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= ((int) $v['admin'] === 2) ? 'selected' : '' ?>>2</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Valider</button>
                    </form>
                </td>
                <td>
                    <form method="post" class="mb-0" onsubmit="return confirm('Voulez-vous vraiment supprimer ce bénévole ?');">
                        <input type="hidden" name="action_type" value="delete_user">
                        <input type="hidden" name="uuid_user" value="<?= htmlspecialchars((string) $v['uuid_user'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                </td>
                <td><?= htmlspecialchars((string) $v['date_inscription'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $v['date_derniere_visite'], ENT_QUOTES, 'UTF-8') ?></td>
<!--                Rajouter éventuellement date dernier et prochain creneau-->
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
        </tfoot>
      </table>
  </div>

<?php

else:
  echo 'Vous n\'êtes pas administrateur, vous n\'avez pas accès à cette page, merci.';
endif;

include('../includes/footer.php');
?>
