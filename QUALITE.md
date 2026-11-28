# Rapport de qualité — Semaine 11

## Vérifications

Les tests utilisent SQLite en mémoire via `phpunit.xml`. Ils couvrent l'authentification, les restrictions par rôle/propriétaire, les médias, le cache des catégories et les routes API.

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

Dernière exécution complète de `php artisan test` : **47 tests réussis sur 47 (100 %), 199 assertions**.

## Optimisation N+1

Sur neuf publications, le parcours non optimisé a été mesuré en paginant puis en chargeant séparément la catégorie, l'auteur et les commentaires de chaque publication : **29 requêtes SQL**. Cette mesure reproduit le comportement avec chargement paresseux afin de comparer le même volume de données.

La liste actuelle dans `ContenuController` utilise `with(['categorie', 'auteur'])` et `withCount('commentaires')`. Le même scénario HTTP `/contenus` exécute **4 requêtes**, soit 25 de moins (environ 86 % de réduction). Un test utilise `DB::listen()` pour compter les requêtes et prévenir une régression.

## Mise en cache

La liste des catégories et le compteur de publications sont conservés sous la clé `categories.liste` pendant **3 600 secondes (1 heure)**. Le cache est invalidé après création, modification ou suppression d'une catégorie, ainsi qu'après sauvegarde, suppression ou restauration d'une publication. Les tests vérifient l'invalidation à travers le CRUD des catégories et la création d'une publication.

## Événement et journalisation

La création d'une publication depuis le site ou l'API déclenche `PublicationCreee`. Son listener `JournaliserPublicationCreee` consigne l'identifiant avec `Log::info()`. Les deux parcours de création sont testés.

## Sécurité et cohérence

- Les formulaires utilisent des Form Requests et Blade échappe les sorties.
- Les routes de création sont protégées; les policies contrôlent les publications et le gate contrôle les fonctions administrateur.
- Les identifiants administrateur proviennent de variables d'environnement; aucun mot de passe par défaut n'est livré.
- Les fichiers uploadés sont validés, stockés sur le disque approprié et supprimés lors du remplacement ou de la suppression d'une publication.
- Les documents privés sont stockés sur le disque local et téléchargeables uniquement par l'auteur ou un administrateur.
- Une publication ne peut recevoir qu'un média public (image ou audio/vidéo); les documents privés restent indépendants.
- Les jetons Sanctum protègent les écritures API.
- Les compteurs de publications invalident le cache des catégories après chaque changement.
