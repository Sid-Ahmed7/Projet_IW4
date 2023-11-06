<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106081338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request DROP CONSTRAINT fk_3b978f9f9dc4ce1f');
        $this->addSql('ALTER TABLE request_user DROP CONSTRAINT fk_f234f1b3427eb8a5');
        $this->addSql('ALTER TABLE request_user DROP CONSTRAINT fk_f234f1b3a76ed395');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE request_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE request (id INT NOT NULL, companie_id INT DEFAULT NULL, category VARCHAR(50) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, even_date DATE NOT NULL, response TEXT DEFAULT NULL, devis_amont NUMERIC(10, 0) DEFAULT NULL, event_location VARCHAR(255) NOT NULL, max_budget NUMERIC(10, 0) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3b978f9f9dc4ce1f ON request (companie_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_3b978f9fd17f50a6 ON request (uuid)');
        $this->addSql('COMMENT ON COLUMN request.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE request_user (request_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(request_id, user_id))');
        $this->addSql('CREATE INDEX idx_f234f1b3a76ed395 ON request_user (user_id)');
        $this->addSql('CREATE INDEX idx_f234f1b3427eb8a5 ON request_user (request_id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT fk_3b978f9f9dc4ce1f FOREIGN KEY (companie_id) REFERENCES companie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request_user ADD CONSTRAINT fk_f234f1b3427eb8a5 FOREIGN KEY (request_id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request_user ADD CONSTRAINT fk_f234f1b3a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
