# Audit de l'application legacy et proposition de migration Laravel

## 1. Architecture actuelle

### Organisation des fichiers
- `public/*.php` contient les points d'entrée HTTP, avec une page PHP par fonctionnalité.
- `actions/users/*.php` concentre les traitements de formulaires, l'authentification et les gardes d'accès.
- `src/App/*.php` contient une couche métier très légère (`Users`, `Admins`, `Conges`).
- `src/Calendar/*.php` regroupe le moteur du planning et la manipulation des créneaux.
- `includes/*.php` et `views/*.php` servent de fragments HTML/Bootstrap réutilisés.
- `09007_ressourceb.sql` et `sql/conges.sql` documentent le schéma SQL existant.

### Routing implicite
Le routage est entièrement implicite via les noms de fichiers dans `public/` :
- `login.php`, `signup.php`, `motdepasseoubli.php`, `reset-password.php` pour l'authentification.
- `index.php` pour l'accueil connecté.
- `calendrier.php`, `inscription.php`, `edit.php`, `gestion_creneaux.php`, `ajout_creneau.php` pour le planning.
- `informations.php`, `list.php`, `confirmation-list.php`, `ajout_creneau_bene.php` pour les bénévoles.
- `compta.php`, `conges.php`, `conges_admin.php`, `documents.php` pour les modules annexes.

### Classes métier
- `App\Users` gère le profil, les inscriptions à des créneaux, la mise à jour mail/téléphone et des agrégats de bénévolat.
- `App\Admins` hérite de `Users` et ajoute les vues admin sur les bénévoles et la mise à jour des habilitations.
- `App\Conges` gère les demandes de congés, mais avec des incohérences de colonnes (`user_id` vs `uuid_user`).
- `Calendar\Events` et `Calendar\Creneaux` portent la logique de planning, de génération de créneaux et d'affichage calendrier.

### Accès base de données
- L'application utilise PDO directement via `actions/db.php`, `actions/dbserveur/db.php` et `src/bootstrap.php`.
- Les accès sont tantôt paramétrés correctement (`prepare/execute`), tantôt interpolés directement dans les requêtes SQL.
- Les identifiants de base distants sont présents en clair dans le dépôt legacy.

## 2. Fonctionnalités principales

### Gestion des bénévoles
- Inscription d'un bénévole avec pseudo, mail, mot de passe, puis création d'une ligne associée dans `date_users`.
- Consultation de la liste des bénévoles et du statut d'habilitation (`admin`).
- Mise à jour du mail et du téléphone depuis la page profil.
- Attribution de créneaux à un bénévole par un administrateur.

### Gestion du planning
- Calendrier mensuel avec créneaux globaux et sous-créneaux.
- Inscription/désinscription d'un bénévole à des créneaux.
- Génération en série de créneaux récurrents selon une fréquence et une durée.
- Vue d'administration pour supprimer/modifier des créneaux et suivre le nombre d'inscrits.

### Authentification actuelle
- Connexion par `pseudo` + mot de passe hashé par `password_hash`.
- Sessions PHP natives via `$_SESSION`.
- Réinitialisation de mot de passe artisanale par email, sans token sécurisé ni expiration réelle.
- Déconnexion via script dédié.

### Rôles utilisateurs
- `admin = 0` : compte en attente de validation.
- `admin = 1` : rôle intermédiaire probable, peu explicité dans le code.
- `admin = 2` : administrateur complet.

### Autres modules
- Congés (`conges.php`, `conges_admin.php`).
- Comptabilité du temps bénévole (`compta.php`).
- Documents internes (`documents.php`).
- Modules métier historiques autour des ventes/objets dans le dump SQL, non visibles dans l'interface bénévole actuelle.

## 3. Schéma de base de données déduit

### Tables cœur bénévolat/planning
- `users(uuid_user, prenom, nom, pseudo, password, admin, mail, tel)`.
- `date_users(id_date, id_user, date_inscription, date_derniere_visite, date_dernier_creneau, date_prochain_creneau)`.
- `events(id, cat_creneau, name, description, start, end)` ; le code laisse aussi entendre des colonnes `public` et `id_in_day` ajoutées hors dump principal.
- `inscription_creneau(id_inscription, id_user, id_event, fonction)`.
- `fonction(id, fonction, description)`.
- `conges(id, uuid_user, date_debut, date_fin, status, created_at)` dans le dump additionnel.

### Tables annexes legacy
`admin`, `article_accueil`, `bilan`, `categories`, `client`, `membres`, `objets_collectes`, `objets_vendus`, `paiement_mixte`, `payments`, `reparation`, `ticketdecaisse`, `ticketdecaissetemp`, `vente`.

