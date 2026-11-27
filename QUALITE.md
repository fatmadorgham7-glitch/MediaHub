# Rapport de qualité

## Vérifications

Les tests utilisent SQLite en mémoire via `phpunit.xml`. Ils couvrent l'authentification, les restrictions par rôle/propriétaire, les médias, le cache des catégories et les routes API.

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

## Sécurité et cohérence

- Les formulaires utilisent des Form Requests et Blade échappe les sorties.
- Les routes de création sont protégées; les policies contrôlent les publications et le gate contrôle les fonctions administrateur.
- Les identifiants administrateur proviennent de variables d'environnement; aucun mot de passe par défaut n'est livré.
- Les fichiers uploadés sont validés, stockés sur le disque approprié et supprimés lors du remplacement ou de la suppression d'une publication.
- Les documents privés sont stockés sur le disque local et téléchargeables uniquement par l'auteur ou un administrateur.
- Une publication ne peut recevoir qu'un média public (image ou audio/vidéo); les documents privés restent indépendants.
- Les jetons Sanctum protègent les écritures API.
- Les compteurs de publications invalident le cache des catégories après chaque changement.
