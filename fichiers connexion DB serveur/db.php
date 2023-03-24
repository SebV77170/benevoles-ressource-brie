<?php
            $dbname ="brie"; /*"09007_ressourceb";*/
            $serveur = "127.0.0.1";/*"sql01.ouvaton.coop";*/
            $login = "root";/*"09007_ressourceb";*/
            $pass = ""; /*"LaRessourcerieDeBrie77170!";*/
            
            try{
                        $db = new PDO("mysql:host=$serveur;dbname=$dbname;charset=utf8;", $login, $pass);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            catch(Exception $e){
                        die('Une erreur a été trouvée : '.$e->getMessage());
            }
            
?>