# Soutenance MediaHub — déroulé de 15 minutes

## Présentation et choix techniques — 3 minutes

**Projet :** MediaHub, plateforme de publication et de consultation de contenus multimédias.

- Les visiteurs consultent les publications et les catégories.
- Les auteurs gèrent leurs publications et leurs médias.
- Les administrateurs gèrent les catégories et disposent des droits de modération prévus par les policies.

**Choix techniques :** Laravel 12 et PHP 8.2, architecture MVC, Eloquent, vues Blade, SQLite pour l'environnement de développement, Sanctum pour l'API REST et Vite pour les assets. Les Form Requests centralisent la validation; policies et gates contrôlent les autorisations.

## Démonstration fonctionnelle — 6 minutes

Préparer le serveur local avant le passage : vérifier les variables locales, lancer `php artisan serve`, et s'assurer que la base a été migrée et peuplée avec un compte administrateur de démonstration. Ne jamais afficher ni inclure le mot de passe dans le dépôt ou les diapositives.

1. Présenter la liste des publications et le filtre par catégorie.
2. Se connecter comme auteur, créer une publication avec une image, puis montrer la modification et la validation d'un fichier invalide.
3. Montrer le contrôle d'accès au document privé avec l'auteur puis un autre compte.
4. Se connecter comme administrateur et présenter la gestion des catégories.
5. Présenter les réponses publiques de `GET /api/publications` et `GET /api/categories`, puis une écriture API protégée par un jeton Sanctum.

## Qualité et préparation au déploiement — 2 minutes

- Suite complète : **47 tests réussis, 199 assertions** au dernier contrôle.
- Mesure N+1 sur neuf publications : **29 requêtes** avec chargement paresseux, **4** après chargement anticipé et `withCount()`.
- Cache de la liste des catégories pendant une heure, invalidé lors des modifications associées.
- Présenter l'événement `PublicationCreee`, la journalisation, et la procédure de sauvegarde/restauration dans [DEPLOIEMENT.md](./DEPLOIEMENT.md).
- Mentionner que la simulation de production (migrations éphémères et caches Laravel) est validée; aucun serveur public ni certificat TLS n'a été configuré pour ce dépôt.

## Questions — 4 minutes

Préparer des réponses courtes aux questions suivantes :

- Pourquoi séparer contrôleurs, modèles Eloquent et vues Blade ?
- Comment une policy distingue-t-elle l'auteur d'un administrateur ?
- Pourquoi stocker les documents privés hors du disque public ?
- Comment l'API Sanctum protège-t-elle les opérations d'écriture ?
- Comment les tests évitent-ils de toucher la base de développement ?
- Que faudrait-il configurer pour un vrai déploiement (DNS, HTTPS, serveur web, secrets, permissions et sauvegardes) ?

## Avant la soutenance

- Exécuter `php artisan test` et vérifier `php artisan route:list`.
- Vérifier le compte de démonstration, les catégories et publications; ne pas exposer de secrets.
- Vérifier que `npm run build` a produit les assets et que `public/storage` pointe vers `storage/app/public`.
- Garder [API.md](./API.md), [QUALITE.md](./QUALITE.md) et [DEPLOIEMENT.md](./DEPLOIEMENT.md) accessibles.
