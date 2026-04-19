<nav class="navbar navbar-expand-lg navforum px-2 mb-3">
    <div class="container-fluid">

        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuForum" aria-controls="menuForum" aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="menuForum">
            <ul class="navbar-nav gap-2 text-center">

                <li class="nav-item">
                    <a class="nav-link menu-btn <?= ($page == 1) ? 'active-link' : 'inactive-link' ?>" href="calendrier.php">Voir le planning</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-btn <?= ($page == 2) ? 'active-link' : 'inactive-link' ?>" href="inscription.php">Prendre un créneau</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-btn <?= ($page == 3) ? 'active-link' : 'inactive-link' ?>" href="informations.php">Vos informations</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-btn <?= ($page == 5) ? 'active-link' : 'inactive-link' ?>" href="documents.php">Documents</a>
                </li>

                <?php if(isset($admin)) { ?>
                <li class="nav-item">
                    <a class="nav-link menu-btn <?= ($page == 4) ? 'active-link' : 'inactive-link' ?>" href="accueil_admin.php">Administration</a>
                </li>
                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link menu-btn inactive-link" href="logout.php">Logout</a>
                </li>

            </ul>
        </div>
    </div>
</nav>