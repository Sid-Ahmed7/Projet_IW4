<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219152854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE negotiation DROP CONSTRAINT fk_179895989dc4ce1f');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT fk_8b27c52b9dc4ce1f');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6499dc4ce1f');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT fk_7b85d6519dc4ce1f');
        $this->addSql('CREATE TABLE company (id INT NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number INT DEFAULT NULL, tax_number INT DEFAULT NULL, siret_number INT NOT NULL, likes INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state VARCHAR(50) NOT NULL, uuid UUID NOT NULL, slug VARCHAR(255) DEFAULT NULL, verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FD17F50A6 ON company (uuid)');
        $this->addSql('COMMENT ON COLUMN company.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP TABLE companie');
        $this->addSql('DROP INDEX idx_8b27c52b9dc4ce1f');
        $this->addSql('ALTER TABLE devis RENAME COLUMN companie_id TO company_id');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8B27C52B979B1AD6 ON devis (company_id)');
        $this->addSql('DROP INDEX idx_179895989dc4ce1f');
        $this->addSql('ALTER TABLE negotiation RENAME COLUMN companie_id TO company_id');
        $this->addSql('ALTER TABLE negotiation ADD CONSTRAINT FK_17989598979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_17989598979B1AD6 ON negotiation (company_id)');
        $this->addSql('DROP INDEX idx_7b85d6519dc4ce1f');
        $this->addSql('ALTER TABLE requests RENAME COLUMN companie_id TO company_id');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7B85D651979B1AD6 ON requests (company_id)');
        $this->addSql('DROP INDEX idx_8d93d6499dc4ce1f');
        $this->addSql('ALTER TABLE "user" ADD is_verified BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN companie_id TO company_id');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649979B1AD6 ON "user" (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B979B1AD6');
        $this->addSql('ALTER TABLE negotiation DROP CONSTRAINT FK_17989598979B1AD6');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT FK_7B85D651979B1AD6');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649979B1AD6');
        $this->addSql('CREATE TABLE companie (id INT NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number INT DEFAULT NULL, tax_number INT DEFAULT NULL, siret_number INT NOT NULL, likes INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state VARCHAR(50) NOT NULL, uuid UUID NOT NULL, slug VARCHAR(255) DEFAULT NULL, verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_43348b03d17f50a6 ON companie (uuid)');
        $this->addSql('COMMENT ON COLUMN companie.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN companie.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN companie.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP INDEX IDX_17989598979B1AD6');
        $this->addSql('ALTER TABLE negotiation RENAME COLUMN company_id TO companie_id');
        $this->addSql('ALTER TABLE negotiation ADD CONSTRAINT fk_179895989dc4ce1f FOREIGN KEY (companie_id) REFERENCES companie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_179895989dc4ce1f ON negotiation (companie_id)');
        $this->addSql('DROP INDEX IDX_8B27C52B979B1AD6');
        $this->addSql('ALTER TABLE devis RENAME COLUMN company_id TO companie_id');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT fk_8b27c52b9dc4ce1f FOREIGN KEY (companie_id) REFERENCES companie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8b27c52b9dc4ce1f ON devis (companie_id)');
        $this->addSql('DROP INDEX IDX_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE "user" DROP is_verified');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN company_id TO companie_id');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6499dc4ce1f FOREIGN KEY (companie_id) REFERENCES companie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8d93d6499dc4ce1f ON "user" (companie_id)');
        $this->addSql('DROP INDEX IDX_7B85D651979B1AD6');
        $this->addSql('ALTER TABLE requests RENAME COLUMN company_id TO companie_id');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT fk_7b85d6519dc4ce1f FOREIGN KEY (companie_id) REFERENCES companie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7b85d6519dc4ce1f ON requests (companie_id)');
    }
}
