#!/bin/bash

# Script de déploiement automatique pour VPS
# Usage: ./deploy-vps.sh [production|staging]

set -e  # Arrêter en cas d'erreur

ENVIRONMENT=${1:-production}
PROJECT_NAME="factupro"
REPO_URL="https://github.com/Sid-Ahmed7/Projet_IW4.git"
BRANCH="feature/account-type-separation"

echo "🚀 Déploiement de FactuPro en environnement: $ENVIRONMENT"

# Fonction de logging
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Créer le répertoire de déploiement si nécessaire
DEPLOY_DIR="/var/www/$PROJECT_NAME"
if [ ! -d "$DEPLOY_DIR" ]; then
    log "📁 Création du répertoire de déploiement: $DEPLOY_DIR"
    sudo mkdir -p "$DEPLOY_DIR"
    sudo chown $USER:$USER "$DEPLOY_DIR"
fi

# Aller dans le répertoire de déploiement
cd "$DEPLOY_DIR"

# Si c'est le premier déploiement
if [ ! -d ".git" ]; then
    log "📥 Premier déploiement - Clonage du repository"
    git clone "$REPO_URL" .
    git checkout "$BRANCH"
else
    log "🔄 Mise à jour depuis GitHub"
    # Sauvegarder les modifications locales
    git stash push -m "Auto-stash before deploy $(date)"
    
    # Récupérer les dernières modifications
    git fetch origin
    git checkout "$BRANCH"
    git pull origin "$BRANCH"
fi

# Copier le fichier d'environnement approprié
if [ "$ENVIRONMENT" = "production" ]; then
    log "⚙️ Configuration pour la production"
    if [ ! -f ".env.prod" ]; then
        log "❌ Fichier .env.prod manquant !"
        echo "Créez le fichier .env.prod avec vos valeurs de production"
        echo "Vous pouvez utiliser .env.example comme modèle"
        exit 1
    fi
    cp .env.prod .env
else
    log "⚙️ Configuration pour le staging"
    if [ ! -f ".env.staging" ]; then
        cp .env.example .env.staging
        log "📝 Fichier .env.staging créé depuis .env.example"
        log "⚠️  Modifiez .env.staging avec vos valeurs avant de redéployer"
    fi
    cp .env.staging .env
fi

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    log "❌ Docker n'est pas installé"
    log "Installez Docker: curl -fsSL https://get.docker.com -o get-docker.sh && sh get-docker.sh"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    log "❌ Docker Compose n'est pas installé"
    log "Installez Docker Compose: sudo apt-get install docker-compose-plugin"
    exit 1
fi

# Arrêter les conteneurs existants
log "🛑 Arrêt des conteneurs existants"
docker-compose down --remove-orphans || true

# Nettoyer les images inutilisées
log "🧹 Nettoyage des images Docker"
docker system prune -f

# Construire et démarrer
log "🔨 Construction et démarrage des conteneurs"
docker-compose up --build -d

# Attendre que l'application soit prête
log "⏳ Attente du démarrage de l'application..."
sleep 30

# Test de santé
log "🏥 Test de santé de l'application"
for i in {1..10}; do
    if docker exec "${PROJECT_NAME}-web-1" php bin/console about &> /dev/null; then
        log "✅ Application démarrée avec succès"
        break
    else
        if [ $i -eq 10 ]; then
            log "❌ L'application n'a pas démarré correctement"
            log "📋 Logs de l'application:"
            docker logs "${PROJECT_NAME}-web-1" --tail 50
            exit 1
        fi
        log "⏳ Tentative $i/10 - En attente..."
        sleep 10
    fi
done

# Test des emails
log "📧 Test du système d'emails"
if docker exec "${PROJECT_NAME}-web-1" php bin/console app:test-email &> /dev/null; then
    log "✅ Système d'emails fonctionnel"
else
    log "⚠️ Problème avec le système d'emails (non bloquant)"
fi

# Afficher les informations de déploiement
log "🎉 Déploiement terminé avec succès !"
log "🌐 Application disponible sur: http://$(curl -s ifconfig.me):8000"
log "📊 Statut des conteneurs:"
docker-compose ps

# Sauvegarder la version déployée
COMMIT_HASH=$(git rev-parse --short HEAD)
echo "$COMMIT_HASH" > ".deployed_version"
log "📌 Version déployée: $COMMIT_HASH"

log "📝 Pour voir les logs en temps réel:"
log "   docker-compose logs -f web"
log "📝 Pour redémarrer:"
log "   docker-compose restart"
log "📝 Pour arrêter:"
log "   docker-compose down"
