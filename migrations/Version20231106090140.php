<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106090140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE requests_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE requests (id INT NOT NULL, Company_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B85D6519DC4CE1F ON requests (Company_id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D6519DC4CE1F FOREIGN KEY (Company_id) REFERENCES Company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE requests_id_seq CASCADE');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT FK_7B85D6519DC4CE1F');
        $this->addSql('DROP TABLE requests');
    }
}
