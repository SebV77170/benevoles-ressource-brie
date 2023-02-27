<?php

session_start();

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

entete('Comptabilité bénévoles','Comptabilité bénévoles','4');
 
 if(isset($_POST['ok'])):
  foreach($_POST['habilitation'] as $k=>$v):
      $admin->updateHabilitation((int)$k,(int)$v);
  endforeach;
endif;
 
 
if(isset($admin)):
$listOfUsers = $admin->getAllUsersAndDateWaiting();



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
                    <th scope="col">
                        <div class=container-fluid>
                            <form method='post'>
                                <div class="row">
                                     <div class="col">
                      
                                        <select class="form-select" aria-label="Default select example" name="habilitation[<?=$v['id']?>]">">
                                        <option selected></option>
                                        <option value='0'>0</option>
                                        <option value='1'>1</option>
                                        <option value='2'>2</option>
                                        </select>
                          
                                    </div>
                                    <div class="col">
                                    <button type='submit' id="ok" name='ok' class="btn btn-success btn-sm">ok</button>
                                    </div>
                                </div>
                            </form> 
                        </div>
                    </th>
                    <th scope="col"><?=$v['date_inscription']?></th>
                    <th scope="col"><?=$v['date_derniere_visite']?></th>
                    <th scope="col"><?=$v['date_dernier_creneau']?></th>
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

//fonction qui va permettre de renvoyer le total d'heure de benevolat

/* public function getNbtotalbenevolat(){

  $sql = 'SELECT start,end , id_user, SUM  AS nb_total_heure
    FROM EVENTS,INSCRIPTION_CRENEAU
    INNER JOIN id.event.INSCRIPTION_CRENEAU=EVENTS.id
    GROUP BY  
    
    $sth = $this -> pdo -> query($sql);
    $result = $sth -> fetchAll();
    
    return $result;
}*/




else:
  echo 'Vous n\'êtes pas administrateur, vous n\'avez pas accès à cette page, merci.';
endif;

include('../includes/footer.php');
?>





<?php

