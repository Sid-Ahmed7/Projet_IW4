<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter les champs client au devis
 */
final class Version20250704120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajouter les champs client (nom, email, adresse, téléphone) à la table devis';
    }

    public function up(Schema $schema): void
    {
        // Ajouter les nouveaux champs client à la table devis
        $this->addSql('ALTER TABLE devis ADD client_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE devis ADD client_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE devis ADD client_address TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE devis ADD client_phone VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les champs client de la table devis
        $this->addSql('ALTER TABLE devis DROP COLUMN client_name');
        $this->addSql('ALTER TABLE devis DROP COLUMN client_email');
        $this->addSql('ALTER TABLE devis DROP COLUMN client_address');
        $this->addSql('ALTER TABLE devis DROP COLUMN client_phone');
    }
} 