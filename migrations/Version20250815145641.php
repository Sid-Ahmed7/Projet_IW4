<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250815145641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE payout_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE payout_request (id INT NOT NULL, wallet_id INT NOT NULL, requested_by_id INT DEFAULT NULL, processed_by_id INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, status VARCHAR(20) NOT NULL, bank_details TEXT DEFAULT NULL, iban VARCHAR(100) DEFAULT NULL, bic VARCHAR(11) DEFAULT NULL, account_holder_name VARCHAR(255) DEFAULT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, notes TEXT DEFAULT NULL, stripe_transfer_id VARCHAR(255) DEFAULT NULL, failure_reason TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5AC7D4C712520F3 ON payout_request (wallet_id)');
        $this->addSql('CREATE INDEX IDX_5AC7D4C4DA1E751 ON payout_request (requested_by_id)');
        $this->addSql('CREATE INDEX IDX_5AC7D4C2FFD4FD3 ON payout_request (processed_by_id)');
        $this->addSql('COMMENT ON COLUMN payout_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN payout_request.processed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN payout_request.completed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE wallet (id INT NOT NULL, company_id INT NOT NULL, balance NUMERIC(10, 2) NOT NULL, pending_balance NUMERIC(10, 2) NOT NULL, total_earnings NUMERIC(10, 2) NOT NULL, total_withdrawn NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C68921F979B1AD6 ON wallet (company_id)');
        $this->addSql('COMMENT ON COLUMN wallet.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN wallet.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE wallet_transaction (id INT NOT NULL, wallet_id INT NOT NULL, invoice_id INT DEFAULT NULL, payout_request_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, amount NUMERIC(10, 2) NOT NULL, source VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, metadata JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, external_reference VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7DAF972712520F3 ON wallet_transaction (wallet_id)');
        $this->addSql('CREATE INDEX IDX_7DAF9722989F1FD ON wallet_transaction (invoice_id)');
        $this->addSql('CREATE INDEX IDX_7DAF9725A3059E9 ON wallet_transaction (payout_request_id)');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE payout_request ADD CONSTRAINT FK_5AC7D4C712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payout_request ADD CONSTRAINT FK_5AC7D4C4DA1E751 FOREIGN KEY (requested_by_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payout_request ADD CONSTRAINT FK_5AC7D4C2FFD4FD3 FOREIGN KEY (processed_by_id) REFERENCES hubuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF972712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF9722989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF9725A3059E9 FOREIGN KEY (payout_request_id) REFERENCES payout_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice DROP signature_token');
        $this->addSql('ALTER TABLE invoice DROP signature_requested_at');
        $this->addSql('ALTER TABLE invoice DROP signed_at');
        $this->addSql('ALTER TABLE invoice DROP signer_name');
        $this->addSql('ALTER TABLE invoice DROP signer_email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE payout_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE wallet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE wallet_transaction_id_seq CASCADE');
        $this->addSql('ALTER TABLE payout_request DROP CONSTRAINT FK_5AC7D4C712520F3');
        $this->addSql('ALTER TABLE payout_request DROP CONSTRAINT FK_5AC7D4C4DA1E751');
        $this->addSql('ALTER TABLE payout_request DROP CONSTRAINT FK_5AC7D4C2FFD4FD3');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921F979B1AD6');
        $this->addSql('ALTER TABLE wallet_transaction DROP CONSTRAINT FK_7DAF972712520F3');
        $this->addSql('ALTER TABLE wallet_transaction DROP CONSTRAINT FK_7DAF9722989F1FD');
        $this->addSql('ALTER TABLE wallet_transaction DROP CONSTRAINT FK_7DAF9725A3059E9');
        $this->addSql('DROP TABLE payout_request');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_transaction');
        $this->addSql('ALTER TABLE invoice ADD signature_token TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signature_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signer_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD signer_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoice.signature_requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invoice.signed_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
