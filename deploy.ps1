# Script de déploiement PowerShell pour FactuPro
Write-Host "🚀 Déploiement de FactuPro" -ForegroundColor Green

# Vérifier Docker
try {
    docker --version | Out-Null
    docker-compose --version | Out-Null
    Write-Host "✅ Docker et Docker Compose sont installés" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker ou Docker Compose n'est pas installé" -ForegroundColor Red
    exit 1
}

# Demander l'environnement
Write-Host "Quel environnement voulez-vous déployer ?"
Write-Host "1) Développement (MailHog)"
Write-Host "2) Production (Gmail)"
$env_choice = Read-Host "Choix [1-2]"

switch ($env_choice) {
    "1" {
        Write-Host "📧 Déploiement en mode développement avec MailHog" -ForegroundColor Cyan
        $ENV_FILE = ".env"
    }
    "2" {
        Write-Host "📧 Déploiement en mode production avec Gmail" -ForegroundColor Cyan
        $ENV_FILE = ".env.prod"
        if (-not (Test-Path $ENV_FILE)) {
            Write-Host "❌ Fichier .env.prod non trouvé" -ForegroundColor Red
            Write-Host "Créez le fichier .env.prod avec vos vraies valeurs de production"
            exit 1
        }
    }
    default {
        Write-Host "❌ Choix invalide" -ForegroundColor Red
        exit 1
    }
}

# Arrêter les conteneurs existants
Write-Host "🛑 Arrêt des conteneurs existants..." -ForegroundColor Yellow
docker-compose down

# Construire et démarrer
Write-Host "🔨 Construction et démarrage des conteneurs..." -ForegroundColor Yellow
if ($env_choice -eq "2") {
    docker-compose --env-file $ENV_FILE up --build -d
} else {
    docker-compose up --build -d
}

# Vérifier que tout fonctionne
Write-Host "🔍 Vérification du déploiement..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

try {
    docker exec projet_iw4-web-1 php bin/console about | Out-Null
    Write-Host "✅ Application démarrée avec succès" -ForegroundColor Green
    Write-Host "🌐 Site web: http://localhost:8000" -ForegroundColor Cyan
    
    if ($env_choice -eq "1") {
        Write-Host "📧 MailHog: http://localhost:8025" -ForegroundColor Cyan
    }
    
    Write-Host "🧪 Test des emails..." -ForegroundColor Yellow
    docker exec projet_iw4-web-1 php bin/console app:test-email
    
} catch {
    Write-Host "❌ Erreur lors du démarrage de l'application" -ForegroundColor Red
    Write-Host "📋 Logs:" -ForegroundColor Yellow
    docker logs projet_iw4-web-1 --tail 20
    exit 1
}

Write-Host "🎉 Déploiement terminé avec succès !" -ForegroundColor Green
