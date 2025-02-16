<?php 
if($_SERVER["HTTP_HOST"]=='benevoles'):
    header('location:/public/login.php');
else:
    header('location:http://benevoles.ressourcebrie.fr/public/');
endif;
?>