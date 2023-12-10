<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109170808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE conversation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE conversation_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE document_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE negotiation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_preferences_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE conversation (id INT NOT NULL, title VARCHAR(50) NOT NULL, last_message_timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN conversation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN conversation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE conversation_user (id INT NOT NULL, users_id INT DEFAULT NULL, is_archived BOOLEAN NOT NULL, unread_count INT DEFAULT NULL, mute_notification BOOLEAN NOT NULL, state VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5AECB55567B3B43D ON conversation_user (users_id)');
        $this->addSql('CREATE TABLE document (id INT NOT NULL, urlurl VARCHAR(255) NOT NULL, filename VARCHAR(50) DEFAULT NULL, file_type VARCHAR(20) NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE image (id INT NOT NULL, url VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, devis_id INT DEFAULT NULL, stripe_payment_id VARCHAR(255) NOT NULL, payment_type VARCHAR(50) NOT NULL, vat NUMERIC(10, 0) DEFAULT NULL, payment_details TEXT DEFAULT NULL, state VARCHAR(50) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9065174441DEFADA ON invoice (devis_id)');
        $this->addSql('COMMENT ON COLUMN invoice.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, sender_id_id INT DEFAULT NULL, message_type VARCHAR(50) NOT NULL, content_text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F6061F7CF ON message (sender_id_id)');
        $this->addSql('COMMENT ON COLUMN message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN message.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE negotiation (id INT NOT NULL, devis_id INT DEFAULT NULL, Company_id INT DEFAULT NULL, initial_price NUMERIC(10, 0) NOT NULL, preview_price NUMERIC(10, 0) DEFAULT NULL, final_price NUMERIC(10, 0) DEFAULT NULL, status VARCHAR(50) NOT NULL, expiration_time_left VARCHAR(255) NOT NULL, message TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1798959841DEFADA ON negotiation (devis_id)');
        $this->addSql('CREATE INDEX IDX_179895989DC4CE1F ON negotiation (Company_id)');
        $this->addSql('COMMENT ON COLUMN negotiation.expiration_time_left IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN negotiation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN negotiation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notification (id INT NOT NULL, users_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(50) NOT NULL, message TEXT DEFAULT NULL, is_read BOOLEAN NOT NULL, link VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CA67B3B43D ON notification (users_id)');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notification.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_preferences (id INT NOT NULL, users_id INT DEFAULT NULL, langue VARCHAR(10) NOT NULL, theme VARCHAR(10) NOT NULL, two_factor_auth BOOLEAN NOT NULL, notifications BOOLEAN NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_402A6F6067B3B43D ON user_preferences (users_id)');
        $this->addSql('COMMENT ON COLUMN user_preferences.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB55567B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174441DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6061F7CF FOREIGN KEY (sender_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE negotiation ADD CONSTRAINT FK_1798959841DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE negotiation ADD CONSTRAINT FK_179895989DC4CE1F FOREIGN KEY (Company_id) REFERENCES Company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA67B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F6067B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE conversation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE conversation_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE document_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE negotiation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_preferences_id_seq CASCADE');
        $this->addSql('ALTER TABLE conversation_user DROP CONSTRAINT FK_5AECB55567B3B43D');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174441DEFADA');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F6061F7CF');
        $this->addSql('ALTER TABLE negotiation DROP CONSTRAINT FK_1798959841DEFADA');
        $this->addSql('ALTER TABLE negotiation DROP CONSTRAINT FK_179895989DC4CE1F');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA67B3B43D');
        $this->addSql('ALTER TABLE user_preferences DROP CONSTRAINT FK_402A6F6067B3B43D');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE conversation_user');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE negotiation');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE user_preferences');
    }
}
