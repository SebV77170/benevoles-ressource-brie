<?php
session_start();
require '../actions/users/securityAction.php';

require '../src/bootstrap.php';
require('../src/config.php');
require '../actions/users/userdefinition.php';
require '../actions/users/security1.php';



$events = new Calendar\Events($pdo);
$eventsslots = new Calendar\Events($pdo);
$month = new Calendar\Month($_GET['month'] ?? null, $_GET['year'] ?? null);
$start = $month->getStartingDay();
$start = $start->format('N') === '1' ? $start : $month->getStartingDay()->modify('last monday');
$weeks = $month->getWeeks();
$end = (clone $start)->modify('+' . (6 + 7 * ($weeks -1)) . ' days');
$events = $events->getEventsBetweenByDay($start, $end, 0);
$eventsslots = $eventsslots->getEventsBetweenByDay($start, $end, 1);





entete('Calendrier','Calendrier des créneaux','1');

?>

<div class="calendar">

  <div class="d-flex flex-row align-items-center justify-content-between mx-sm-3">
    <h1><?= $month->toString(); ?></h1>

    <?php if (isset($_GET['success'])): ?>
      <div class="container">
        <div class="alert alert-success">
          L'évènement a bien été enregistré
        </div>
      </div>
    <?php endif; ?>

    <div>
      <a href="calendrier.php?month=<?= $month->previousMonth()->month; ?>&year=<?= $month->previousMonth()->year; ?>" class="btn btn-primary">&lt;</a>
      <a href="calendrier.php?month=<?= $month->nextMonth()->month; ?>&year=<?= $month->nextMonth()->year; ?>" class="btn btn-primary">&gt;</a>
    </div>
  </div>

  <table class="calendar__table calendar__table--<?= $weeks; ?>weeks">
      <?php for ($i = 0; $i < $weeks; $i++): ?>
        <tr>
            <?php
            foreach($month->days as $k => $day):
                $date = (clone $start)->modify("+" . ($k + $i * 7) . " days");
                $eventsForDay = $events[$date->format('Y-m-d')] ?? [];
                $eventsslotForDay = $eventsslots[$date->format('Y-m-d')] ?? [];
                $isToday = date('Y-m-d') === $date->format('Y-m-d');
                
                ?>
              <td class="<?= $month->withinMonth($date) ? '' : 'calendar__othermonth'; ?> <?= $isToday ? 'is-today' : ''; ?>">
                  <?php if ($i === 0): ?>
                    <div class="calendar__weekday"><?= $day; ?></div>
                  <?php endif; ?>
                  
                  
                <a class="calendar__day" <?php  if(isset($admin)){?>href="add.php?date=<?= $date->format('Y-m-d'); ?><?php }else{echo 'href="#"';} ?>"><?= $date->format('d'); ?></a>
                  
                
                
                  <?php foreach($eventsForDay as $event): ?>
                    <div class="calendar__event">
                        <?= (new DateTime($event['start']))->format('H:i') ?> - <?= (new DateTime($event['end']))->format('H:i') ?> <a href="edit.php?id=<?= $event['id']; ?>"><?= h($event['name']); ?></a>
                        
                    </div>
                    <?php $usersByCreneau = $users->getAllUsersByCreneau2($eventsslotForDay);
                      $count = $users->countAllUsersByCreneau($usersByCreneau);
                      
                          if(min($count)<=3){
                            $colorlight = 'rouge';
                          }elseif(min($count)>3 AND min($count)<=7){
                            $colorlight = 'jaune';
                          }else{
                            $colorlight = 'vert';
                          }
                        
                       
                        ?>
                        
                        <div class="calendar__light <?= $colorlight?>">
                        </div>
                        <?php
                        foreach($count as $k=>$v){
                          echo '('.$v.')';
                          
                        }
                        
                      endforeach; ?>
                    
                    
                    
                        
                  
              </td>
            <?php endforeach; ?>
        </tr>
      <?php endfor; ?>
  </table>

  

</div>


<?php require '../includes/footer.php'; ?>
