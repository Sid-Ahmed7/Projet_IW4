<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106001736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Company (id INT NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number INT DEFAULT NULL, tax_number INT DEFAULT NULL, siret_number INT NOT NULL, likes INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state VARCHAR(50) NOT NULL, uuid UUID NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43348B03D17F50A6 ON Company (uuid)');
        $this->addSql('COMMENT ON COLUMN Company.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN Company.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN Company.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE devis (id INT NOT NULL, Company_id INT DEFAULT NULL, users_id INT DEFAULT NULL, content TEXT NOT NULL, state VARCHAR(50) NOT NULL, is_negotiable BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B27C52BD17F50A6 ON devis (uuid)');
        $this->addSql('CREATE INDEX IDX_8B27C52B9DC4CE1F ON devis (Company_id)');
        $this->addSql('CREATE INDEX IDX_8B27C52B67B3B43D ON devis (users_id)');
        $this->addSql('COMMENT ON COLUMN devis.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN devis.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN devis.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN devis.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE plan (id INT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, price NUMERIC(10, 0) NOT NULL, features TEXT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, state VARCHAR(50) NOT NULL, uuid UUID NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DD5A5B7DD17F50A6 ON plan (uuid)');
        $this->addSql('CREATE INDEX IDX_DD5A5B7DF675F31B ON plan (author_id)');
        $this->addSql('COMMENT ON COLUMN plan.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN plan.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN plan.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN plan.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE request (id INT NOT NULL, Company_id INT DEFAULT NULL, category VARCHAR(50) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, even_date DATE NOT NULL, response TEXT DEFAULT NULL, devis_amont NUMERIC(10, 0) DEFAULT NULL, event_location VARCHAR(255) NOT NULL, max_budget NUMERIC(10, 0) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B978F9FD17F50A6 ON request (uuid)');
        $this->addSql('CREATE INDEX IDX_3B978F9F9DC4CE1F ON request (Company_id)');
        $this->addSql('COMMENT ON COLUMN request.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE request_user (request_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(request_id, user_id))');
        $this->addSql('CREATE INDEX IDX_F234F1B3427EB8A5 ON request_user (request_id)');
        $this->addSql('CREATE INDEX IDX_F234F1B3A76ED395 ON request_user (user_id)');
        $this->addSql('CREATE TABLE user_plan (id INT NOT NULL, plan_id INT DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A7DB17B4D17F50A6 ON user_plan (uuid)');
        $this->addSql('CREATE INDEX IDX_A7DB17B4E899029B ON user_plan (plan_id)');
        $this->addSql('COMMENT ON COLUMN user_plan.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B9DC4CE1F FOREIGN KEY (Company_id) REFERENCES Company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B67B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F9DC4CE1F FOREIGN KEY (Company_id) REFERENCES Company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request_user ADD CONSTRAINT FK_F234F1B3427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request_user ADD CONSTRAINT FK_F234F1B3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_plan ADD CONSTRAINT FK_A7DB17B4E899029B FOREIGN KEY (plan_id) REFERENCES plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD Company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD user_plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6499DC4CE1F FOREIGN KEY (Company_id) REFERENCES Company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649B0941C91 FOREIGN KEY (user_plan_id) REFERENCES user_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6499DC4CE1F ON "user" (Company_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B0941C91 ON "user" (user_plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6499DC4CE1F');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649B0941C91');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B9DC4CE1F');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B67B3B43D');
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT FK_DD5A5B7DF675F31B');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT FK_3B978F9F9DC4CE1F');
        $this->addSql('ALTER TABLE request_user DROP CONSTRAINT FK_F234F1B3427EB8A5');
        $this->addSql('ALTER TABLE request_user DROP CONSTRAINT FK_F234F1B3A76ED395');
        $this->addSql('ALTER TABLE user_plan DROP CONSTRAINT FK_A7DB17B4E899029B');
        $this->addSql('DROP TABLE Company');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE request_user');
        $this->addSql('DROP TABLE user_plan');
        $this->addSql('DROP INDEX IDX_8D93D6499DC4CE1F');
        $this->addSql('DROP INDEX IDX_8D93D649B0941C91');
        $this->addSql('ALTER TABLE "user" DROP Company_id');
        $this->addSql('ALTER TABLE "user" DROP user_plan_id');
    }
}
