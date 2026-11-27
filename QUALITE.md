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
- Les fichiers uploadés sont validés, stockés sur le disque public et nettoyés lors du remplacement ou de la suppression définitive.
- Les jetons Sanctum protègent les écritures API.
- Les compteurs de publications invalident le cache des catégories après chaque changement.
