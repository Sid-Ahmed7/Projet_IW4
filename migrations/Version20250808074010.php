<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808074010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Ajouter le champ account_type avec une valeur par défaut
        $this->addSql('ALTER TABLE hubuser ADD account_type VARCHAR(20) DEFAULT \'personal\'');
        
        // Mettre à jour tous les utilisateurs existants avec le type 'personal'
        $this->addSql('UPDATE hubuser SET account_type = \'personal\' WHERE account_type IS NULL');
        
        // Rendre le champ obligatoire après avoir mis à jour les données
        $this->addSql('ALTER TABLE hubuser ALTER COLUMN account_type SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hubuser DROP account_type');
    }
}
