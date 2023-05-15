<?php

session_start();

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

entete('Comptabilité bénévoles','Comptabilité bénévoles','4');
 

 
if(isset($admin)):
$listOfUsers = $admin->getNbtotalbenevolat();



?>

<div class="container bg-light rounded mt-4 mb-4">
    <form method="post">
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
                foreach($listOfUsers as $v):
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
      </form>
  </div>
  
<?php

else:
  echo 'Vous n\'êtes pas administrateur, vous n\'avez pas accès à cette page, merci.';
endif;

include('../includes/footer.php');
?>