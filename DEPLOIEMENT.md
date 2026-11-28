# Préparation de la mise en production — MediaHub

Ce guide décrit une procédure de déploiement Laravel et une simulation locale. Aucun hébergeur ni serveur n'étant configuré dans le dépôt, la simulation ne publie pas l'application sur Internet et n'utilise pas la base locale.

## 1. Prérequis et configuration

Le projet utilise Laravel 12, PHP 8.2 ou supérieur, SQLite par défaut, Sanctum, Blade et Vite. PHP doit notamment disposer de `pdo_sqlite` (ou `pdo_mysql` si la base de production est MySQL), `fileinfo`, `mbstring`, `openssl` et `pdo`. Installer Composer et Node.js uniquement pour construire les dépendances et les assets.

Sur le serveur, cloner le dépôt dans un répertoire non accessible directement par le web, puis :

```sh
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
cp .env.example .env
```

Configurer le `.env` du serveur hors Git :

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.example
LOG_CHANNEL=daily
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
DB_CONNECTION=sqlite
DB_DATABASE=/srv/mediahub/shared/database.sqlite
MEDIAHUB_ADMIN_EMAIL=admin@votre-domaine.example
MEDIAHUB_ADMIN_PASSWORD=replace-with-a-random-secret-of-at-least-12-characters
```

Pour MySQL, remplacer les paramètres `DB_*` par ceux du serveur et s'assurer que `pdo_mysql` est installé. Ne jamais copier le `.env` de développement sur le serveur ni versionner un fichier `.env.*`. Le `.env.example` est un modèle sans secrets.

Générer la clé une seule fois lors de la première installation et la conserver de façon sécurisée. **Ne pas régénérer `APP_KEY` lors d'une mise à jour**, car cela invaliderait les données chiffrées et les sessions existantes :

```sh
php artisan key:generate
```

Sur un serveur utilisant SQLite, créer le fichier de base de données avant la migration et donner au processus PHP-FPM le droit d'y écrire :

```sh
sudo install -d -o www-data -g www-data -m 770 /srv/mediahub/shared
sudo -u www-data touch /srv/mediahub/shared/database.sqlite
```

Adapter `www-data` au compte de PHP-FPM. Avec MySQL, la base est créée par l'administrateur SQL et le dernier chemin `touch` n'est pas nécessaire.

## 2. Déploiement sur un serveur

Mettre l'application en maintenance, arrêter temporairement les workers s'il y en a, puis exécuter depuis la racine du projet :

```sh
php artisan down
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Sur une base neuve uniquement, exécuter le seeder après avoir configuré les identifiants d'administration dans l'environnement :

```sh
php artisan db:seed --force
```

Le seeder crée ou met à jour le compte administrateur à partir de `MEDIAHUB_ADMIN_EMAIL` et `MEDIAHUB_ADMIN_PASSWORD`. Ne pas lancer un seeding qui modifie les comptes sur une base existante sans en vérifier l'effet.

Configurer Nginx ou Apache avec le **DocumentRoot exclusivement sur le dossier `public/`**, PHP-FPM, et une redirection HTTP vers HTTPS avec un certificat valide. Ne jamais exposer la racine du dépôt. Donner à l'utilisateur PHP-FPM les droits nécessaires en écriture seulement sur `storage/` et `bootstrap/cache/`; limiter l'accès au `.env`, à la base SQLite et aux sauvegardes :

```sh
sudo chown -R "$DEPLOY_USER":www-data storage bootstrap/cache
sudo chmod -R u+rwX,g+rwX,o-rwx storage bootstrap/cache
```

Après la vérification post-déploiement, remettre l'application en ligne :

```sh
php artisan up
```

En cas d'échec avant la remise en ligne, consulter `storage/logs/laravel-*.log`, corriger ou restaurer la version précédente, puis ne lancer `php artisan up` qu'après vérification.

## 3. Cache, journaux et mises à jour

Laravel conserve les journaux journaliers avec le canal `daily` configuré dans `config/logging.php` (rétention par défaut : 14 jours). Vérifier que le processus PHP peut écrire dans `storage/logs/` et configurer une supervision externe si le serveur le permet.

Après une modification du code ou de la configuration, vider les caches avant de les reconstruire :

