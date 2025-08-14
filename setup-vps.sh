#!/bin/bash

echo "🚀 Configuration de FactuPro sur VPS"
echo "===================================="

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Erreur: docker-compose.yml non trouvé"
    echo "Assurez-vous d'être dans le répertoire du projet cloné"
    exit 1
fi

# Vérifier si .env.prod existe
if [ ! -f ".env.prod" ]; then
    echo "📝 Création du fichier .env.prod..."
    echo "⚠️  IMPORTANT: Vous devez modifier ce fichier avec vos vraies valeurs !"
    
    cat > .env.prod << 'EOF'
###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=CHANGEZ_CETTE_VALEUR_UNIQUE_SECRETE
APP_URL=https://factupro.ibrahimouahabi.fr
###< symfony/framework-bundle ###

# Base de données production
DATABASE_URL="pgsql://root:CHANGEZ_MOT_DE_PASSE@database:5432/stackT1"

# Configuration des emails production  
FROM_EMAIL=noreply@votredomaine.com

###> symfony/mailer ###
# Configuration Gmail pour la production
MAILER_DSN=smtp://votre-email@gmail.com:votre-app-password@smtp.gmail.com:587?encryption=tls&auth_mode=login
###< symfony/mailer ###

# API Keys
GROQ_API_KEY="votre-clé-groq-ici"

# Stripe Production
STRIPE_SECRET_KEY="sk_live_VOTRE_VRAIE_CLE_STRIPE_PRODUCTION"
EOF
    
    echo "✅ Fichier .env.prod créé"
    echo ""
    echo "🔧 ÉTAPE OBLIGATOIRE:"
    echo "   Modifiez maintenant le fichier .env.prod avec vos vraies valeurs:"
    echo "   nano .env.prod"
    echo ""
    echo "📧 Pour Gmail, utilisez vos vraies informations:"
    echo "   - Votre email Gmail"
    echo "   - Votre mot de passe d'application (pas votre mot de passe normal)"
    echo ""
    echo "🔑 Générez un secret unique avec:"
    echo "   openssl rand -base64 32"
    echo ""
    echo "Après avoir modifié .env.prod, relancez ce script."
    exit 0
fi

echo "✅ Fichier .env.prod trouvé"

# Vérifier Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Installation..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    sudo usermod -aG docker $USER
    echo "✅ Docker installé. Redémarrez votre session et relancez ce script."
    exit 0
fi

if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé. Installation..."
    sudo apt update
    sudo apt install docker-compose-plugin -y
fi

echo "✅ Docker et Docker Compose sont installés"

# Copier .env.prod vers .env
cp .env.prod .env
echo "✅ Configuration de production activée"

# Arrêter les conteneurs existants
echo "🛑 Arrêt des conteneurs existants..."
docker-compose down 2>/dev/null || true

# Construire et démarrer
echo "🔨 Construction et démarrage..."
docker-compose up --build -d

# Attendre que l'application soit prête
echo "⏳ Attente du démarrage (30 secondes)..."
sleep 30

# Vérifier le statut
echo "🔍 Vérification du statut..."
if docker exec factupro-web-1 php bin/console about &> /dev/null; then
    echo "✅ Application démarrée avec succès!"
    echo ""
    echo "🌐 Votre site est accessible sur:"
    echo "   http://$(curl -s ifconfig.me 2>/dev/null || echo 'VOTRE_IP'):8000"
    echo ""
    echo "📧 Test des emails..."
    docker exec factupro-web-1 php bin/console app:test-email
    echo ""
    echo "🎉 Déploiement terminé avec succès!"
    echo ""
    echo "📊 Commandes utiles:"
    echo "   Voir les logs: docker-compose logs -f web"
    echo "   Redémarrer: docker-compose restart"
    echo "   Arrêter: docker-compose down"
else
    echo "❌ Problème de démarrage. Logs:"
    docker-compose logs web --tail 20
fi
