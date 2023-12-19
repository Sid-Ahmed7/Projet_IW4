<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210224444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE devis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER INDEX uniq_43348b03d17f50a6 RENAME TO UNIQ_4FBF094FD17F50A6');
        $this->addSql('ALTER INDEX idx_8b27c52b9dc4ce1f RENAME TO IDX_8B27C52B979B1AD6');
        $this->addSql('ALTER INDEX idx_179895989dc4ce1f RENAME TO IDX_17989598979B1AD6');
        $this->addSql('ALTER INDEX idx_7b85d6519dc4ce1f RENAME TO IDX_7B85D651979B1AD6');
        $this->addSql('ALTER TABLE "user" ADD is_verified BOOLEAN NOT NULL');
        $this->addSql('ALTER INDEX idx_8d93d6499dc4ce1f RENAME TO IDX_8D93D649979B1AD6');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE devis_id_seq CASCADE');
        $this->addSql('ALTER INDEX idx_8b27c52b979b1ad6 RENAME TO idx_8b27c52b9dc4ce1f');
        $this->addSql('ALTER INDEX idx_17989598979b1ad6 RENAME TO idx_179895989dc4ce1f');
        $this->addSql('ALTER INDEX uniq_4fbf094fd17f50a6 RENAME TO uniq_43348b03d17f50a6');
        $this->addSql('ALTER TABLE "user" DROP is_verified');
        $this->addSql('ALTER INDEX idx_8d93d649979b1ad6 RENAME TO idx_8d93d6499dc4ce1f');
        $this->addSql('ALTER INDEX idx_7b85d651979b1ad6 RENAME TO idx_7b85d6519dc4ce1f');
    }
}
