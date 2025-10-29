<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029120036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add submitted_data column to applications table';
    }

    public function up(Schema $schema): void
    {
        // Add submitted_data column to applications table
        $this->addSql('ALTER TABLE applications ADD submitted_data JSON DEFAULT NULL AFTER application_data');
    }

    public function down(Schema $schema): void
    {
        // Remove submitted_data column from applications table
        $this->addSql('ALTER TABLE applications DROP COLUMN submitted_data');
    }
}
