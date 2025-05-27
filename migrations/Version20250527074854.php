<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527074854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE company_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE devis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE devis_asset_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hubuser_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE plan_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reque_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE company (id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, phone_number VARCHAR(20) NOT NULL, siret_number VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_verified BOOLEAN NOT NULL, state VARCHAR(50) NOT NULL, created_by INT DEFAULT NULL, iban VARCHAR(34) DEFAULT NULL, bic VARCHAR(11) DEFAULT NULL, balance NUMERIC(10, 2) DEFAULT NULL, stripe_metadata JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FBF094FA76ED395 ON company (user_id)');
        $this->addSql('COMMENT ON COLUMN company.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN company.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE devis (id INT NOT NULL, company_id INT DEFAULT NULL, hubuser_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, state VARCHAR(50) NOT NULL, is_negotiable BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B27C52B979B1AD6 ON devis (company_id)');
        $this->addSql('CREATE INDEX IDX_8B27C52B33420145 ON devis (hubuser_id)');
        $this->addSql('COMMENT ON COLUMN devis.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN devis.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE devis_asset (id INT NOT NULL, devis_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, price INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, state VARCHAR(30) NOT NULL, size INT NOT NULL, description TEXT NOT NULL, picture1 VARCHAR(255) DEFAULT NULL, picture2 VARCHAR(255) DEFAULT NULL, picture3 VARCHAR(255) DEFAULT NULL, unit_price INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_148C477841DEFADA ON devis_asset (devis_id)');
        $this->addSql('COMMENT ON COLUMN devis_asset.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN devis_asset.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE hubuser (id INT NOT NULL, company_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, birthdate DATE DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_verified BOOLEAN NOT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31FF6CF6E7927C74 ON hubuser (email)');
        $this->addSql('CREATE INDEX IDX_31FF6CF6979B1AD6 ON hubuser (company_id)');
        $this->addSql('COMMENT ON COLUMN hubuser.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hubuser.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE image (id INT NOT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN image.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, company_id INT NOT NULL, hubuser_id INT NOT NULL, devis_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90651744979B1AD6 ON invoice (company_id)');
        $this->addSql('CREATE INDEX IDX_9065174433420145 ON invoice (hubuser_id)');
        $this->addSql('CREATE INDEX IDX_9065174441DEFADA ON invoice (devis_id)');
        $this->addSql('COMMENT ON COLUMN invoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notification (id INT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(50) NOT NULL, message TEXT DEFAULT NULL, is_read BOOLEAN NOT NULL, link VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, notification_template INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notification.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notification_user (notification_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(notification_id, user_id))');
        $this->addSql('CREATE INDEX IDX_35AF9D73EF1A9D84 ON notification_user (notification_id)');
        $this->addSql('CREATE INDEX IDX_35AF9D73A76ED395 ON notification_user (user_id)');
        $this->addSql('CREATE TABLE notification_template (id INT NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, read BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notification_template.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE plan (id INT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, duration INT NOT NULL, stripe_id VARCHAR(255) NOT NULL, features TEXT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, state VARCHAR(50) NOT NULL, stripe_plan_id VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, stripe_payment_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DD5A5B7DF675F31B ON plan (author_id)');
        $this->addSql('COMMENT ON COLUMN plan.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN plan.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN plan.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reque (id INT NOT NULL, usr_id INT DEFAULT NULL, company_id INT DEFAULT NULL, eventdate DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, operator_id INT DEFAULT NULL, event_country VARCHAR(40) NOT NULL, firstname VARCHAR(50) NOT NULL, max_budget INT DEFAULT NULL, state VARCHAR(20) NOT NULL, mail VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, companie INT NOT NULL, pic1 VARCHAR(255) DEFAULT NULL, pic�2 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_736B5980C69D3FB ON reque (usr_id)');
        $this->addSql('CREATE INDEX IDX_736B5980979B1AD6 ON reque (company_id)');
        $this->addSql('COMMENT ON COLUMN reque.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_role (id INT NOT NULL, role_name VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by INT NOT NULL, company INT NOT NULL, usr INT NOT NULL, state VARCHAR(30) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN user_role.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_role.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FA76ED395 FOREIGN KEY (user_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B33420145 FOREIGN KEY (hubuser_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_asset ADD CONSTRAINT FK_148C477841DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hubuser ADD CONSTRAINT FK_31FF6CF6979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174433420145 FOREIGN KEY (hubuser_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174441DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification_user ADD CONSTRAINT FK_35AF9D73EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification_user ADD CONSTRAINT FK_35AF9D73A76ED395 FOREIGN KEY (user_id) REFERENCES hubuser (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7DF675F31B FOREIGN KEY (author_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reque ADD CONSTRAINT FK_736B5980C69D3FB FOREIGN KEY (usr_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reque ADD CONSTRAINT FK_736B5980979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE company_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE devis_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE devis_asset_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hubuser_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_template_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE plan_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reque_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_role_id_seq CASCADE');
        $this->addSql('ALTER TABLE company DROP CONSTRAINT FK_4FBF094FA76ED395');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B979B1AD6');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B33420145');
        $this->addSql('ALTER TABLE devis_asset DROP CONSTRAINT FK_148C477841DEFADA');
        $this->addSql('ALTER TABLE hubuser DROP CONSTRAINT FK_31FF6CF6979B1AD6');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744979B1AD6');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174433420145');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174441DEFADA');
        $this->addSql('ALTER TABLE notification_user DROP CONSTRAINT FK_35AF9D73EF1A9D84');
        $this->addSql('ALTER TABLE notification_user DROP CONSTRAINT FK_35AF9D73A76ED395');
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT FK_DD5A5B7DF675F31B');
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT FK_736B5980C69D3FB');
        $this->addSql('ALTER TABLE reque DROP CONSTRAINT FK_736B5980979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE devis_asset');
        $this->addSql('DROP TABLE hubuser');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_user');
        $this->addSql('DROP TABLE notification_template');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE reque');
        $this->addSql('DROP TABLE user_role');
    }
}
