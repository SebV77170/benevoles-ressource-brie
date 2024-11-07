<?php
if(!isset($_SESSION['auth'])){
        header('location:benevoles-ressource-brie/public/login.php');
}