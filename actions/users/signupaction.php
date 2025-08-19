<?php

session_start();
require('../actions/db.php');

//Validation du formulaire

    if(isset($_POST['validate'])){
        
        //Vérifier si l'user a bien compléter tous les champs
    
        if(!empty($_POST['prenom']) AND !empty($_POST['nom']) AND !empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['password'])){
        
            //Les données de l'user
            $date = new \DateTime('now',new \DateTimeZone('Europe/Paris'));
            $date = $date->format('Y-m-d G:i');
            $date_visite = new \DateTime('now',new \DateTimeZone('Europe/Paris'));
            $date_visite = $date_visite->format('Y-m-d G:i');
            $date_last_creneau = NULL;
            $date_next_creneau = NULL;
            $user_prenom = htmlspecialchars($_POST['prenom']);
            $user_nom = htmlspecialchars($_POST['nom']);
            $user_pseudo = htmlspecialchars($_POST['pseudo']);
            $user_email = htmlspecialchars($_POST['mail']);
            $user_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user_admin = 0;
            
            //Vérifier si l'utilisateur est déjà sur le site
            
            $checkIfUserAlreadyExists = $db->prepare('SELECT pseudo FROM users WHERE pseudo = ?');
            $checkIfUserAlreadyExists->execute(array($user_pseudo));
            
            if($checkIfUserAlreadyExists->rowCount() == 0){
                
                //Insérer l'utilisateur dans la BDD
                
                $insertUserOnWebsite = $db->prepare('INSERT INTO users(prenom, nom, pseudo, mail, password, admin)VALUES(?,?,?,?,?,?)');
                $insertUserOnWebsite->execute(array($user_prenom, $user_nom, $user_pseudo,$user_email, $user_password, $user_admin));
                
                //Récupérer les informations de l'utilisateur
                
                $GetInfoOfThisUserReq = $db->prepare('SELECT * FROM users WHERE nom = ? AND prenom = ? AND pseudo = ?  AND mail = ?');
                $GetInfoOfThisUserReq->execute(array($user_nom, $user_prenom, $user_pseudo,$user_email));
                
                $usersInfos = $GetInfoOfThisUserReq->fetch();
                
                //Insérer les date de l'utilisateur dans la table date_users
                
                $insertUserOnDate = $db->prepare('INSERT INTO date_users(id_user, date_inscription, date_derniere_visite, date_dernier_creneau, date_prochain_creneau)VALUES(?,?,?,?,?)');
                $insertUserOnDate->execute(array($usersInfos['uuid_users'], $date, $date_visite, $date_last_creneau, $date_next_creneau));
                
                //Authentifier l'utilisateur sur le site et récupérer ses données dans des variables session.
                
                $_SESSION['auth'] = true;
                $_SESSION['uuid_users'] = $usersInfos['uuid_users'];
                $_SESSION['nom'] = $usersInfos['nom'];
                $_SESSION['prenom'] = $usersInfos['prenom'];
                $_SESSION['pseudo'] = $usersInfos['pseudo'];
                $_SESSION['admin'] = $user_admin;
                $_SESSION['mail'] = $usersInfos['mail'];
                $_SESSION['tel'] = $usersInfos['tel'];
                $_SESSION['date_inscription'] = $date;
                $_SESSION['date_derniere_visite'] = $date_visite;
                $_SESSION['date_dernier_creneau'] = $date_creneau;
                $_SESSION['date_prochain_creneau'] = $date_next_creneau;
                
                //Redirection vers la page d'accueil du forum
                
                header('location: index.php');
                
            }else{
                
                $errorMsg = "l'utilisateur est déjà inscrit sur ce site";
                
            }
            
        
        }else{
            $errorMsg = "Veuillez compléter tous les champs, svp.";
        }

    }


?>