<?php
if(!isset($_SESSION['auth'])){
    header('location: /public/login.php');
}