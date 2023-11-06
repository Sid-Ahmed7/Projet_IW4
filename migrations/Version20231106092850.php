<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106092850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE requests_id_seq CASCADE');
        $this->addSql('ALTER TABLE requests ADD users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE requests ADD category VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE requests ADD description TEXT NOT NULL');
        $this->addSql('ALTER TABLE requests ADD status VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE requests ADD uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE requests ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE requests ADD even_date DATE NOT NULL');
        $this->addSql('ALTER TABLE requests ADD response TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE requests ADD devis_amont NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('ALTER TABLE requests ADD event_location VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE requests ADD max_budget NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('ALTER TABLE requests ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN requests.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN requests.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D65167B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B85D651D17F50A6 ON requests (uuid)');
        $this->addSql('CREATE INDEX IDX_7B85D65167B3B43D ON requests (users_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE requests_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT FK_7B85D65167B3B43D');
        $this->addSql('DROP INDEX UNIQ_7B85D651D17F50A6');
        $this->addSql('DROP INDEX IDX_7B85D65167B3B43D');
        $this->addSql('ALTER TABLE requests DROP users_id');
        $this->addSql('ALTER TABLE requests DROP category');
        $this->addSql('ALTER TABLE requests DROP description');
        $this->addSql('ALTER TABLE requests DROP status');
        $this->addSql('ALTER TABLE requests DROP uuid');
        $this->addSql('ALTER TABLE requests DROP created_at');
        $this->addSql('ALTER TABLE requests DROP even_date');
        $this->addSql('ALTER TABLE requests DROP response');
        $this->addSql('ALTER TABLE requests DROP devis_amont');
        $this->addSql('ALTER TABLE requests DROP event_location');
        $this->addSql('ALTER TABLE requests DROP max_budget');
        $this->addSql('ALTER TABLE requests DROP slug');
    }
}
