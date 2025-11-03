# Documentation des APIs

Toutes les APIs sont accessibles via `http://localhost:13013/api/`

## Authentification

### POST /api/auth/register

Créer un nouveau compte utilisateur.

**Corps de la requête (JSON)** :
```json
{
  "prenom": "Jean",
  "nom": "Dupont",
  "email": "jean.dupont@example.com",
  "password": "motdepasse123"
}
```

**Réponse réussie (201)** :
```json
{
  "success": true,
  "message": "Utilisateur créé",
  "user": {
    "id": 1,
    "prenom": "Jean",
    "nom": "Dupont",
    "email": "jean.dupont@example.com",
    "role": "user"
  }
}
```

### POST /api/auth/login

Se connecter et obtenir un token d'authentification.

**Corps de la requête (JSON)** :
```json
{
  "email": "jean.dupont@example.com",
  "password": "motdepasse123"
}
```

**Réponse réussie (200)** :
```json
{
  "success": true,
  "token": "dev",
  "user": {
    "id": 1,
    "prenom": "Jean",
    "nom": "Dupont",
    "email": "jean.dupont@example.com",
    "role": "user"
  }
}
```

### GET /api/auth/me

Récupérer les informations de l'utilisateur connecté.

**Headers requis** :
```
Authorization: Bearer <token>
```

**Réponse réussie (200)** :
```json
{
  "id": 1,
  "prenom": "Jean",
  "nom": "Dupont",
  "email": "jean.dupont@example.com",
  "role": "user"
}
```

## Catégories

### GET /api/categories

Lister toutes les catégories d'outils.

**Réponse réussie (200)** :
```json
[
  {
    "id": 1,
    "name": "Perçage",
    "description": "Outils de perçage et trépan"
  },
  {
    "id": 2,
    "name": "Sciage",
    "description": "Scies et lames"
  }
]
```

### POST /api/admin/categories

Créer une nouvelle catégorie (admin uniquement).

**Corps de la requête (JSON)** :
```json
{
  "name": "Soudure",
  "description": "Matériel de soudure"
}
```

**Réponse réussie (201)** :
```json
{
  "success": true,
  "category": {
    "id": 7,
    "name": "Soudure",
    "description": "Matériel de soudure"
  }
}
```

### PUT /api/admin/categories/{id}

Modifier une catégorie existante (admin uniquement).

**Corps de la requête (JSON)** :
```json
{
  "name": "Soudure",
  "description": "Matériel de soudure et accessoires"
}
```

## Modèles

### GET /api/models

Lister tous les modèles d'outils.

**Réponse réussie (200)** :
```json
[
  {
    "id": 1,
    "category_id": 1,
    "name": "Perceuse percussion 18V",
    "brand": "Makita",
    "image_url": "https://...",
    "price_per_day": 10,
    "description": "Perceuse sans fil avec 2 batteries"
  }
]
```

### POST /api/admin/models

Créer un nouveau modèle (admin uniquement).

**Corps de la requête (JSON)** :
```json
{
  "category_id": 1,
  "name": "Perceuse visseuse 20V",
  "brand": "DeWalt",
  "image_url": "https://example.com/image.jpg",
  "price_per_day": 12,
  "description": "Description du modèle"
}
```

### PUT /api/admin/models/{id}

Modifier un modèle existant (admin uniquement).

## Outils

### GET /api/outils

Lister tous les outils disponibles. Les outils sont des exemplaires physiques de modèles.

**Paramètres de requête optionnels** :
- `category_id` : Filtrer par catégorie

**Exemple** :
```
GET /api/outils?category_id=1
```

**Réponse réussie (200)** :
```json
[
  {
    "id": 1,
    "model_id": 1,
    "status": 0,
    "model": {
      "id": 1,
      "name": "Perceuse percussion 18V",
      "brand": "Makita",
      "price_per_day": 10
    }
  }
]
```

### GET /api/outils/paginated

Lister les outils avec pagination.

**Paramètres de requête** :
- `page` : Numéro de page (défaut: 1)
- `limit` : Nombre d'éléments par page (défaut: 48)

**Exemple** :
```
GET /api/outils/paginated?page=1&limit=20
```

**Réponse réussie (200)** :
```json
{
  "items": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "total_pages": 8
  }
}
```

### GET /api/outils/search

Rechercher des outils par nom ou description.

**Paramètres de requête** :
- `q` : Terme de recherche (requis)
- `page` : Numéro de page (défaut: 1)
- `limit` : Nombre d'éléments par page (défaut: 48)

**Exemple** :
```
GET /api/outils/search?q=perceuse&page=1&limit=10
```

