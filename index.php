<?php 
if($_SERVER["HTTP_HOST"]=='localhost:8888'):
    header('location:http://localhost:8888/benevoles-ressource-brie/public/index.php');
else:
    header('location:http://benevoles.ressourcebrie.fr/public/');
endif;
?>