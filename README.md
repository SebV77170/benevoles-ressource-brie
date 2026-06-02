# Benevoles Ressource Brie

Application PHP/MySQL de gestion des benevoles de Ressource Brie : comptes utilisateurs, planning de creneaux, inscriptions, documents, administration, conges et suivi des heures de benevolat.

## Fonctionnalites principales

### Comptes utilisateurs

- Inscription d'un nouveau benevole depuis `public/signup.php`.
- Connexion/deconnexion par pseudo et mot de passe.
- Mots de passe stockes avec `password_hash`.
- Reinitialisation du mot de passe par e-mail :
  - demande depuis `public/motdepasseoubli.php` ;
  - changement du mot de passe depuis `public/reset-password.php`.
- Validation des nouveaux comptes par un administrateur dans `public/confirmation-list.php`.
- Modification des informations personnelles depuis `public/informations.php` :
  - adresse e-mail ;
  - telephone ;
  - consultation des creneaux de l'utilisateur.

### Habilitations

Le niveau d'acces est stocke dans `users.admin`.

- `0` : compte cree mais non valide / acces limite.
- `1` : utilisateur valide.
- `2` : administrateur.

Les nouveaux comptes doivent etre valides par un administrateur avant d'acceder pleinement a l'application.

### Planning et creneaux

- Consultation du planning mensuel dans `public/calendrier.php`.
- Navigation mois precedent / mois suivant.
- Affichage des plages globales et des sous-creneaux.
- Indicateur de remplissage des creneaux selon le nombre d'inscrits.
- Inscription et desinscription a des creneaux depuis `public/inscription.php`.
- Attribution ou modification d'une fonction sur une inscription, si des fonctions sont configurees.

### Gestion des creneaux par l'administration

Depuis `public/accueil_admin.php`, l'administration peut acceder a :

- `public/ajout_creneau.php` : creation de series de creneaux.
- `public/ajout_creneau_bene.php` : ajout d'un creneau pour un benevole.
- `public/gestion_creneaux.php` : gestion et suppression des creneaux existants.
- `public/add.php` : ajout ponctuel d'un evenement.
- `public/edit.php` : modification d'un evenement ou d'un creneau.

La logique metier liee aux creneaux se trouve surtout dans :

- `src/Calendar/Creneaux.php`
- `src/Calendar/Events.php`
- `src/Calendar/Month.php`

### Administration des benevoles

- Menu principal d'administration : `public/accueil_admin.php`.
- Validation des comptes en attente : `public/confirmation-list.php`.
- Liste des utilisateurs inscrits : `public/list.php`.
- Modification des habilitations.
- Suppression d'utilisateurs avec nettoyage des donnees liees via `App\Admins::deleteUser`.

### Documents

- Espace documents : `public/documents.php`.
- Les fichiers sont stockes dans `public/fichiers`.
- Fonctionnalites presentes :
  - creation de dossiers ;
  - upload de fichiers ;
  - renommage ;
  - suppression ;
  - deplacement par glisser-deposer ;
  - conservation de noms d'affichage dans `.display_names.json`.

Le dossier `public/fichiers` est ignore par Git pour eviter de versionner les documents utilisateurs.

### Conges / ressources humaines

- Demande de conges par un utilisateur : `public/conges.php`.
- Consultation de ses demandes et de leur statut.
- Validation/refus des demandes par l'administration : `public/conges_admin.php`.
- Recapitulatif mensuel des jours approuves.

### Comptabilite benevole

- Page : `public/compta.php`.
- Selection d'un intervalle de dates.
- Calcul du total d'heures de benevolat sur la periode.
- Repartition des heures par benevole.

### Pages utilitaires

- `public/maintenance.php` : page de maintenance.
- `public/404.php` : page d'erreur.
- `public/NoRight.php` : page affichee quand un compte n'a pas encore les droits suffisants.
- `public/mise-a-jour.php` : page de mise a jour.

## Configuration

### Installation PHP

Installer les dependances Composer :

```bash
composer install
```

La dependance importante pour la configuration est `vlucas/phpdotenv`, utilisee pour charger les variables du fichier `.env`.

### Variables d'environnement

Le fichier `.env` ne doit pas etre versionne. Un modele est disponible dans `.env.example`.

Variables de base de donnees :

```env
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_username
DB_PASS=your_password
DB_CHARSET=utf8
```