## 4. Problèmes techniques actuels

### Sécurité
- Secrets SMTP et identifiants SQL committés en clair dans le dépôt.
- Réinitialisation de mot de passe vulnérable : pseudo encodé en base64 au lieu d'un vrai token, aucune expiration, requêtes SQL interpolées.
- Plusieurs requêtes SQL construites dynamiquement (`Calendar\Events::getEventsBetween`, `find`, pages reset password).
- Contrôles d'accès basés sur `$_SESSION['admin']` dispersés et fragiles.
- Peu ou pas de protection CSRF native.

### Dette de structure
- Mélange systématique vue/contrôleur/modèle dans les mêmes fichiers PHP.
- Duplication massive entre `inscription.php` et `ajout_creneau_bene.php`.
- Incohérences de schéma entre le code et les SQL (`conges.user_id` vs `uuid_user`, `events.public`, `events.id_in_day`).
- API PDO utilitaire dupliquée dans plusieurs fichiers.

### Maintenabilité
- Pas de couche de validation homogène.
- Pas de tests automatisés.
- Couplage fort aux noms de fichiers et aux variables de session.
- Domaines métier non isolés, ce qui complique une migration incrémentale.

## 5. Architecture Laravel cible

### Découpage applicatif
- `app/Http/Controllers/Auth` : login, logout, mot de passe oublié, reset password.
- `app/Http/Controllers` : profil bénévole, planning, inscriptions.
- `app/Http/Controllers/Admin` : administration des créneaux et des bénévoles.
- `app/Models` : `User`, `UserDate`, `EventSlot`, `VolunteerRegistration`, `LeaveRequest`.
- `app/Services` : orchestration métier du planning et support de migration des mots de passe legacy.
- `app/Policies` : autorisations fines sur les créneaux et l'administration.
- `app/Http/Requests` : validation centralisée des formulaires.

### Ce qui est conservé
- Le schéma métier principal (`users`, `date_users`, `events`, `inscription_creneau`, `fonction`, `conges`).
- La logique fonctionnelle de base : pseudo comme identifiant de connexion, rôles via colonne `admin`, Bootstrap pour les vues, fonctionnement du planning.

### Ce qui est refactoré
- Toute l'authentification vers les composants Laravel (session, CSRF, broker de reset password, guards, middleware).
- Les accès base vers Eloquent / Query Builder.
- Les écrans bénévoles et planning vers contrôleurs + Blade.
- La validation vers Form Requests.

### Ce qui est supprimé à terme
- Scripts `public/*.php` legacy comme routeurs directs.
- `actions/db*.php` et la gestion artisanale des sessions.
- Le reset password maison et les secrets en clair dans le code source.

## 6. Stratégie de migration progressive
1. Mettre Laravel à côté de l'existant et le brancher sur la base actuelle.
2. Migrer d'abord l'authentification, car elle sécurise tout le reste.
3. Migrer ensuite les profils bénévoles et la consultation de leurs créneaux.
4. Migrer le planning de consultation puis l'inscription aux créneaux.
5. Migrer enfin les écrans d'administration, congés et comptabilité du temps.
6. Basculer les routes une par une, avec redirections temporaires depuis le legacy.

## 7. Authentification Laravel proposée
- Connexion sur `pseudo` + `password` en gardant la colonne `mail` pour le reset password.
- Sessions en base (`sessions`) pour audit et invalidation propre.
- Table `password_reset_tokens` avec token, date de création et expiration gérée par Laravel.
- Middleware `auth` + middleware métier `EnsureVolunteerIsApproved`.
- Stratégie utilisateurs existants : les hashes bcrypt existants restent valides ; si un hash legacy non standard est détecté plus tard, forcer une réinitialisation via email.

## 8. Plan de mise en production
1. Créer l'application Laravel et installer les dépendances.
2. Reprendre `.env` hors dépôt avec secrets nettoyés.
3. Exécuter les migrations compatibles avec la base existante sur un environnement de recette.
4. Vérifier la connexion, le reset password et le planning.
5. Basculer les URLs critiques avec redirections contrôlées.
6. Prévoir une fenêtre de retour arrière vers le legacy tant que tous les modules ne sont pas migrés.

## 9. Limitation rencontrée dans cet environnement
L'installation complète de Laravel via Composer n'a pas pu être finalisée ici à cause d'un blocage réseau vers Packagist. Le dossier `laravel-app/` livré dans ce dépôt contient donc la structure cible, les contrôleurs, modèles, vues et migrations, mais nécessitera un `composer install` dans un environnement disposant d'un accès aux dépendances PHP pour être réellement exécutable.
