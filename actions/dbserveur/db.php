<?php
            /* $dbname = "09007_ressourceb";
            $serveur ="sql01.ouvaton.coop";
            $login = "09007_ressourceb";
            $pass = "LaRessourcerieDeBrie77170!"; */

            $dbname = "ressourcebrie_bdd";
            $serveur ="mysql-ressourcebrie.alwaysdata.net";
            $login = "418153";
            $pass = "geMsos-wunxoc-1fucbu";
            
            try{
                        $db = new PDO("mysql:host=$serveur;dbname=$dbname;charset=utf8;", $login, $pass);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            catch(Exception $e){
                        die('Une erreur a été trouvée : '.$e->getMessage());
            }
            
?>
