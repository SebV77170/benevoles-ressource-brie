<?php 
if($_SERVER["HTTP_HOST"]=='localhost:8888'):
    header('location:/public/index.php');
else:
    header('location:http://benevoles.ressourcebrie.fr/public/');
endif;
?>