Variables e-mail :

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=admin@ressourcebrie.fr
MAIL_PASSWORD=mot_de_passe_application_google_sans_espaces
MAIL_FROM_ADDRESS=admin@ressourcebrie.fr
MAIL_FROM_NAME="Ressource Brie"
```

## Configuration des e-mails Google Workspace

L'envoi des e-mails de reinitialisation de mot de passe passe par SMTP Gmail / Google Workspace avec PHPMailer.

Important : `MAIL_PASSWORD` n'est pas le mot de passe habituel du compte Google. Il faut utiliser un mot de passe d'application Google.

Pour le creer :

1. Se connecter au compte Google utilise pour l'envoi, par exemple `admin@ressourcebrie.fr`.
2. Activer la validation en deux etapes sur le compte si elle ne l'est pas deja.
3. Aller dans les mots de passe d'application Google : `https://myaccount.google.com/apppasswords`.
4. Generer un mot de passe pour l'application.
5. Copier le mot de passe de 16 caracteres dans `MAIL_PASSWORD`, sans espaces.

Si l'authentification SMTP echoue avec `SMTP Error: Could not authenticate`, verifier en priorite :

- le mot de passe d'application ;
- l'activation de la validation en deux etapes ;
- l'autorisation des mots de passe d'application dans Google Workspace ;
- la coherence entre `MAIL_USERNAME` et `MAIL_FROM_ADDRESS`.

## Base de donnees

La connexion principale passe par `actions/db.php`, qui charge `.env` puis cree une connexion PDO.

Des exports SQL sont presents a la racine du projet, notamment :

- `09007_ressourceb.sql`
- `tutocalendar.sql`

Ils servent de reference pour reconstruire la base ou comprendre les tables existantes.

## Structure du projet

```text
actions/
  db.php                    Connexion PDO via .env
  uuid.php                  Generation d'UUID
  users/                    Actions login, logout, signup, securite

includes/
  head.php                  Entete HTML commun
  nav.php                   Menu principal
  footer.php                Footer et scripts communs
  modal.php                 Informations liees aux donnees personnelles

public/
  index.php                 Accueil connecte
  login.php                 Connexion
  signup.php                Inscription
  logout.php                Deconnexion
  motdepasseoubli.php       Demande de reinitialisation de mot de passe
  reset-password.php        Changement du mot de passe
  calendrier.php            Planning mensuel
  inscription.php           Inscription aux creneaux
  informations.php          Profil utilisateur
  documents.php             Gestion documentaire
  accueil_admin.php         Menu d'administration
  confirmation-list.php     Validation des nouveaux comptes
  list.php                  Liste et gestion des utilisateurs
  ajout_creneau.php         Creation de series de creneaux
  ajout_creneau_bene.php    Ajout de creneau pour un benevole
  gestion_creneaux.php      Gestion/suppression de creneaux
  compta.php                Synthese des heures de benevolat
  conges.php                Demande de conges
  conges_admin.php          Validation des conges

src/
  App/
    Users.php               Operations utilisateur et inscriptions aux creneaux
    Admins.php              Operations administrateur
    Conges.php              Demandes et validation de conges
    Validator.php           Validation generique
  Calendar/
    Month.php               Calculs de mois/calendrier
    Event.php               Modele evenement
    Events.php              CRUD evenement
    Creneaux.php            Generation et gestion des creneaux
```

## Parcours utilisateur

1. Le benevole cree son compte sur `signup.php`.
2. Le compte est cree avec un niveau d'acces limite.
3. Un administrateur valide le compte dans `confirmation-list.php`.
4. Une fois valide, le benevole peut consulter le planning, prendre des creneaux, gerer ses informations et acceder aux documents.
5. En cas d'oubli de mot de passe, le benevole demande un lien de reinitialisation. Le lien est envoye par le SMTP Google Workspace configure dans `.env`.

## Parcours administrateur

1. Se connecter avec un compte ayant `admin = 2`.
2. Ouvrir le menu `Administration`.
3. Les actions principales sont :
   - valider les nouveaux comptes ;
   - modifier les habilitations ;
   - ajouter ou supprimer des creneaux ;
   - inscrire un benevole sur un creneau ;
   - consulter la liste des utilisateurs ;
   - suivre la comptabilite benevole ;
   - gerer les demandes de conges ;
   - administrer les documents.

## Securite et points d'attention

- Ne jamais versionner `.env`.
- Ne jamais mettre de mot de passe SMTP ou base de donnees directement dans le PHP.
- Apres une migration de messagerie, tester immediatement le flux "mot de passe oublie".
- Si un secret a ete commite ou partage par erreur, le remplacer/revoquer.
- Les documents utilisateurs dans `public/fichiers` ne sont pas versionnes.
- Plusieurs anciens fichiers contiennent encore des logiques historiques de configuration. Pour les nouveaux changements, privilegier `.env`.

## Verification rapide

Verifier la syntaxe PHP d'un fichier :

```bash
php -l public/motdepasseoubli.php
```

Verifier que le `.env` est lisible par `phpdotenv` :

```bash
php -r "require 'vendor/autoload.php'; Dotenv\Dotenv::createImmutable(__DIR__)->load(); echo 'OK';"
```

Tester la connectivite SMTP depuis Windows :

```powershell
Test-NetConnection smtp.gmail.com -Port 465
```
