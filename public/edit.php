<?php
session_start();
require '../actions/users/securityAction.php';
require '../src/bootstrap.php';
require '../src/config.php';
require '../actions/users/userdefinition.php';

$events = new Calendar\Events($pdo);
$errors = [];
try {
    $event = $events->find($_GET['id'] ?? null);
} catch (\Exception $e) {
    e404();
} catch (\Error $e) {
    e404();
}

$data = [
    'name'        => $event->getName(),
    'date'        => $event->getStart()->format('d-m-Y'),
    'start'       => $event->getStart()->format('H:i'),
    'end'         => $event->getEnd()->format('H:i'),
    'description' => $event->getDescription()
];



$Creneau = new Calendar\Creneaux($pdo,$timezone);

if (isset($_POST['modify'])):
  
    // Format fr => format us
    $format_fr = $_POST['date'];
    $format_us = implode('-',array_reverse  (explode('-',$format_fr)));
    $_POST['date']=$format_us;
    $data = $_POST;
    $validator = new Calendar\EventValidator();
    $errors = $validator->validates($data);
    if (empty($errors)):
        $events->hydrate($event, $data);
        $events->update($event);
        header('Location: index.php?success=1');
        exit();
    endif;
endif;

if (isset($_POST['insert'])):
      $insertion = $users->insertCreneauUser($_POST['id_event']);
    if($insertion == 0):
        $message1 = 'Veuillez insérer au moins un créneau svp.';
    else:
        $message2 = 'Merci, vous venez de vous inscrire sur '.$insertion.' creneau(x)';
    endif;   
endif;

$creneauOnDay = $Creneau->getEventsBetween(new \DateTime(''.$data['date'].''), new \DateTime(''.$data['date'].''), 1);
$usersByCreneau = $users->getAllUsersByCreneau($creneauOnDay);



entete($data['date'],'Consulter et s\'inscrire','1'); 
?>

<div class="container">

  <h1>
    <?= h($event->getName()); ?>
  </h1>

  <form method="post" class="form">
      <?php render('calendar/form', ['data' => $data, 'errors' => $errors]); ?>
    <div class="form-group">
      <?php  if(isset($admin)){?>
      <button type="submit" name="modify" class="btn btn-primary">Modifier l'évènement</button>
      <?php } ?>
    </div>
  </form>
  <form method="post" class="form">
    
    <h1>Créneau pour ce jour</h1>
    <div class="container bg-light rounded mt-4 mb-4 text-center">
    
    <?php foreach($creneauOnDay as $v){
      if($users -> checkIfCreneauExist($v['id'])){
    ?>
      <div class="form-check form-check-inline">
      <input class="form-check-input" name="id_event[]" type="checkbox" id="inlineCheckbox1" value="<?=$v['id']?>" disabled>
      <label class="form-check-label" for="inlineCheckbox1">
      <?php echo ($Creneau -> explodeHeureInDb($v['start']));
      echo '-';
      echo($Creneau -> explodeHeureInDb($v['end']));
      ?>
      </label>
      </div>
    <?php
    }else{
    ?>
    
    <div class="form-check form-check-inline">
      <input class="form-check-input" name="id_event[]" type="checkbox" id="inlineCheckbox1" value="<?=$v['id']?>" >
      <label class="form-check-label" for="inlineCheckbox1">
      <?php echo ($Creneau -> explodeHeureInDb($v['start']));
      echo '-';
      echo($Creneau -> explodeHeureInDb($v['end']));
      ?>
      </label>
      </div>
    
    <?php
    }
    }
    ?>
   
      <div class="container mt-4 mb-4">
      <button type="submit" name="insert" class="btn btn-primary">S'inscrire</button>
      </div>
       </div>
  </form>
</div>

<div class="container bg-light rounded mt-4 mb-4">
  
  <h2>Bénévole(s) inscrit(s) le <?=$data['date']?></h2>

<table class="table table-striped">
      <thead>
        <tr>
          
          <th scope="col">Prenom</th>
          <th scope="col">Nom</th>
          <th scope="col">Créneau</th>
          
        </tr>
      </thead>
      <tbody>
        <?php foreach($usersByCreneau as $v=>$k){
                foreach($k as $n){
        ?>
        <tr>
          <th scope="row">
          <?php
          echo $n['prenom'];
          ?>
          </th>
          <td scope="row" class="text-center">
          <?php
          echo $n['nom'];
          ?></td>
          <td scope="row" class="text-center">
          <?php
          echo ''.$Creneau->explodeHeureInDB($n['start']).'-'.$Creneau->explodeHeureInDB($n['end']).'';
          ?>
          </td>
        </tr>
        <?php
                }
                echo '<tr>
                <th></th>
                <td></td>
                <td></td>
                </tr>';
                
        }
        ?>
      </tbody>
      <tfoot>
        <tr>
          <th scope="col"></th>
          <th scope="col"></th>
          <th scope="col"></th>
        </tr>
      </tfoot>
    </table>
    
</div>

<?php render('footer'); ?>
