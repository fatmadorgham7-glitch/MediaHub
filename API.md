# API REST MediaHub

L'API est préfixée par `/api` et renvoie des réponses JSON, sauf le téléchargement d'un document privé. Les routes de lecture et les catégories sont publiques. La création, la modification, la suppression et le téléchargement d'un document exigent une authentification Sanctum.

## Authentification

### Obtenir un jeton — `POST /api/connexion`

En-tête : `Accept: application/json`, `Content-Type: application/json`.

Requête :

```json
{
  "email": "utilisateur@example.com",
  "password": "votre-mot-de-passe"
}
```

Réponse `200 OK` :

```json
{
  "token": "1|jeton-personnel-a-conserver"
}
```

Les identifiants incorrects renvoient `401 Unauthorized` :

```json
{
  "message": "Identifiants incorrects."
}
```

Les champs requis manquants ou invalides renvoient `422 Unprocessable Content` avec un objet `errors`. Pour appeler une route protégée, transmettre le jeton reçu :

```text
Authorization: Bearer <token>
Accept: application/json
```

Le compte administrateur doit être configuré avec `MEDIAHUB_ADMIN_EMAIL` et `MEDIAHUB_ADMIN_PASSWORD` avant le seeding. Aucun identifiant par défaut n'est fourni.

## Publications

| Méthode | URL | Authentification / autorisation | Succès |
|---|---|---|---|
| `GET` | `/api/publications` | Publique | `200 OK`, collection paginée |
| `GET` | `/api/publications/{id}` | Publique | `200 OK`, publication |
| `POST` | `/api/publications` | Jeton Sanctum | `201 Created`, publication |
| `PUT`, `PATCH` | `/api/publications/{id}` | Jeton Sanctum; propriétaire ou administrateur | `200 OK`, publication |
| `DELETE` | `/api/publications/{id}` | Jeton Sanctum; propriétaire ou administrateur | `204 No Content` |
| `GET` | `/api/publications/{id}/document` | Jeton Sanctum; propriétaire ou administrateur | `200 OK`, fichier téléchargé |

### Lister et filtrer

`GET /api/publications` accepte les paramètres facultatifs `page` et `categorie_id`. Par exemple :

```text
GET /api/publications?categorie_id=2&page=1
```

La liste renvoie au maximum 10 publications par page, selon le format de pagination Laravel (`data`, `links`, `meta`). Chaque élément de `data` a la forme suivante :

```json
{
  "id": 15,
  "titre": "Une publication intéressante",
  "contenu": "Contenu de la publication...",
  "categorie": "Culture",
  "auteur": "Nom utilisateur",
  "image": "/storage/publications/image.jpg",
  "fichier_media": null,
  "type_media": null,
  "document_url": null,
  "commentaires_count": 0,
  "publie_le": "2026-11-27"
}
```

Pour une requête réussie, la collection et la ressource détaillée sont enveloppées dans `data`. Une publication inconnue renvoie `404 Not Found`.

### Créer — `POST /api/publications`

Utiliser `multipart/form-data` si un fichier est envoyé; sinon, `application/json` convient. Les champs `titre`, `contenu` et `categorie_id` sont obligatoires. Exemple JSON sans fichier :

```json
{
  "titre": "Une publication intéressante",
  "contenu": "Le contenu de la publication contient au moins vingt caractères.",
  "categorie_id": 2
}
```

Réponse `201 Created` : objet publication enveloppé dans `data`, avec la même structure que ci-dessus. Exemple de téléversement en ligne de commande :

```sh
curl -X POST http://127.0.0.1:8000/api/publications \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -F "titre=Une publication intéressante" \
  -F "contenu=Le contenu de la publication contient au moins vingt caractères." \
  -F "categorie_id=2" \
  -F "image=@couverture.jpg"
```

### Modifier — `PUT` ou `PATCH /api/publications/{id}`

Le propriétaire de la publication ou un administrateur peut la modifier. Envoyer les champs requis `titre`, `contenu` et `categorie_id`; les champs de fichiers sont facultatifs. Les requêtes avec fichiers doivent utiliser `multipart/form-data`. Pour retirer explicitement un média existant, envoyer `retirer_image`, `retirer_fichier_media` ou `retirer_document` à `true`.

Réponse `200 OK` : publication mise à jour sous `data`. La ressource ciblée doit exister (`404` sinon) et l'utilisateur doit être autorisé (`403` sinon).

### Supprimer — `DELETE /api/publications/{id}`

Exemple :

```sh
curl -X DELETE http://127.0.0.1:8000/api/publications/15 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

Le propriétaire ou un administrateur reçoit `204 No Content` (aucun corps de réponse). Sans jeton, l'API renvoie `401 Unauthorized`; avec un jeton mais sans autorisation, elle renvoie `403 Forbidden`.

### Télécharger un document privé

La création et la modification acceptent un document PDF ou DOCX de 5 Mo maximum. Les documents sont conservés sur le disque local privé. `document_url` renvoie l'URL de téléchargement protégée; le chemin interne n'est jamais exposé. L'auteur ou un administrateur doit transmettre son jeton Sanctum à `GET /api/publications/{id}/document`. Un utilisateur non connecté reçoit `401`, un autre utilisateur reçoit `403` et une publication ou un document absent reçoit `404`.

## Catégories — `GET /api/categories`

Cette route publique renvoie un tableau JSON de catégories contenant leur identifiant, nom, slug et nombre de publications :

```json
[
  {
    "id": 2,
    "nom": "Culture",
    "slug": "culture",
    "publications_count": 8
  }
]
```

Le champ `categorie_id` obtenu ici peut servir à filtrer `/api/publications`.

## Validation et erreurs HTTP

Les erreurs de validation utilisent le statut `422 Unprocessable Content` et le format Laravel :

```json
{
  "message": "Le titre de la publication doit contenir au moins 10 caractères.",
  "errors": {
    "titre": [
      "Le titre de la publication doit contenir au moins 10 caractères."
    ]
  }
}
```

Principaux statuts :

| Statut | Signification |
|---|---|
| `200` | Lecture, connexion ou modification réussie |
| `201` | Publication créée |
| `204` | Publication supprimée, sans corps |
| `401` | Jeton absent/invalide ou identifiants de connexion incorrects |
| `403` | Jeton valide, mais l'utilisateur n'est pas propriétaire ni administrateur |
| `404` | Ressource ou document introuvable |
| `422` | Données absentes ou invalides |

Une publication doit avoir un titre de 10 à 255 caractères, un contenu d'au moins 20 caractères et une catégorie existante. Les fichiers acceptés sont une image de 2 Mo maximum, un média MP3/MP4/WAV de 20 Mo maximum ou un document PDF/DOCX de 5 Mo maximum. Une publication peut avoir une image ou un média audio/vidéo, mais pas les deux simultanément.
