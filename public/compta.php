<?php

session_start();

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

entete('Comptabilité bénévoles','Comptabilité bénévoles','4');
 

 
if(isset($admin)):
  if(isset($_POST["soumettre"])):
    $listOfUsers = $admin->getNbtotalbenevolat($_POST["startDate"],$_POST["endDate"]);
    $totalHeureDeTravail = $listOfUsers["totalHeures"];
  endif;
?>
<div class="container mt-5">
        <h2 class="mb-4">Merci de sélectionner l'intervalle de date que vous souhaitez svp.</h2>
        <form method="post">
            <div class="form-group">
                <label for="startDate">Date de début</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">Date de fin</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>
            <button type="submit" name="soumettre" class="btn btn-primary">Soumettre</button>
        </form>
</div>
<?php if(isset($_POST["soumettre"])):?>
<div class="container bg-light rounded mt-4 mb-4">
      <h1>Sur la période de <?=$_POST["startDate"]?> à <?=$_POST["endDate"]?>, les <?= $totalHeureDeTravail ?> heures de travail se répartissent comme suit :</h1>
        <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Pseudo</th>
                <th scope="col">Nombre totales de bénévolat</th>
              </tr>
            </thead>
            <tbody>
                <?php
                foreach($listOfUsers["result"] as $v):
                ?>
                <tr>
                    <th scope="col"><?=$v['nom']?></th>
                    <th scope="col"><?=$v['prenom']?></th>
                    <th scope="col"><?=$v['pseudo']?></th>
                    <th scope="col"><?=$v['Heuretotal']?></th>
                
                </tr>
                <?php              
                endforeach;
                ?>
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
<?php endif;?>  
<?php

else:
  echo 'Vous n\'êtes pas administrateur, vous n\'avez pas accès à cette page, merci.';
endif;

include('../includes/footer.php');
?>