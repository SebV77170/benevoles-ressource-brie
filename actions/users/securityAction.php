<?php
if(!isset($_SESSION['auth'])){
    header('Location: /public/login.php');
    exit;
}
