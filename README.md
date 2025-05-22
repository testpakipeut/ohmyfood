# OhMyFood

Application web de réservation de restaurants et de composition de menus en ligne.

## Prérequis

- Docker
- Docker Compose

## Installation

1. Cloner le repository :
```bash
git clone [URL_DU_REPO]
cd OHMYFOOD
```

2. Lancer l'application avec Docker Compose :
```bash
docker-compose up -d
```

L'application sera accessible à l'adresse : http://localhost:8080

## Structure du projet

- `assets/` : Contient les ressources statiques (CSS, images, etc.)
- `config/` : Fichiers de configuration
- `includes/` : Fichiers PHP réutilisables
- `pages/` : Pages de l'application
- `rules/` : Règles de validation et de traitement

## Déploiement

Le projet est configuré pour être déployé sur Railway. Les fichiers nécessaires sont :
- `Dockerfile` : Configuration du conteneur
- `docker-compose.yml` : Configuration de l'environnement de développement

## Variables d'environnement

Les variables d'environnement suivantes doivent être configurées :
- `DB_HOST` : Hôte de la base de données
- `DB_USER` : Utilisateur de la base de données
- `DB_PASSWORD` : Mot de passe de la base de données
- `DB_NAME` : Nom de la base de données 