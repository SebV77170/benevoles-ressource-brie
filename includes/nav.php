
<nav class="navforum">
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-principal">
                <span class="menu-toggle__icon" aria-hidden="true"></span>
                <span class="menu-toggle__label">Menu</span>
        </button>
        <ul class="liste" id="menu-principal">
                <li <?php if($page == 1){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="calendrier.php">Voir le planning</a></li>
                <li <?php if($page == 2){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="inscription.php">Prendre un créneau</a></li>
                <li <?php if($page == 3){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="informations.php">Vos informations</a></li>
                <li <?php if($page == 5){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="documents.php">Documents</a></li>
                <?php  if(isset($admin)){?>
                <li <?php if($page == 4){echo 'class="puce vert"';}else{echo 'class="puce bleu"';} ?>><a class="lienpuce" href="accueil_admin.php">Administration</a></li>
                <?php } ?>
                <li class="puce bleu"><a class="lienpuce" href="logout.php">Logout</a></li>     
        </ul>
</nav>
<script>
        (function () {
                var nav = document.querySelector('.navforum');
                if (!nav) {
                        return;
                }
                var button = nav.querySelector('.menu-toggle');
                var menu = nav.querySelector('.liste');
                if (!button || !menu) {
                        return;
                }

                button.addEventListener('click', function () {
                        var isOpen = nav.classList.toggle('navforum--open');
                        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
        })();
</script>
