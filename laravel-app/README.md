# Laravel migration target

## Point d'entrée HTTP

- Le point d'entrée web Laravel est désormais présent dans `public/index.php`.
- Le fichier `public/.htaccess` a aussi été ajouté pour un déploiement Apache classique.

## Installation

> Cette installation n'a pas pu être exécutée dans l'environnement d'agent à cause d'un accès réseau bloqué vers Packagist.

Dans un environnement avec accès Composer :

```bash
cd laravel-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Modules déjà préparés
- Authentification sécurisée avec reset password Laravel.
- Gestion du profil bénévole.
- Consultation du planning et inscription aux créneaux.
- Administration de base des créneaux.

## Points d'attention
- L'application est pensée pour réutiliser la base existante (`users.mail`, `users.pseudo`, colonne de rôle `admin`).
- Les dépendances Laravel ne sont pas vendoriées dans ce dépôt.
