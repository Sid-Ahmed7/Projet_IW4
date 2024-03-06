<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301112332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE roles_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_roles_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_roles DROP CONSTRAINT fk_54fcd59f67b3b43d');
        $this->addSql('ALTER TABLE user_roles DROP CONSTRAINT fk_54fcd59f979b1ad6');
        $this->addSql('ALTER TABLE user_roles DROP CONSTRAINT fk_54fcd59f896dbbde');
        $this->addSql('ALTER TABLE user_roles DROP CONSTRAINT fk_54fcd59fd60322ac');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT fk_7b85d651979b1ad6');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT fk_7b85d65167b3b43d');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE requests');
        $this->addSql('ALTER TABLE company ADD created_by INT NOT NULL');
        $this->addSql('ALTER TABLE devis ADD operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD notification_template INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE roles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_roles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE roles (id INT NOT NULL, rolename VARCHAR(30) NOT NULL, role_description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN roles.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN roles.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN roles.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_roles (id INT NOT NULL, users_id INT NOT NULL, company_id INT NOT NULL, updated_by_id INT DEFAULT NULL, role_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_54fcd59fd60322ac ON user_roles (role_id)');
        $this->addSql('CREATE INDEX idx_54fcd59f896dbbde ON user_roles (updated_by_id)');
        $this->addSql('CREATE INDEX idx_54fcd59f979b1ad6 ON user_roles (company_id)');
        $this->addSql('CREATE INDEX idx_54fcd59f67b3b43d ON user_roles (users_id)');
        $this->addSql('COMMENT ON COLUMN user_roles.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_roles.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_roles.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE requests (id INT NOT NULL, company_id INT DEFAULT NULL, users_id INT DEFAULT NULL, category VARCHAR(50) NOT NULL, description TEXT NOT NULL, status VARCHAR(50) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, even_date DATE NOT NULL, response TEXT DEFAULT NULL, devis_amont NUMERIC(10, 0) DEFAULT NULL, event_location VARCHAR(255) NOT NULL, max_budget NUMERIC(10, 0) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7b85d65167b3b43d ON requests (users_id)');
        $this->addSql('CREATE INDEX idx_7b85d651979b1ad6 ON requests (company_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7b85d651d17f50a6 ON requests (uuid)');
        $this->addSql('COMMENT ON COLUMN requests.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN requests.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT fk_54fcd59f67b3b43d FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT fk_54fcd59f979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT fk_54fcd59f896dbbde FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT fk_54fcd59fd60322ac FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT fk_7b85d651979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT fk_7b85d65167b3b43d FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification DROP notification_template');
        $this->addSql('ALTER TABLE company DROP created_by');
        $this->addSql('ALTER TABLE devis DROP operator_id');
    }
}
