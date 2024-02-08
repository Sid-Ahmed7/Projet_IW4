<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207145927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_role (id INT NOT NULL, role_name VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by INT NOT NULL, company INT NOT NULL, usr INT NOT NULL, state VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN user_role.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE company ALTER created_by SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_role_id_seq CASCADE');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('ALTER TABLE company ALTER created_by DROP NOT NULL');
    }
}
