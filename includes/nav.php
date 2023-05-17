
<nav class="navforum">
        <ul class="liste">
                <li <?php if($page == 1){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="calendrier.php">Voir le planning</a></li>
                <li <?php if($page == 2){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="inscription.php">Prendre un créneau</a></li>
                <li <?php if($page == 3){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="informations.php">Vos informations</a></li>
                <li <?php if($page == 5){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="documents.php">Documents</a></li>
                <?php  if(isset($admin)){?>
                <li <?php if($page == 4){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="accueil_admin.php">Administration</a></li>
                <?php } ?>
                <li class="puce bleu"><a class="lienpuce" href="../actions/users/logoutAction.php">Logout</a></li>     
        </ul>
</nav>