<?php

session_start();
require('../actions/db.php');

//Validation du formulaire

    if(isset($_POST['validate'])){
        
        //Vérifier si l'user à bien compléter tous les champs
    
        if(!empty($_POST['pseudo']) AND !empty($_POST['password'])){
        
            //Les données de l'user
    
            $user_pseudo = htmlspecialchars($_POST['pseudo']);
            $user_password = htmlspecialchars($_POST['password']);
            
            //Vérifier si l'utilisateur existe (si le pseudo existe)
            
            $checkIfUserExists = $db->prepare('SELECT * FROM users
                                               INNER JOIN date_users ON users.id = date_users.id_user
                                               WHERE pseudo = ?');
            $checkIfUserExists->execute(array($user_pseudo));
            
            if($checkIfUserExists->rowCount() > 0){
                
                //Récupérer les données de l'utilisateur
                
                $usersInfos = $checkIfUserExists->fetch();
                
                //Vérifier si le mot de passe est correct.
                
                if(password_verify($user_password, $usersInfos['password'])){
                    
                    //Récupérer et insérer la date de cette connexion dans la table date_users:
                    
                    $date_visite = new \DateTime('now',new \DateTimeZone('Europe/Paris'));
                    $date_visite = $date_visite->format('Y-m-d G:i');
                    
                    $sql = 'UPDATE date_users SET date_derniere_visite = ? WHERE id_user = '.$usersInfos['id'].'';
                    $sth = $db->prepare($sql);
                    $sth -> execute(array($date_visite)); 
                    
                    //Authentifier l'utilisateur sur le site et récupérer ses données dans des variables session.
                
                    $_SESSION['auth'] = true;
                    $_SESSION['admin']=$usersInfos['admin'];
                    $_SESSION['id'] = $usersInfos['id'];
                    $_SESSION['nom'] = $usersInfos['nom'];
                    $_SESSION['prenom'] = $usersInfos['prenom'];
                    $_SESSION['ucprenom'] = ucwords($usersInfos['prenom']);
                    $_SESSION['pseudo'] = $usersInfos['pseudo'];
                    $_SESSION['mail'] = $usersInfos['mail'];
                    $_SESSION['tel'] = $usersInfos['tel'];
                    $_SESSION['date_inscription'] = $usersInfos['date_inscription'];
                    $_SESSION['date_derniere_visite'] = $date_visite;
                    $_SESSION['date_dernier_creneau'] = $usersInfos['date_dernier_creneau'];
                    $_SESSION['date_prochain_creneau'] = $usersInfos['date_prochain_creneau'];
                    
                    //Rediriger l'utilisateur vers la page d'accueil du forum
                    
                    header('location: ../public/index.php');
                    
                }else{
                    $errorMsg = "Votre mot de passe est incorrect...";
                }
                
            }else{
                $errorMsg = "Le pseudo est incorrect...";
            }
            
        
        }else{
            $errorMsg = "Veuillez compléter tous les champs, svp.";
        }

    }


?>