# API MediaHub

Toutes les réponses sont au format JSON. Les routes de lecture sont publiques; les opérations d'écriture exigent un jeton Sanctum.

## Authentification

### `POST /api/connexion`

Envoie `email` et `password` en JSON. La réponse `200` contient un jeton :

```json
{
  "token": "..."
}
```

Le compte administrateur doit être configuré avec `MEDIAHUB_ADMIN_EMAIL` et `MEDIAHUB_ADMIN_PASSWORD` avant le seeding. Aucun identifiant par défaut n'est fourni.

Pour les routes protégées, envoyer :

```text
Authorization: Bearer <token>
Accept: application/json
```

## Publications

| Méthode | URL | Accès | Succès |
|---|---|---|---|
| GET | `/api/publications` | Public | 200 |
| GET | `/api/publications/{publication}` | Public | 200 |
| POST | `/api/publications` | Jeton Sanctum | 201 |
| PUT/PATCH | `/api/publications/{publication}` | Propriétaire ou administrateur | 200 |
| DELETE | `/api/publications/{publication}` | Propriétaire ou administrateur | 204 |

La liste accepte `categorie_id`, par exemple `/api/publications?categorie_id=1`. Les créations et modifications acceptent `multipart/form-data` pour inclure une image ou un fichier audio/vidéo.

## Catégories

`GET /api/categories` renvoie les catégories publiques avec le nombre de publications associées.
