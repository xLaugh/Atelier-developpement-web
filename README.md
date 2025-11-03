# Atelier Développement Web - Système de Location d'Outils

Application web de gestion et location d'outils développée par Alexandre, Arman, Baptiste et Mathias

<img width="1620" height="778" alt="image" src="https://github.com/user-attachments/assets/cf09f9f1-5754-4f43-8545-dd939b20fe07" />


## Description

Cette application permet de gérer un catalogue d'outils et de permettre aux utilisateurs de les réserver pour une période donnée. Le système comprend une API REST backend en PHP (Slim Framework) et une interface frontend en HTML/CSS/JavaScript.

## Prérequis

- Docker et Docker Compose installés sur votre machine
- Git (pour cloner le projet)

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/xLaugh/Atelier-developpement-web
cd Atelier-developpement-web
```

### 2. Lancer les services avec Docker Compose

```bash
docker-compose up -d --build
```

### 3. Vérifier que tout fonctionne

Une fois les conteneurs démarrés, vérifiez l'état de l'API :

```
GET http://localhost:13013/api/health
```

Le frontend est accessible sur :
```
http://localhost:13014
```

## Accès à la base de données

- **Host** : localhost (ou `db` depuis les conteneurs Docker)
- **Port** : 3306
- **Database** : charlymatloc
- **User** : root
- **Password** : (vide)

La base de données est initialisée automatiquement avec les données de test contenues dans `sql/bdd.sql`.

## Structure du projet

```
Atelier-developpement-web/
├── backend/              # API PHP (Slim Framework)
│   ├── src/
│   │   ├── actions/     # Contrôleurs API
│   │   ├── application/ # Logique métier
│   │   ├── domain/      # Entités métier
│   │   ├── infrastructure/ # Répositories PDO
│   │   └── routes.php   # Définition des routes
│   ├── public/
│   │   └── index.php    # Point d'entrée
│   └── Dockerfile
├── frontend/            # Interface utilisateur
│   ├── js/             # JavaScript
│   ├── page/           # Pages HTML
│   └── style/          # Styles SCSS
├── sql/
│   └── bdd.sql         # Script d'initialisation BDD
└── docker-compose.yml   # Configuration Docker
```

## Commandes utiles

**Arrêter les conteneurs** :
```bash
docker-compose down -v
```

**Voir les logs** :
```bash
docker-compose logs -f
```

**Redémarrer les conteneurs** :
```bash
docker-compose restart
```

**Accéder à la base de données** :
```bash
docker exec -it sae_db mysql -u root charlymatloc
```

**Reconstruire les images** :
```bash
docker-compose build
```
