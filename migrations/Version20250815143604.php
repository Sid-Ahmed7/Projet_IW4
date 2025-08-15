<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250815143604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD signature_token TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signature_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signer_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signer_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoice.signature_requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.signed_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invoice DROP signature_token');
        $this->addSql('ALTER TABLE invoice DROP signature_requested_at');
        $this->addSql('ALTER TABLE invoice DROP signed_at');
        $this->addSql('ALTER TABLE invoice DROP signer_name');
        $this->addSql('ALTER TABLE invoice DROP signer_email');
    }
}
