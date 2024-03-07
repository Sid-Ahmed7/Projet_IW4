<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306215022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "like_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "like" (id INT NOT NULL, comp_id INT DEFAULT NULL, hazer_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC6340B34D0D3BCB ON "like" (comp_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3E8B08199 ON "like" (hazer_id)');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B34D0D3BCB FOREIGN KEY (comp_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B3E8B08199 FOREIGN KEY (hazer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "like_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B34D0D3BCB');
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B3E8B08199');
        $this->addSql('DROP TABLE "like"');
    }
}
