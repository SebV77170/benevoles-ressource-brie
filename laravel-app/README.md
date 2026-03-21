# Laravel migration target

## Installation

> Cette installation n'a pas pu être exécutée dans l'environnement d'agent à cause d'un accès réseau bloqué vers Packagist.

Le dépôt contenait une cible Laravel incomplète : le dossier `public/` n'était pas versionné, ce qui empêchait notamment l'utilisation normale de `php artisan serve` et d'un serveur web pointant vers `public/index.php`. Le point d'entrée a maintenant été ajouté au dépôt.

Dans un environnement avec accès Composer :

```bash
cd laravel-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve  # depuis le dossier laravel-app, avec public/index.php désormais présent
```

## Modules déjà préparés
- Authentification sécurisée avec reset password Laravel.
- Gestion du profil bénévole.
- Consultation du planning et inscription aux créneaux.
- Administration de base des créneaux.

## Points d'attention
- L'application est pensée pour réutiliser la base existante (`users.mail`, `users.pseudo`, colonne de rôle `admin`).
- Les dépendances Laravel ne sont pas vendoriées dans ce dépôt.
