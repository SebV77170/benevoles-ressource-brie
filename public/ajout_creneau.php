<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../src/config.php';
require '../actions/users/userdefinition.php';
entete('Administration','Administration','4');

$jour=new Calendar\Creneaux($pdo,$timezone);

dd($_POST);

if(isset($_POST['validate'])):
  foreach($_POST['jour'] as $k=>$v):
    ${"listdate".$v}=$jour->findNewCreneau($v,$_POST['opening'],$_POST['closing'],$_POST['timing'],$_POST['frequency']);
    $listdate[$jour->tranlateday($v)]=${"listdate".$v};
  endforeach;
  foreach($listdate as $key=>$value):
    foreach($value as $k=>$v):
    $completelistdate[] = $jour->spitCreneauIntoSousCreneau($v, $_POST['souscren']);
    endforeach;
  endforeach;
endif;


if(isset($_POST['insert'])):
  $newlistdate = $jour->TransformArray($_POST);
  dd($newlistdate);
  foreach($newlistdate as $key=>$value):
    foreach($value as $k=>$v):
      $insert=$jour->insertCreneau($value, $k);
    endforeach;
  endforeach;
endif;





?>

<?php
if($_SESSION['admin'] >= 0){
?>

<div class="accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Ajouter une série de créneaux
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <form method="post">
          <div class="container">
            <div class="row">
              <div class="col">
                <h2>Quelle(s) jour(s) ?</h2>
                <?php foreach($jour->days as $k=>$v):?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="jour[]" value="<?=$v?>" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    <?=$k?>
                  </label>
                </div>
                <?php endforeach?>
              </div>
              <div class="col">
                <h2>A quelle fréquence ?</h2>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="frequency" value="everyweeks" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Toutes les semaines
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="frequency" value="everyotherweekspaire" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Toutes les 2 semaines (semaine paire)
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="frequency" value="everyotherweeksimpaire" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Toutes les 2 semaines (semaine impaire)
                  </label>
                </div>
              </div>
              <div class="col">
                <h2>Sur quelle période ?</h2>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="timing" value="3 months" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Sur les prochains 3 mois
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="timing" value="4 months" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Sur les prochains 4 mois
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="timing" value="5 months" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Sur les prochains 5 mois
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="timing" value="6 months" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Sur les prochains 6 mois
                  </label>
                </div>
              </div>
              <div class="col">
                <div class="row">
                  <div class="col">
                    <h2>Heure d'ouverture ?</h2>
                    <select class="form-select" name="opening" aria-label="Default select example">
                      <option selected>Choisissez l'heure d'ouverture</option>
                      <?php foreach($jour->hours as $v):?>
                      <option value="<?=$v?>"><?=$v?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                <div class="row">
                  <div class="col">
                    <h2>Heure de fermeture ?</h2>
                    <select class="form-select" name="closing" aria-label="Default select example">
                        <option selected>Choisissez l'heure de fermeture</option>
                        <?php foreach($jour->hours as $v):?>
                        <option value="<?=$v?>"><?=$v?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
              </div>
              <div class="col-4">
              <h2>Combien de sous-créneaux ?</h2>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="souscren" value="1" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    1
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="souscren" value="2" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    2
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="souscren" value="3" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    3
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="souscren" value="4" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    4
                  </label>
                </div>              
              </div>
              <div class="col-4">
              </div>
            </div>
            <div class="row">
              <div class="col-4">
              </div>
              <div class="col-4">
              <button class="btn btn-success m-3" name="validate" type="submit">Valider</button>
              </div>
              <div class="col-4">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        Ajouter un créneau solitaire
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
      </div>
    </div>
  </div>
</div>

<?php
if(isset($_POST['validate'])):
  echo "<h2>Vous êtes sur le point d'insérer ces créneaux, décochez ceux que vous ne souhaitez pas ajouter, svp.</h2>
  "; 
?>
<form method="post">
  <div class="container">
    <div class="row">
      <div class="col-3">
      </div>
      <div class="col-6">
        <table class="table table-striped">
          <tbody>
          <?php
          foreach($listdate as $key=>$value): 
            foreach($completelistdate as $k=>$v):
              foreach($v[2] as $k1=>$v1):
                ?>
                <tr>
                  <td>
                    <input class="form-check-input" type="checkbox" name="listdate[]" value="<?=$v[0]->format('Y-m-d G:i')?> / <?=$v[1]->format('G:i')?> / <?= $v1[0]->format('G:i')?> - <?= $v1[1]->format('G:i')?>" id="flexCheckChecked" checked>
                  </td>
                  <td>
                    <?=$key?> - <?= $v[0]->format('d/m')?>
                  </td>
                  <td>
                    <?= $v1[0]->format('G:i')?> - <?= $v1[1]->format('G:i')?>
                  </td>
                </tr>
                <?php 
              endforeach;
            endforeach;
          endforeach;
          ?>
          </tbody>
        </table>
      </div>
      <div class="col-3">
      </div>
    </div>
    <div class="row">
      <div class="col-3">
      </div>
      <div class="col-6">
        <button class="btn btn-success m-3" name="insert" type="submit">Insérer</button>
      </div>
      <div class="col-3">
      </div>
    </div>
  </div>
</form>
<?php
endif;
?>



<?php
    }else{
        echo 'Vous n\'êtes pas administrateur, veuillez contacter le webmaster svp';
    }
    
    include('../includes/footer.php');
    ?>


