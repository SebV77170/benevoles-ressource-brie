<?php

session_start();
require '../actions/users/securityAction.php';

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';

entete('Inscription créneau','Inscription à un créneau','2');



$creneau = new Calendar\Creneaux($pdo, $timezone);



if(isset($_POST['validate'])){
    $insertion = $users->insertCreneauUser($_POST['id_event']);
    if($insertion == 0){
        $message1 = 'Veuillez insérer au moins un créneau svp.';
    }else{
        $message2 = 'Merci, vous venez de vous inscrire sur '.$insertion.' creneau(x)';
    }
}

if(isset($_POST['suprr'])){
    $insertion = $users->deleteCreneauUser($_POST['id']);
    if($insertion == 0){
        $message3 = 'Veuillez sélectionner au moins un créneau svp.';
    }else{
        $message4 = 'Merci, vous venez de supprimer '.$insertion.' creneau(x)';
    }
}


if(isset($_POST['ok'])):
  foreach($_POST['id_inscription'] as $k=>$v):
      $users->updateFunction((int)$k,$v);
  endforeach;
endif;

if(isset($admin)):
$creneauUser = $admin -> getCreneauUser();
else:
$creneauUser = $users -> getCreneauUser();
endif;

$lundi = $creneau -> getCreneauByDay('Monday');
$mardi = $creneau -> getCreneauByDay('Tuesday');
$jeudi = $creneau -> getCreneauByDay('Thursday');
$samedi = $creneau -> getCreneauByDay('Saturday');


?>



    <?php if(isset($message1)){
    echo '<div class="alert alert-danger text-center" role="alert">'.$message1.'</div>';
    }?>
    
    <?php if(isset($message2)){
    echo '<div class="alert alert-success text-center" role="alert">'.$message2.'</div>';
    }?>





<h2 class='text-center'>Cochez les créneaux sur lesquels vous voulez vous inscrire sur les 3 prochains mois, puis cliquez sur s'inscrire</h2>
<div class='container'>
  <div class="alert alert-success p-4" role="alert" style="font-size: 1.1rem;">
  <h2 class="text-center mb-4">🎯 Objet de l'association</h2>

  <p class="fw-bold">L’association a pour objet :</p>
  <ul>
    <li>La promotion et le développement du réemploi et de la réparation</li>
    <li>La sensibilisation à l’économie circulaire et l’éducation à l’environnement</li>
    <li>Le soutien au développement social local</li>
  </ul>

  <p class="fw-bold mt-4">Ces objectifs seront poursuivis à travers :</p>
  <ul>
    <li>
      La gestion et le développement d’une structure de réemploi 
      <em>(collecte / valorisation / revente…)</em> d’objets inutilisés,
      en quelque lieu que ce soit sur la commune de Brie-Comte-Robert
    </li>
    <li>L’organisation d’évènements, d’interventions scolaires, d’animations et d’ateliers pour sensibiliser la population</li>
    <li>Toute action jugée adéquate par les membres de l’association</li>
  </ul>
</div>
  <div class="h2 alert alert-warning text-center" role="alert">En étant bénévole à la ressourcerie, je m'engage à respecter les statuts, ainsi que le règlement intérieur.
</div>

</div>

