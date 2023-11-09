<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109172106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD conversation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FC33F7837 FOREIGN KEY (document_id) REFERENCES document (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307F3DA5256D ON message (image_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FC33F7837 ON message (document_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F3DA5256D');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FC33F7837');
        $this->addSql('DROP INDEX IDX_B6BD307F3DA5256D');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396');
        $this->addSql('DROP INDEX IDX_B6BD307FC33F7837');
        $this->addSql('ALTER TABLE message DROP image_id');
        $this->addSql('ALTER TABLE message DROP conversation_id');
        $this->addSql('ALTER TABLE message DROP document_id');
    }
}
