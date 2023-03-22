<?php
session_start();
require '../actions/users/securityAction.php';

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';


if(isset($_POST['modiftel'])):
    $users->updateTelUser($_POST['tel']);
    header('location:informations.php');
endif;

if(isset($_POST['modifmail'])):
    $users->updateMailUser($_POST['mail']);
    header('location:informations.php');
endif;

entete('Vos informations','Vos informations','3');

$creneau = new Calendar\Creneaux($pdo, $timezone);

$creneauUser = $users -> getCreneauUser();

?>

<section>

  <form method="post">   
    <div class="col-lg-8 mt-4 mx-auto">
      <div class="card mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col-sm-3">
              <p class="mb-0">Nom et Prénom</p>
            </div>
            <div class="col-sm-9">
              <p class="text-muted mb-0"><?= $users->getNom()?> <?= $users->getPrenom()?> </p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-sm-3">
              <p class="mb-0">Pseudo</p>
            </div>
            <div class="col-sm-9">
              <p class="text-muted mb-0"><?= $users->getPseudo()?> </p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-sm-3">
              <p class="mb-0">E-mail</p>
            </div>
            <div class="col-sm-9">
              <p class="text-muted mb-0">
              <?php
              if(!empty($users->getMail())):
                echo $users->getMail();
                ?>
                <input class="form-control form-control-sm" name="mail" type="text" placeholder="exemple@mail.fr" aria-label=".form-control-sm example">
                <button type="submit" name="modifmail" class="btn btn-primary btn-sm">Modifier</button>
                <?php
              else:
              ?>
              <input class="form-control form-control-sm" name="mail" type="text" placeholder="exemple@mail.fr" aria-label=".form-control-sm example">
              <button type="submit" name="modifmail" class="btn btn-primary btn-sm">Modifier</button>
              <?php
              endif;
              
              ?>
              </p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-sm-3">
              <p class="mb-0">Numéro de téléphone</p>
            </div>
            <div class="col-sm-9">
              <p class="text-muted mb-0">
              <?php
              if(!empty($users->getTel())):
                echo $users->getTel();
                ?>
                <input class="form-control form-control-sm" name="tel" type="text" placeholder="0612345678" aria-label=".form-control-sm example">
                <button type="submit" name="modiftel" class="btn btn-primary btn-sm">Modifier</button>
                <?php
              else:
              ?>
              <input class="form-control form-control-sm" name="tel" type="text" placeholder="0612345678" aria-label=".form-control-sm example">
              <button type="submit" name="modiftel" class="btn btn-primary btn-sm">Modifier</button>
              <?php
              endif;
              ?>
              </p>
            </div>
          </div>
        </div>
      </div>
   </form>  
      
      <h2 class='text-center'>Les créneaux sur lesquels vous êtes déjà inscrits</h2>
      <small class='text-center'>Pour modifier les fonctions, aller dans l'onglet 'Prendre un créneau'</small>
        
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">Jour</th>
          <th scope="col">Heure début</th>
          <th scope="col">Heure fin</th>
          <th scope="col">Fonction</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($creneauUser as $v){
        ?>
        <tr>
          <th scope="row">
          <?php
          echo $creneau -> explodeDateInDb($v['start']);
          ?>
          </th>
          <td><?php echo $creneau -> explodeHeureInDb($v['start']);?></td>
          <td>
          <?php
          echo $creneau -> explodeHeureInDb($v['end']);
          ?>
          </td>
          <td><?php echo $v['fonction'];?></td>
        </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
        
</section>



<?php include('../includes/footer.php'); ?>