# Configuration de l'environnement

## Développement local

1. Copier `.env.example` vers `.env`
2. Modifier les valeurs selon vos besoins
3. Lancer `docker-compose up -d`

Pour le développement, MailHog est utilisé pour capturer les emails :
- Interface web : http://localhost:8025
- SMTP : smtp://mailer:1025

## Production

1. Créer un fichier `.env.prod` avec vos vraies valeurs
2. Configurer votre serveur SMTP (Gmail, SendGrid, etc.)
3. Déployer avec `docker-compose --env-file .env.prod up -d`

⚠️ **Important** : Ne jamais commiter les fichiers `.env` ou `.env.prod` qui contiennent des credentials !

## Configuration des emails

### Développement (MailHog)
```
MAILER_DSN=smtp://mailer:1025
```

### Production (Gmail)
```
MAILER_DSN=smtp://votre-email@gmail.com:votre-app-password@smtp.gmail.com:587?encryption=tls&auth_mode=login
```

Pour Gmail, vous devez :
1. Activer la double authentification
2. Générer un "mot de passe d'application"
3. Utiliser ce mot de passe dans la configuration
