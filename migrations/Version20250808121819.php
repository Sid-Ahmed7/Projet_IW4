<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808121819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mise à jour des statuts des devis existants pour le nouveau workflow de validation';
    }

    public function up(Schema $schema): void
    {
        // Mettre à jour les anciens devis "En attente" vers "En attente de validation"
        $this->addSql("UPDATE devis SET state = 'En attente de validation' WHERE state = 'En attente'");
    }

    public function down(Schema $schema): void
    {
        // Remettre les statuts comme avant
        $this->addSql("UPDATE devis SET state = 'En attente' WHERE state = 'En attente de validation'");
    }
}
