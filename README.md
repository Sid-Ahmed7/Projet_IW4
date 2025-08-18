# 🚀 FactuPro - Plateforme de Gestion de Devis & Factures

> **Plateforme complète de gestion de devis, factures et paiements pour PME avec système de wallet intégré**

[![Symfony](https://img.shields.io/badge/Symfony-6.x-blue.svg)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-purple.svg)](https://php.net/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-teal.svg)](https://tailwindcss.com/)

## 📋 Table des matières

- [🎯 Objectif du Projet](#-objectif-du-projet)
- [✨ Fonctionnalités Principales](#-fonctionnalités-principales)
- [🏗️ Architecture & Technologies](#️-architecture--technologies)
- [🚀 Installation & Démarrage](#-installation--démarrage)
- [👥 Gestion des Utilisateurs](#-gestion-des-utilisateurs)
- [💰 Système de Wallet](#-système-de-wallet)
- [📧 Notifications Automatiques](#-notifications-automatiques)
- [🔧 Administration](#-administration)
- [🛠️ Développement](#️-développement)
- [👨‍💻 Équipe de Développement](#-équipe-de-développement)

---

## 🎯 Objectif du Projet

**FactuPro** est une plateforme SaaS complète conçue pour les PME françaises souhaitant digitaliser leur processus de facturation et de gestion client. Elle offre un workflow complet de devis à paiement avec un système de wallet intégré pour gérer les gains des entreprises.

### 🎭 Cas d'usage principaux :
- **Entreprises de services** (événementiel, consulting, développement)
- **Auto-entrepreneurs** et freelances
- **PME** avec besoin de facturation récurrente
- **Plateformes** gérant les paiements pour leurs clients

---

## ✨ Fonctionnalités Principales

### 📊 Gestion Commercial
- ✅ **Création de devis** avec templates personnalisables
- ✅ **Conversion devis → facture** automatique
- ✅ **Gestion client** complète avec historique
- ✅ **Suivi des paiements** Stripe intégré
- ✅ **Rapports financiers** par entreprise/période
- ✅ **Multi-entreprises** pour un utilisateur

### 💰 Système de Wallet Avancé
- 💳 **Portefeuille numérique** par entreprise
- 🔄 **Retraits bancaires** avec validation admin
- 📈 **Historique des transactions** détaillé
- 🎯 **Dashboard financier** temps réel
- ⚡ **Notifications automatiques** à chaque étape

### 🔐 Administration & Sécurité
- 👑 **Interface admin** pour validation retraits
- 🛡️ **Gestion des rôles** (Personal/Company/Admin)
- ✉️ **Emails transactionnels** automatiques
- 📋 **Logs complets** de toutes les actions
- 🔄 **Workflow strict** de validation des paiements

### 🎨 Interface Utilisateur
- 📱 **Responsive design** (Desktop/Mobile/Tablet)
- 🌙 **Interface moderne** avec TailwindCSS
- ⚡ **Navigation intuitive** par type de compte
- 📊 **Dashboards personnalisés** par rôle
- 🔍 **Filtres avancés** et recherche

---

## 🏗️ Architecture & Technologies

### 🔧 Backend
- **Symfony 6.x** - Framework PHP robuste
- **Doctrine ORM** - Gestion BDD avancée
- **PostgreSQL** - Base de données principale
- **Stripe API** - Paiements sécurisés
- **SwiftMailer** - Emails transactionnels

### 🎨 Frontend
- **Twig** - Templates optimisés
- **TailwindCSS** - Design system moderne
- **Webpack Encore** - Build assets
- **Alpine.js** - Interactions légères
- **Chart.js** - Graphiques et stats

### 🐳 DevOps & Infrastructure
- **Docker Compose** - Environnement containerisé
- **MailHog** - Test emails en développement
- **Nginx** - Serveur web production
- **PostgreSQL** - Base de données scalable

---

## 🚀 Installation & Démarrage

### Prérequis
- Docker & Docker Compose
- Git

### Installation rapide

```bash
# 1. Cloner le projet
git clone https://github.com/Sid-Ahmed7/Projet_IW4.git
cd Projet_IW4

# 2. Démarrer les services
docker-compose up -d

# 3. Installer les dépendances
docker exec -it projet_iw4-web-1 composer install
docker exec -it projet_iw4-web-1 npm install

# 4. Configurer la base de données
docker exec -it projet_iw4-web-1 php bin/console doctrine:migrations:migrate

# 5. Compiler les assets
docker exec -it projet_iw4-web-1 npm run build
```

### 🌐 URLs d'accès
- **Application** : http://localhost:8000
- **MailHog** (emails) : http://localhost:8025
- **Base de données** : localhost:5432

---

## 👥 Gestion des Utilisateurs

### Types de Comptes

#### 👤 **Compte Personnel**
- Gestion individuelle de devis/factures
- Interface simplifiée
- Facturation directe

#### 🏢 **Compte Entreprise**
- Multi-organisations
- Wallet system complet
- Gestion équipe
- Rapports avancés

#### 👑 **Compte Admin**
- Validation des retraits
- Gestion globale utilisateurs
- Accès aux statistiques système
- Interface d'administration

### Création d'un Admin
```bash
# Via commande console
docker exec -it projet_iw4-web-1 php bin/console app:create-admin

# Ou promotion d'un utilisateur existant
docker exec -it projet_iw4-web-1 php bin/console app:promote-admin user@example.com
```

---

## 💰 Système de Wallet

### 🔄 Workflow des Retraits
1. **Demande** → L'entreprise fait une demande de retrait
2. **Notification** → L'admin reçoit un email automatique
3. **Validation** → L'admin traite la demande via interface web
4. **Virement** → Exécution du virement bancaire réel
5. **Confirmation** → Débit automatique du wallet + notifications

### 💳 Fonctionnalités
- ✅ Soldes en temps réel
- ✅ Historique complet des transactions
- ✅ Validation IBAN/BIC automatique
- ✅ Montants minimum configurables
- ✅ Rapports mensuels/annuels

### 🛡️ Sécurité
- Validation manuelle par admin
- Logs complets de toutes les opérations
- Vérification des coordonnées bancaires
- Notifications à chaque étape

---

## 📧 Notifications Automatiques

### Types d'emails
- 📝 **Nouveau devis créé**
- 💰 **Facture payée** 
- 🔔 **Demande de retrait** (admin)
- ✅ **Retrait validé**
- ❌ **Retrait refusé**
- 👤 **Inscription confirmée**

### Configuration
```bash
# Variables d'environnement
MAILER_DSN="smtp://localhost:1025"  # Développement
MAILER_DSN="gmail+smtp://user:pass@default"  # Production
```

---

## 🔧 Administration

### Interface Admin
- **URL** : `/admin/payouts`
- **Gestion retraits** : Traiter/Compléter/Refuser
- **Gestion utilisateurs** : `/admin/users`
- **Statistiques** : Volumes, montants, tendances

### Commandes Console
```bash
# Gestion des retraits
docker exec -it projet_iw4-web-1 php bin/console app:payout:manage

# Créer un admin
docker exec -it projet_iw4-web-1 php bin/console app:create-admin

# Promouvoir un utilisateur
docker exec -it projet_iw4-web-1 php bin/console app:promote-admin email@domain.com
```

---

## 🛠️ Développement

### Structure du Projet
```
├── src/
│   ├── Controller/        # Contrôleurs (routes + logique)
│   ├── Entity/           # Entités Doctrine (BDD)
│   ├── Service/          # Services métier
│   ├── Repository/       # Requêtes BDD
│   └── Command/          # Commandes console
├── templates/            # Templates Twig
├── assets/              # CSS/JS sources
├── public/              # Assets compilés
├── migrations/          # Migrations BDD
└── docker/              # Configuration Docker
```

### Commandes utiles
```bash
# Accès conteneur web
docker exec -it projet_iw4-web-1 bash

# Logs
docker logs projet_iw4-web-1 --tail=50

# Base de données
docker exec -it projet_iw4-database-1 psql -U root -d stackT1

# Cache Symfony
docker exec -it projet_iw4-web-1 php bin/console cache:clear
```

---

## 👨‍💻 Équipe de Développement

| Développeur           |  GitHub                                     |
|-----------------------|---------------------------------------------|
| **Ibrahim Ouahabi**   | [@Narutino10](https://github.com/Narutino10)|
| **Leonce Yopa**       | [@zaengetsu](https://github.com/zaengetsu)  |

---

## 📊 Statistiques du Projet

- **Durée** : Octobre 2023 - Mars 2024 (+ améliorations continues)
- **Lignes de code** : ~15,000+ lignes
- **Technologies** : 10+ frameworks/librairies
- **Fonctionnalités** : 50+ features implémentées
- **Tests** : Environnement Docker complet

---

## 📞 Support & Contact

- **Issues** : [GitHub Issues](https://github.com/Sid-Ahmed7/Projet_IW4/issues)
- **Documentation** : Voir `/docs` (à venir)
- **Email** : sidahmed.moussi@exemple.com

---

## 📄 Licence

Ce projet est développé dans le cadre académique de l'ESGI Paris.

---

*Développé avec ❤️ par l'équipe ESGI Paris - Promotion 2024*
