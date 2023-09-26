<?php

session_start();

include('../src/bootstrap.php');
require('../src/config.php');
require '../actions/users/userdefinition.php';

entete('Inscription créneau','Inscrire un bénévole','4');

if(isset($admin)):

  $listOfUsers = $admin->getAllUsers();
  $creneau = new Calendar\Creneaux($pdo, $timezone);
  
   if(isset($_GET['id'])){
    $donnees=$admin->getOneUser($_GET['id']);
    $donnees = $donnees[0];
    $benevole = new App\Users($donnees,$pdo);
    $creneauUser = $benevole -> getCreneauUser();
  }
  
  if(isset($_POST['validate'])){
    $data=$admin->getOneUser($_GET['id']);
    $data = $data[0];
    $benevole = new App\Users($data,$pdo);
    
    $insertion = $benevole->insertCreneauUser($_POST['id_event']);
    $creneauUser = $benevole -> getCreneauUser();
    if($insertion == 0){
        $message1 = 'Veuillez insérer au moins un créneau svp.';
    }else{
        $message2 = 'Merci, vous venez de vous inscrire sur '.$insertion.' creneau(x)';
    }
  }
  
  if(isset($_POST['suprr'])){
    $data=$admin->getOneUser($_GET['id']);
    $data = $data[0];
    $benevole = new App\Users($data,$pdo);
    $insertion = $benevole->deleteCreneauUser($_POST['id']);
    $creneauUser = $benevole -> getCreneauUser();
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
  $data=$admin->getOneUser($_GET['id']);
  $data = $data[0];
  $benevole = new App\Users($data,$pdo);
  $creneauUser = $benevole -> getCreneauUser();
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
  
  
  
  
  
  <h2 class='text-center'>Sélectionnez le bénévole pour qui vous voulez ajouter un ou des créneaux :</h2>
  
  <form method='get'>
    <div class='mb-3 text-center'>  
  
      <select class="form-select" name='id' aria-label="Default select example">
        <option selected value="<?php if(isset($benevole)) : echo"".$benevole->getId().""; endif; ?>"><?php if(isset($benevole)) : echo "".$benevole->getPrenom()." ".$benevole->getNom().""; else : echo'Selectionner un nom'; endif;?></option>
        <?php
        foreach($listOfUsers as $v):
        ?>
          <option value='<?=$v['id']?>'><?php echo''.$v['prenom'].' '.$v['nom'].''?></option>
          
        <?php  
        endforeach;
        ?>
      </select>
      <div class='mt-4 mb-4 text-center'>
        <button type='submit' name='choose' class="btn btn-success">OK</button>
      </div>
    </div>
  </form>
  
    <?php if(isset($benevole)): ?>
  
  <div class="container bg-light rounded mt-4">
      <form method='post' action="ajout_creneau_bene.php?id=<?=$_GET['id']?>">
    <div class="row">
      
      <div class="col">
        <h3>Lundi</h3>
        <hr>
        <?php
              Foreach($lundi as $v){
                  
        ?>
        <div class="form-check">
          
              <?php if(!($creneau -> CheckIfCreneauOutOfDate($v['end']))){
                  
                      if($benevole -> checkIfCreneauExist($v['id'])){
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
                  
                      if($benevole -> checkIfCreneauExist($v['id'])){
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
                  
                      if($benevole -> checkIfCreneauExist($v['id'])){
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
      <button type='submit' name='validate' class="btn btn-success">Inscrire</button>
    </div>
    
  </div>
  </form>
  
  
  <h2 class='text-center'>Les créneaux sur lesquels <?php echo "".$benevole->getPrenom()." ".$benevole->getNom().""?> est déjà inscrit(e)</h2>
  
  <?php if(isset($message3)){
      echo '<div class="alert alert-danger text-center" role="alert">'.$message3.'</div>';
      }?>
      
      <?php if(isset($message4)){
      echo '<div class="alert alert-success text-center" role="alert">'.$message4.'</div>';
      }?>
  
  
  <div class="container bg-light rounded mt-4 mb-4">
  <form method='post' action="ajout_creneau_bene.php?id=<?=$_GET['id']?>">
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
          <?php foreach($creneauUser as $v){
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
              
              $fonctions=$benevole->getAllFunctions();
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
                  <label class="form-check-label" for="flexCheckDefault">
                  </label>
              </div>
            </td>
          </tr>
          <?php
          }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col">
              
              <button type='submit' name='suprr' class="btn btn-danger">Supprimer</button>
              
            </th>
          </tr>
        </tfoot>
      </table>
      </form>
  </div>
  
<?php

  endif;

else:
  echo 'Vous n\'êtes pas administrateur, vous n\'avez pas accès à cette page, merci.';
endif;

include('../includes/footer.php');
?>