<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306213340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, company_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, uuid UUID DEFAULT NULL, title VARCHAR(40) NOT NULL, description VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C1979B1AD6 ON category (company_id)');
        $this->addSql('COMMENT ON COLUMN category.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1979B1AD6');
        $this->addSql('DROP TABLE category');
    }
}