```sh
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ne pas modifier `env()` dans les fichiers applicatifs : après `config:cache`, lire les valeurs avec `config()`.

## 4. Sauvegarde et restauration

Le script PowerShell (pour Windows/Laragon) sauvegarde la base SQLite, les médias publics et les documents privés dans une archive placée hors du projet. Il utilise `database/database.sqlite` par défaut; passer `-DatabasePath` si `DB_DATABASE` pointe ailleurs. Les secrets du `.env` ne sont pas inclus :

```powershell
.\scripts\backup.ps1 -DestinationDirectory 'D:\MediaHubBackups'
.\scripts\backup.ps1 -DatabasePath 'C:\MediaHub\shared\database.sqlite' -DestinationDirectory 'D:\MediaHubBackups'
```

Restreindre l'accès au répertoire des sauvegardes et chiffrer les archives au repos. Planifier la commande dans le Planificateur de tâches Windows. Sur un serveur Linux, utiliser un script `tar` équivalent pour la base SQLite configurée et les deux dossiers de médias; avec MySQL, utiliser un outil de sauvegarde SQL tel que `mysqldump` plutôt qu'une copie de fichiers. Pour SQLite, mettre l'application en maintenance et arrêter les traitements qui écrivent dans la base avant la copie; vérifier que l'archive a bien été créée et la transférer vers un emplacement distinct du serveur.

Pour une restauration sous Windows, d'abord arrêter les écritures et sauvegarder l'état courant. Adapter les chemins et exécuter dans PowerShell depuis la racine du projet :

```powershell
$archive = 'D:\MediaHubBackups\mediahub-AAAAMMJJ-HHMMSS-fff.zip'
$databasePath = 'C:\laragon\www\mediahub\database\database.sqlite'
$staging = Join-Path $env:TEMP ('mediahub-restore-' + [guid]::NewGuid().ToString('N'))
$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
Expand-Archive -LiteralPath $archive -DestinationPath $staging
if (-not (Test-Path (Join-Path $staging 'database\database.sqlite'))) {
    throw 'La sauvegarde ne contient pas la base SQLite.'
}
foreach ($disk in @('public', 'private')) {
    if (-not (Test-Path (Join-Path $staging "storage\app\$disk"))) {
        throw "La sauvegarde ne contient pas le disque $disk."
    }
}
php artisan down
Move-Item -LiteralPath $databasePath ($databasePath + ".before-$stamp")
Copy-Item (Join-Path $staging 'database\database.sqlite') $databasePath
foreach ($disk in @('public', 'private')) {
    $current = "storage\app\$disk"
    if (Test-Path $current) {
        Move-Item $current ($current + ".before-$stamp")
    }
    Copy-Item (Join-Path $staging "storage\app\$disk") storage\app -Recurse
}
php artisan migrate:status
php artisan up
Remove-Item -LiteralPath $staging -Recurse -Force
```

Conserver les répertoires `.before-*` jusqu'à validation complète, puis supprimer seulement les sauvegardes temporaires ciblées. Sur Linux, effectuer les mêmes opérations avec les outils d'archive et de copie du serveur. Reprendre le service seulement après vérification des droits, des médias et d'une connexion applicative. Conserver `.env` et `APP_KEY` dans un gestionnaire de secrets distinct.

La procédure a été vérifiée sur une base SQLite de test isolée : création et extraction de l'archive, contrôle `PRAGMA integrity_check`, vérification d'une donnée restaurée et présence de fichiers sur les disques public et privé. Ne pas tester une restauration sur la base de production.

## 5. Simulation locale sans toucher aux données de développement

Dans PowerShell, les variables suivantes ne valent que pour la session de simulation. La base SQLite en mémoire est éphémère :

```powershell
$env:APP_ENV = 'production'
$env:APP_DEBUG = 'false'
$env:APP_URL = 'https://mediahub.example.test'
$env:DB_CONNECTION = 'sqlite'
$env:DB_DATABASE = ':memory:'
$env:CACHE_STORE = 'array'
$env:SESSION_DRIVER = 'array'
$env:QUEUE_CONNECTION = 'sync'
$env:LOG_CHANNEL = 'daily'
$env:LOG_LEVEL = 'warning'

try {
    php artisan migrate --force
    if (-not $?) { throw 'Migration simulation failed.' }
    php artisan config:cache
    if (-not $?) { throw 'Configuration cache failed.' }
    php artisan route:cache
    if (-not $?) { throw 'Route cache failed.' }
    php artisan view:cache
    if (-not $?) { throw 'View cache failed.' }
    php artisan route:list
    if (-not $?) { throw 'Cached route listing failed.' }
} finally {
    php artisan optimize:clear
    foreach ($name in @('APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_DATABASE', 'CACHE_STORE', 'SESSION_DRIVER', 'QUEUE_CONNECTION', 'LOG_CHANNEL', 'LOG_LEVEL')) {
        Remove-Item "Env:$name" -ErrorAction SilentlyContinue
    }
}
```

La commande `optimize:clear` supprime les caches de simulation. Ne pas exécuter `migrate --force` avec l'environnement de production pointant vers une base réelle sans fenêtre de déploiement et sauvegarde préalable.

## 6. Recette post-déploiement

Avant de remettre l'application en ligne ou de présenter la simulation :

1. Vérifier `php artisan about`, `php artisan route:list` et l'état des migrations.
2. Vérifier que `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` utilise HTTPS, et que `public/storage` pointe vers `storage/app/public`.
3. Vérifier `GET /up`, l'accueil, l'inscription/connexion, les autorisations, les catégories et le CRUD d'une publication.
4. Téléverser une image et vérifier son accès public; vérifier qu'un document privé reste accessible seulement à son auteur ou à un administrateur.
5. Vérifier `GET /api/categories`, `GET /api/publications`, une création API authentifiée et un refus `401` sans jeton.
6. Exécuter `php artisan test` avant le déploiement (jamais contre la base de production) et vérifier les derniers journaux.

## 7. Limites de la simulation et soutenance

La simulation vérifie le cache des routes/configuration/vues, les migrations sur SQLite éphémère et la restauration des sauvegardes de test. Elle ne remplace pas un déploiement réel : HTTPS, DNS, Nginx/Apache, permissions système, supervision et tâche planifiée doivent être validés sur le serveur choisi.

Pour la soutenance, suivre le déroulé et la démonstration proposés dans [SOUTENANCE.md](./SOUTENANCE.md), ainsi que les résultats de qualité décrits dans [QUALITE.md](./QUALITE.md).
