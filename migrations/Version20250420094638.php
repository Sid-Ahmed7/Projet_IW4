<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420094638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS "user" CASCADE');
        $this->addSql('DROP SEQUENCE IF EXISTS user_id_seq CASCADE');
        
        $this->addSql('CREATE TABLE hubuser (id SERIAL NOT NULL, company_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, birthdate DATE DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_verified BOOLEAN NOT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31FF6CF6E7927C74 ON hubuser (email)');
        $this->addSql('CREATE INDEX IDX_31FF6CF6979B1AD6 ON hubuser (company_id)');
        $this->addSql('COMMENT ON COLUMN hubuser.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hubuser.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE hubuser ADD CONSTRAINT FK_31FF6CF6979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Update devis table
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT IF EXISTS fk_8b27c52ba76ed395');
        $this->addSql('DROP INDEX IF EXISTS idx_8b27c52ba76ed395');
        $this->addSql('ALTER TABLE devis RENAME COLUMN user_id TO hubuser_id');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B33420145 FOREIGN KEY (hubuser_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8B27C52B33420145 ON devis (hubuser_id)');

        // Update invoice table
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT IF EXISTS fk_90651744a76ed395');
        $this->addSql('DROP INDEX IF EXISTS idx_90651744a76ed395');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN user_id TO hubuser_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174433420145 FOREIGN KEY (hubuser_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9065174433420145 ON invoice (hubuser_id)');

        // Update notification table
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT IF EXISTS fk_bf5476ca67b3b43d');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA67B3B43D FOREIGN KEY (users_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Update plan table
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT IF EXISTS fk_dd5a5b7df675f31b');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7DF675F31B FOREIGN KEY (author_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Update reque table
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT IF EXISTS fk_736b5980c69d3fb');
        $this->addSql('ALTER TABLE reque ADD CONSTRAINT FK_736B5980C69D3FB FOREIGN KEY (usr_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B33420145');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174433420145');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA67B3B43D');
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT FK_DD5A5B7DF675F31B');
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT FK_736B5980C69D3FB');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, company_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, picture VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_verified BOOLEAN NOT NULL, birthdate DATE DEFAULT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8d93d649979b1ad6 ON "user" (company_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hubuser DROP CONSTRAINT FK_31FF6CF6979B1AD6');
        $this->addSql('DROP TABLE hubuser');
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT fk_736b5980c69d3fb');
        $this->addSql('ALTER TABLE reque ADD CONSTRAINT fk_736b5980c69d3fb FOREIGN KEY (usr_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT fk_bf5476ca67b3b43d');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT fk_bf5476ca67b3b43d FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX IDX_9065174433420145');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN hubuser_id TO user_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_90651744a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_90651744a76ed395 ON invoice (user_id)');
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT fk_dd5a5b7df675f31b');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT fk_dd5a5b7df675f31b FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX IDX_8B27C52B33420145');
        $this->addSql('ALTER TABLE devis RENAME COLUMN hubuser_id TO user_id');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT fk_8b27c52ba76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8b27c52ba76ed395 ON devis (user_id)');
    }
}
