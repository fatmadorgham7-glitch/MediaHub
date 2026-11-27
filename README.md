# MediaHub

Plateforme Laravel de gestion et de diffusion de contenus multimédias.

## Fonctionnalités

- Routage MVC et vues Blade responsive.
- Modèles Eloquent pour catégories, publications et commentaires.
- CRUD complet des catégories et publications.
- Inscription, connexion, déconnexion et rôles administrateur/auteur.
- Policies et gate administrateur.
- Images, fichiers audio et vidéo.
- API REST protégée par Laravel Sanctum.
- Tests automatisés Laravel.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
# Renseignez MEDIAHUB_ADMIN_EMAIL et MEDIAHUB_ADMIN_PASSWORD (12 caracteres minimum) dans .env.
php artisan migrate --seed
php artisan storage:link
npm ci
npm run build
php artisan serve
```

Le seeder administrateur refuse de s'exécuter tant que ces identifiants ne sont pas configurés. N'utilisez jamais un mot de passe de production en développement.

## Vérification

```bash
php artisan test
php artisan route:list
```

- [Documentation API](./API.md)
- [Rapport de qualité](./QUALITE.md)