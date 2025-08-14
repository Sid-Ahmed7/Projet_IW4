# Guide de déploiement FactuPro

## 🚀 Déploiement sur VPS (Recommandé)

### Prérequis sur le VPS
- Ubuntu 20.04+ ou Debian 11+
- Docker et Docker Compose
- Git
- Accès root ou sudo

### 1. Préparation du VPS

```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation de Git
sudo apt install git -y

# Installation de Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER

# Installation de Docker Compose
sudo apt install docker-compose-plugin -y

# Redémarrer la session pour prendre en compte les groupes
newgrp docker
```

### 2. Déploiement automatique

```bash
# Télécharger le script de déploiement
curl -O https://raw.githubusercontent.com/Sid-Ahmed7/Projet_IW4/feature/account-type-separation/deploy-vps.sh

# Rendre le script exécutable
chmod +x deploy-vps.sh

# Déployer en production
./deploy-vps.sh production
```

### 3. Configuration pour la production

Créer le fichier `.env.prod` dans `/var/www/factupro/` :

```bash
sudo nano /var/www/factupro/.env.prod
```

Contenu du fichier :
```env
###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=VOTRE_SECRET_UNIQUE_ICI
###< symfony/framework-bundle ###

# Base de données production
DATABASE_URL="pgsql://root:VOTRE_MOT_DE_PASSE@database:5432/stackT1"

# Configuration des emails production
FROM_EMAIL=noreply@votredomaine.com

###> symfony/mailer ###
MAILER_DSN=smtp://votre-email@gmail.com:votre-app-password@smtp.gmail.com:587?encryption=tls&auth_mode=login
###< symfony/mailer ###

# API Keys
GROQ_API_KEY="votre-clé-groq"

# Stripe Production
STRIPE_SECRET_KEY="sk_live_VOTRE_CLE_STRIPE_PRODUCTION"
```

### 4. Configuration du pare-feu

```bash
# Autoriser le port 8000 (ou configurez nginx/apache comme proxy)
sudo ufw allow 8000
sudo ufw enable
```

## 🔄 Mises à jour

Pour mettre à jour l'application :

```bash
cd /var/www/factupro
./deploy-vps.sh production
```

## 📊 Monitoring

### Voir les logs en temps réel
```bash
cd /var/www/factupro
docker-compose logs -f web
```

### Vérifier le statut
```bash
docker-compose ps
```

### Redémarrer l'application
```bash
docker-compose restart
```

## 🔧 Configuration avancée

### Utiliser un nom de domaine

1. **Configurer nginx comme proxy inverse :**

```nginx
server {
    listen 80;
    server_name votredomaine.com;
    
    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

2. **SSL avec Let's Encrypt :**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d votredomaine.com
```

## ⚠️ Sécurité

- Changez tous les mots de passe par défaut
- Utilisez des secrets forts (générez avec `openssl rand -base64 32`)
- Configurez le pare-feu
- Activez les mises à jour automatiques
- Sauvegardez régulièrement la base de données

## 📋 Checklist de déploiement

- [ ] VPS configuré avec Docker
- [ ] Fichier `.env.prod` créé avec vos vraies valeurs
- [ ] Script de déploiement exécuté
- [ ] Application accessible sur http://IP:8000
- [ ] Emails de test envoyés et reçus
- [ ] Pare-feu configuré
- [ ] Nom de domaine configuré (optionnel)
- [ ] SSL activé (optionnel)

## 🆘 Dépannage

### L'application ne démarre pas
```bash
docker-compose logs web
```

### Problème d'emails
```bash
docker exec factupro-web-1 php bin/console app:test-email
```

### Base de données
```bash
docker exec factupro-database-1 psql -U root -d stackT1 -c "SELECT COUNT(*) FROM devis;"
```