<div class="container bg-light rounded mt-4">
    <form method='post'>
  <div class="row">
    
    <div class="col">
      <h3>Lundi</h3>
      <hr>
      <?php
            Foreach($lundi as $v){
                
      ?>
      <div class="form-check">
        
            <?php if(!($creneau -> CheckIfCreneauOutOfDate($v['end']))){
                
                    if($users -> checkIfCreneauExist($v['id'])){
                        ?>    
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDisabled" disabled>
                        <label class="form-check-label" for="flexCheckDisabled">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                        </label>
                          <?php
                        }else{
                            ?>
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDefault" >
                        <label class="form-check-label" for="flexCheckDefault">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                </label>
                <?php
                }
                
            }else{
                
            }
            ?>    
      
      </div>
          
      <?php 
        }
      ?>
        
    </div>
    <div class="col">
      <h3>Mardi</h3>
      <hr>
      <?php
            Foreach($mardi as $v){
                
      ?>
      <div class="form-check">
        
            <?php if(!($creneau -> CheckIfCreneauOutOfDate($v['end']))){
                
                    if($users -> checkIfCreneauExist($v['id'])){
                        ?>    
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDisabled" disabled>
                        <label class="form-check-label" for="flexCheckDisabled">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                        </label>
                          <?php
                        }else{
                            ?>
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDefault" >
                        <label class="form-check-label" for="flexCheckDefault">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                </label>
                <?php
                }
                
            }else{
                
            }
            ?>    
      
      </div>
          
      <?php 
        }
      ?>
        
    </div>
    <div class="col">
      <h3>Jeudi</h3>
      <hr>
      <?php
            Foreach($jeudi as $v){
                
      ?>
      <div class="form-check">
        
            <?php if(!($creneau -> CheckIfCreneauOutOfDate($v['end']))){
                
                    if($users -> checkIfCreneauExist($v['id'])){
                        ?>    
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDisabled" disabled>
                        <label class="form-check-label" for="flexCheckDisabled">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                        </label>
                          <?php
                        }else{
                            ?>
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDefault" >
                        <label class="form-check-label" for="flexCheckDefault">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                </label>
                <?php
                }
                
            }else{
                
            }
            ?>    
      
      </div>
          
      <?php 
        }
      ?>
        
    </div>
    <div class="col">
      <h3>Samedi</h3>
      <hr>
      <?php
            Foreach($samedi as $v){
                
      ?>
      <div class="form-check">
        
            <?php if(!($creneau -> CheckIfCreneauOutOfDate($v['end']))){
                
                    if($users -> checkIfCreneauExist($v['id'])){
                        ?>    
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDisabled" disabled>
                        <label class="form-check-label" for="flexCheckDisabled">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                        </label>
                          <?php
                        }else{
                            ?>
                        <input class="form-check-input" type="checkbox" name='id_event[]' value="<?php echo $v['id']?>" id="flexCheckDefault" >
                        <label class="form-check-label" for="flexCheckDefault">
                          <?php
                          echo $creneau -> explodeDateInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['start'].'');
                          echo ' ';
                          echo $creneau -> explodeHeureInDb(''.$v['end'].'');
                          ?>
                </label>
                <?php
                }
                
            }else{
                
            }
            ?>    
      
      </div>
          
      <?php 
        }
      ?>
        
    </div>

  </div>
  <div class='container mt-4 mb-4 text-center'>
    <button type='submit' name='validate' class="btn btn-success">S'incrire</button>
  </div>
  
</div>
</form>


<h2 class='text-center'>Les créneaux sur lesquels vous êtes déjà inscrits</h2>

<?php if(isset($message3)){
    echo '<div class="alert alert-danger text-center" role="alert">'.$message3.'</div>';
    }?>
    
    <?php if(isset($message4)){
    echo '<div class="alert alert-success text-center" role="alert">'.$message4.'</div>';
    }?>


<div class="container bg-light rounded mt-4 mb-4">
<form method='post'>
<table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">Jour</th>
          <th scope="col">Heure début</th>
          <th scope="col">Heure fin</th>
          <th scope="col">Fonction</th>
          <th scope="col">Supprimer</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($creneauUser as $v):
        ?>
        <tr>
          <th scope="row">
          <?php
          echo $creneau -> explodeDateInDb($v['start']);
          ?>
          </th>
          <td scope="row"><?php echo $creneau -> explodeHeureInDb($v['start']);?></td>
          <td scope="row">
          <?php
          echo $creneau -> explodeHeureInDb($v['end']);
          ?>
          </td>
          
          
          
          <td scope="row">
          <?php
            if($users->checkIfFunctionExists($v['id_inscription'])):
            
              echo $v['fonction'];
            else:
              
              $fonctions=$users->getAllFunctions();
              ?>
              <div class=container-fluid>
                
                  <form method='post'>
                    <div class="row">
                      <div class="col">
                      
                        <select class="form-select" aria-label="Default select example" name="id_inscription[<?=$v['id_inscription']?>]">">
                        <option selected>Choisissez une fonction</option>
                        <?php
                        foreach($fonctions as $n):
                        ?>
                        <option value='<?=$n['fonction']?>'><?=$n['fonction']?></option>
                        <?php
                        endforeach;
                        ?>
                        </select>
                          
                      </div>
                      <div class="col">
                      <button type='submit' id="ok" name='ok' class="btn btn-success btn-sm">ok</button>
                      </div>
                    </div>
                  </form> 
              </div>
              <?php
            endif;
          ?>
          </td>
          
          
          
          <td scope="row">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name='id[]' value="<?php echo $v['id']?>" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault"></label>
            </div>
          </td>
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
          <th scope="col">
            
            <?php if(count($creneauUser)>0):?>
            <button type='submit' name='suprr' class="btn btn-danger">Supprimer</button>
            <?php endif;?>
          </th>
        </tr>
      </tfoot>
    </table>
    </form>
</div>




<?php include('../includes/footer.php'); ?>