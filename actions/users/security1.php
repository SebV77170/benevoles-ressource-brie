<?php
if($users->getAdmin() == 0):
    header('location: NoRight.php');
endif;