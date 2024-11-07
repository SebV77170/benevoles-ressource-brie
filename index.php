<?php 
if($_SERVER["HTTP_HOST"]=='benevoles'):
    header('location:benevoles-ressource-brie/public/index.php');
else:
    header('location:http://benevoles.ressourcebrie.fr/public/');
endif;
?>