<?php

session_start();
require('../actions/db.php');
require('../actions/uuid.php');

function normalizePseudo(string $pseudo): string
{
    $pseudo = trim($pseudo);
    $pseudo = mb_strtolower($pseudo, 'UTF-8');

    $translit = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $pseudo);
    if ($translit !== false) {
        $pseudo = $translit;
    }

    // 🔥 nettoyage des caractères parasites (dont ')
    $pseudo = preg_replace('/[^a-z0-9]/', '', $pseudo);

    return $pseudo;
}

// Validation du formulaire
if (isset($_POST['validate'])) {

    if (
        !empty($_POST['prenom']) &&
        !empty($_POST['nom']) &&
        !empty($_POST['pseudo']) &&
        !empty($_POST['mail']) &&
        !empty($_POST['password'])
    ) {

        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $date = $date->format('Y-m-d G:i');

        $date_visite = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $date_visite = $date_visite->format('Y-m-d G:i');

        $date_last_creneau = null;
        $date_next_creneau = null;

        $user_prenom = trim($_POST['prenom']);
        $user_nom = trim($_POST['nom']);
        $user_pseudo = trim($_POST['pseudo']);
        $user_pseudo_normalise = normalizePseudo($user_pseudo);
        $user_email = trim($_POST['mail']);
        $user_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user_admin = 0;
        $user_uuid = generate_uuidv4();

        $checkIfUserAlreadyExists = $db->prepare('SELECT uuid_user FROM users WHERE pseudo_normalise = ?');
        $checkIfUserAlreadyExists->execute(array($user_pseudo_normalise));

        if ($checkIfUserAlreadyExists->rowCount() == 0) {

            $insertUserOnWebsite = $db->prepare('
                INSERT INTO users(uuid_user, prenom, nom, pseudo, pseudo_normalise, mail, password, admin)
                VALUES(?,?,?,?,?,?,?,?)
            ');
            $insertUserOnWebsite->execute(array(
                $user_uuid,
                $user_prenom,
                $user_nom,
                $user_pseudo,
                $user_pseudo_normalise,
                $user_email,
                $user_password,
                $user_admin
            ));

            $GetInfoOfThisUserReq = $db->prepare('SELECT * FROM users WHERE uuid_user = ?');
            $GetInfoOfThisUserReq->execute(array($user_uuid));

            $usersInfos = $GetInfoOfThisUserReq->fetch();

            $insertUserOnDate = $db->prepare('
                INSERT INTO date_users(id_user, date_inscription, date_derniere_visite, date_dernier_creneau, date_prochain_creneau)
                VALUES(?,?,?,?,?)
            ');
            $insertUserOnDate->execute(array(
                $usersInfos['uuid_user'],
                $date,
                $date_visite,
                $date_last_creneau,
                $date_next_creneau
            ));

            $_SESSION['auth'] = true;
            $_SESSION['uuid_user'] = $usersInfos['uuid_user'];
            $_SESSION['nom'] = $usersInfos['nom'];
            $_SESSION['prenom'] = $usersInfos['prenom'];
            $_SESSION['pseudo'] = $usersInfos['pseudo'];
            $_SESSION['admin'] = $user_admin;
            $_SESSION['mail'] = $usersInfos['mail'];
            $_SESSION['tel'] = $usersInfos['tel'];
            $_SESSION['date_inscription'] = $date;
            $_SESSION['date_derniere_visite'] = $date_visite;
            $_SESSION['date_dernier_creneau'] = $date_last_creneau;
            $_SESSION['date_prochain_creneau'] = $date_next_creneau;

            header('location: index.php');
            exit;

        } else {
            $errorMsg = "L'utilisateur est déjà inscrit sur ce site.";
        }

    } else {
        $errorMsg = "Veuillez compléter tous les champs, svp.";
    }
}
?>