<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303020729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_plan ADD usr_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_plan ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_plan ADD CONSTRAINT FK_A7DB17B4C69D3FB FOREIGN KEY (usr_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A7DB17B4C69D3FB ON user_plan (usr_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_plan DROP CONSTRAINT FK_A7DB17B4C69D3FB');
        $this->addSql('DROP INDEX IDX_A7DB17B4C69D3FB');
        $this->addSql('ALTER TABLE user_plan DROP usr_id');
        $this->addSql('ALTER TABLE user_plan DROP slug');
    }
}
