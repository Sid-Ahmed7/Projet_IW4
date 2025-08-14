#!/bin/bash

# Script de déploiement pour FactuPro
echo "🚀 Déploiement de FactuPro"

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé"
    exit 1
fi

echo "✅ Docker et Docker Compose sont installés"

# Demander l'environnement
echo "Quel environnement voulez-vous déployer ?"
echo "1) Développement (MailHog)"
echo "2) Production (Gmail)"
read -p "Choix [1-2]: " env_choice

case $env_choice in
    1)
        echo "📧 Déploiement en mode développement avec MailHog"
        ENV_FILE=".env"
        ;;
    2)
        echo "📧 Déploiement en mode production avec Gmail"
        ENV_FILE=".env.prod"
        if [ ! -f "$ENV_FILE" ]; then
            echo "❌ Fichier .env.prod non trouvé"
            echo "Créez le fichier .env.prod avec vos vraies valeurs de production"
            exit 1
        fi
        ;;
    *)
        echo "❌ Choix invalide"
        exit 1
        ;;
esac

# Arrêter les conteneurs existants
echo "🛑 Arrêt des conteneurs existants..."
docker-compose down

# Construire et démarrer
echo "🔨 Construction et démarrage des conteneurs..."
if [ "$env_choice" = "2" ]; then
    docker-compose --env-file "$ENV_FILE" up --build -d
else
    docker-compose up --build -d
fi

# Vérifier que tout fonctionne
echo "🔍 Vérification du déploiement..."
sleep 10

if docker exec projet_iw4-web-1 php bin/console about > /dev/null 2>&1; then
    echo "✅ Application démarrée avec succès"
    echo "🌐 Site web: http://localhost:8000"
    
    if [ "$env_choice" = "1" ]; then
        echo "📧 MailHog: http://localhost:8025"
    fi
    
    echo "🧪 Test des emails..."
    docker exec projet_iw4-web-1 php bin/console app:test-email
    
else
    echo "❌ Erreur lors du démarrage de l'application"
    echo "📋 Logs:"
    docker logs projet_iw4-web-1 --tail 20
    exit 1
fi

echo "🎉 Déploiement terminé avec succès !"
