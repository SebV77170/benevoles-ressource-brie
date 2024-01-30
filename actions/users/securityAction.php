<?php
if(!isset($_SESSION['auth'])){
        header('location:http://localhost:8888/benevoles-ressource-brie/public/login.php');
}