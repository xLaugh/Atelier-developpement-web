# Guide de démarrage rapide

Guide rapide pour installer et démarrer l'application avec Docker.

## Prérequis

- Docker installé sur votre machine
- Docker Compose installé (inclus avec Docker Desktop)

### Installation de Docker

**Windows** :
1. Téléchargez Docker Desktop depuis https://www.docker.com/products/docker-desktop
2. Lancez l'installateur et suivez les instructions
3. Redémarrez votre ordinateur si nécessaire
4. Vérifiez l'installation avec : `docker --version`

**Linux** :
```bash
sudo apt-get update
sudo apt-get install docker.io docker-compose
sudo systemctl start docker
sudo systemctl enable docker
```

**macOS** :
1. Téléchargez Docker Desktop depuis https://www.docker.com/products/docker-desktop
2. Installez l'application
3. Lancez Docker Desktop depuis les Applications

## Installation du projet

### 1. Cloner le dépôt

```bash
git clone https://github.com/xLaugh/Atelier-developpement-web
cd Atelier-developpement-web
```

### 2. Lancer les services

```bash
docker-compose up -d --build
```

Cette commande va :
- Construire les images Docker nécessaires
- Démarrer trois conteneurs :
  - **Backend API** : http://localhost:13013
  - **Frontend** : http://localhost:13014
  - **Base de données MySQL** : port 3306

### 3. Vérifier que tout fonctionne

Attendez quelques secondes que les conteneurs démarrent, puis testez l'API :

```bash
curl http://localhost:13013/api/health
```

Ou ouvrez dans votre navigateur :
- Frontend : http://localhost:13014
- API Health : http://localhost:13013/api/health

## Commandes Docker utiles

### Voir les conteneurs en cours d'exécution

```bash
docker-compose ps
```

### Voir les logs

```bash
# Tous les logs
docker-compose logs -f

# Logs du backend uniquement
docker-compose logs -f backend

# Logs de la base de données
docker-compose logs -f db
```

### Arrêter les conteneurs

```bash
docker-compose stop
```

### Arrêter et supprimer les conteneurs

```bash
docker-compose down
```

### Redémarrer les conteneurs

```bash
docker-compose restart
```

### Reconstruire les images

```bash
docker-compose build
```

### Reconstruire et redémarrer

```bash
docker-compose up -d --build
```

## Accès à la base de données

### Avec un client MySQL

- **Host** : localhost
- **Port** : 3306
- **Database** : charlymatloc
- **User** : root
- **Password** : (vide)

### Avec la ligne de commande

```bash
docker exec -it sae_db mysql -u root charlymatloc
```

## Dépannage

### Les conteneurs ne démarrent pas

Vérifiez que les ports 13013, 13014 et 3306 ne sont pas déjà utilisés :

```bash
# Windows
netstat -ano | findstr :13013

# Linux/macOS
lsof -i :13013
```

### Erreur de permission (Linux)

Ajoutez votre utilisateur au groupe docker :

```bash
sudo usermod -aG docker $USER
```

Puis reconnectez-vous ou redémarrez.

### Réinitialiser complètement

Si vous voulez tout supprimer et recommencer :

```bash
docker-compose down -v
docker-compose up -d --build
```

L'option `-v` supprime également les volumes, donc la base de données sera réinitialisée.

### Voir l'état des conteneurs

```bash
docker-compose ps
```

Si un conteneur est en erreur, consultez ses logs :

```bash
docker-compose logs nom_du_conteneur
```

## Utilisateurs de test

Une fois l'application lancée, vous pouvez vous connecter avec :

**Utilisateur standard** :
- Email : `test@example.com`
- Password : `password`

**Administrateur** :
- Email : `admin@admin.com`
- Password : `password`

## URLs importantes

- **Frontend** : http://localhost:13014
- **API Backend** : http://localhost:13013/api/
- **API Health Check** : http://localhost:13013/api/health
- **Documentation API** : Voir `API.md`

## Commandes rapides de référence

```bash
# Démarrer
docker-compose up -d --build

# Arrêter
docker-compose stop

# Voir les logs
docker-compose logs -f

# Redémarrer
docker-compose restart

# Tout supprimer
docker-compose down -v
```

