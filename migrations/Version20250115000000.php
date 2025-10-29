<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove expiry_date and status columns from qualifications table
 */
final class Version20250115000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove expiry_date and status columns from qualifications table';
    }

    public function up(Schema $schema): void
    {
        // Remove expiry_date and status columns from qualifications table
        $this->addSql('ALTER TABLE qualifications DROP COLUMN expiry_date');
        $this->addSql('ALTER TABLE qualifications DROP COLUMN status');
    }

    public function down(Schema $schema): void
    {
        // Add back expiry_date and status columns
        $this->addSql('ALTER TABLE qualifications ADD expiry_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE qualifications ADD status VARCHAR(20) DEFAULT NULL');
    }
}
