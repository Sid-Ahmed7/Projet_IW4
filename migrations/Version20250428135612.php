<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428135612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD iban VARCHAR(34) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD bic VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD balance NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD stripe_metadata JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company DROP iban');
        $this->addSql('ALTER TABLE company DROP bic');
        $this->addSql('ALTER TABLE company DROP balance');
        $this->addSql('ALTER TABLE company DROP stripe_metadata');
    }
}
