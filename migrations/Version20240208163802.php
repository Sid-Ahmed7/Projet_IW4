<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208163802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reque_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reque (id INT NOT NULL, usr_id INT DEFAULT NULL, company_id INT DEFAULT NULL, eventdate DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, operator_id INT DEFAULT NULL, event_location VARCHAR(255) DEFAULT NULL, event_country VARCHAR(40) NOT NULL, event_city VARCHAR(40) DEFAULT NULL, event_code INT NOT NULL, lastame VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, phone_number INT NOT NULL, max_budget INT DEFAULT NULL, state VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_736B5980C69D3FB ON reque (usr_id)');
        $this->addSql('CREATE INDEX IDX_736B5980979B1AD6 ON reque (company_id)');
        $this->addSql('COMMENT ON COLUMN reque.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reque ADD CONSTRAINT FK_736B5980C69D3FB FOREIGN KEY (usr_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reque ADD CONSTRAINT FK_736B5980979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT fk_7b85d65167b3b43d');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT fk_7b85d651979b1ad6');
        $this->addSql('DROP TABLE requests');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE reque_id_seq CASCADE');
        $this->addSql('CREATE TABLE requests (id INT NOT NULL, company_id INT DEFAULT NULL, users_id INT DEFAULT NULL, category VARCHAR(50) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, even_date DATE NOT NULL, response TEXT DEFAULT NULL, devis_amont NUMERIC(10, 0) DEFAULT NULL, event_location VARCHAR(255) NOT NULL, max_budget NUMERIC(10, 0) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7b85d651979b1ad6 ON requests (company_id)');
        $this->addSql('CREATE INDEX idx_7b85d65167b3b43d ON requests (users_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7b85d651d17f50a6 ON requests (uuid)');
        $this->addSql('COMMENT ON COLUMN requests.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN requests.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT fk_7b85d65167b3b43d FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT fk_7b85d651979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT FK_736B5980C69D3FB');
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT FK_736B5980979B1AD6');
        $this->addSql('DROP TABLE reque');
    }
}