**Réponse réussie (200)** :
```json
{
  "success": true,
  "items": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "total_pages": 3
  }
}
```

### GET /api/outils/{id}

Récupérer les détails d'un outil spécifique.

**Réponse réussie (200)** :
```json
{
  "id": 1,
  "model_id": 1,
  "status": 0,
  "model": {
    "id": 1,
    "name": "Perceuse percussion 18V",
    "brand": "Makita",
    "price_per_day": 10,
    "description": "Perceuse sans fil avec 2 batteries"
  }
}
```

### POST /api/admin/outils

Créer un nouvel outil (admin uniquement).

**Corps de la requête (JSON)** :
```json
{
  "model_id": 1
}
```

### PUT /api/admin/outils/{id}

Modifier un outil existant (admin uniquement).

## Réservations

### POST /api/reservations

Créer une réservation pour une date spécifique (requiert authentification).

**Headers requis** :
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Corps de la requête (JSON)** :
```json
{
  "items": [
    {
      "outil_id": 1,
      "quantite": 2,
      "date": "2025-01-15",
      "prix": 10.0
    }
  ],
  "payment_token": "tok_123456789"
}
```

**Réponse réussie (200)** :
```json
{
  "success": true,
  "message": "Réservation confirmée et paiement validé (simulation)",
  "payment_token": "tok_123456789",
  "reserved_items": [
    {
      "item_id": 1,
      "outil_id": 1,
      "date": "2025-01-15",
      "user_id": 1
    }
  ],
  "total_items": 2
}
```

### POST /api/reservations/period

Créer une réservation pour une période (du début à la fin).

**Corps de la requête (JSON)** :
```json
{
  "items": [
    {
      "outil_id": 1,
      "quantite": 1,
      "start_date": "2025-01-15",
      "end_date": "2025-01-20",
      "prix_total": 50.0
    }
  ],
  "payment_token": "tok_123456789"
}
```

**Réponse réussie (200)** :
```json
{
  "success": true,
  "message": "Réservation confirmée et paiement validé (simulation)",
  "payment_token": "tok_123456789",
  "reservations": [
    {
      "id": 1,
      "user_id": 1,
      "model_id": 1,
      "quantity": 1,
      "start_date": "2025-01-15",
      "end_date": "2025-01-20",
      "status": "pending",
      "total_price": 50.0
    }
  ]
}
```

### GET /api/user/reservations

Récupérer les réservations de l'utilisateur connecté (requiert authentification).

**Headers requis** :
```
Authorization: Bearer <token>
```

**Réponse réussie (200)** :
```json
[
  {
    "id": 1,
    "user_id": 1,
    "model_id": 1,
    "quantity": 1,
    "start_date": "2025-01-15",
    "end_date": "2025-01-20",
    "status": "pending",
    "total_price": 50.0,
    "created_at": "2025-01-10T10:00:00"
  }
]
```

### GET /api/availability

Vérifier la disponibilité d'un outil pour une période.

**Paramètres de requête** :
- `model_id` : ID du modèle (requis)
- `start_date` : Date de début (format: YYYY-MM-DD) (requis)
- `end_date` : Date de fin (format: YYYY-MM-DD) (requis)

**Exemple** :
```
GET /api/availability?model_id=1&start_date=2025-01-15&end_date=2025-01-20
```

**Réponse réussie (200)** :
```json
{
  "available": true,
  "count": 5,
  "model_id": 1
}
```

## Paiement

### POST /api/payment/process

Traiter un paiement (simulation).

**Corps de la requête (JSON)** :
```json
{
  "cardholder": "Jean Dupont",
  "cardNumber": "4532015112830366",
  "expiry": "12/25",
  "cvc": "123",
  "amount": 50.0
}
```

**Réponse réussie (200)** :
```json
{
  "success": true,
  "token": "tok_generated_123456",
  "last4": "0366",
  "message": "Paiement validé"
}
```

**Note** : Le paiement est simulé. Utilisez le token retourné dans les requêtes de réservation.

## Logs

### GET /api/user/logs

Récupérer les logs d'actions de l'utilisateur connecté (requiert authentification).

**Headers requis** :
```
Authorization: Bearer <token>
```

**Réponse réussie (200)** :
```json
[
  {
    "id": 1,
    "user_id": 1,
    "item_id": 1,
    "action": "borrow",
    "created_at": "2025-01-10T10:00:00"
  }
]
```

## Santé du système

### GET /api/health

Vérifier que l'API est opérationnelle.

**Réponse réussie (200)** :
```json
{
  "status": "ok",
  "timestamp": "2025-01-10T10:00:00Z"
}
```

