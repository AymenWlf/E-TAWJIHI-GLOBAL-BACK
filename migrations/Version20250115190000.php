<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create program_requirements table for dynamic requirements management';
    }

    public function up(Schema $schema): void
    {
        // Create program_requirements table
        $this->addSql('CREATE TABLE program_requirements (
            id INT AUTO_INCREMENT NOT NULL,
            program_id INT NOT NULL,
            type VARCHAR(100) NOT NULL,
            subtype VARCHAR(100) DEFAULT NULL,
            name VARCHAR(255) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            minimum_value NUMERIC(10, 2) DEFAULT NULL,
            maximum_value NUMERIC(10, 2) DEFAULT NULL,
            unit VARCHAR(50) DEFAULT NULL,
            system VARCHAR(50) DEFAULT NULL,
            is_required TINYINT(1) NOT NULL,
            is_active TINYINT(1) NOT NULL,
            metadata JSON DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_PROGRAM_REQUIREMENTS_PROGRAM (program_id),
            INDEX IDX_PROGRAM_REQUIREMENTS_TYPE (type),
            INDEX IDX_PROGRAM_REQUIREMENTS_SUBTYPE (subtype),
            INDEX IDX_PROGRAM_REQUIREMENTS_ACTIVE (is_active),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraint
        $this->addSql('ALTER TABLE program_requirements ADD CONSTRAINT FK_PROGRAM_REQUIREMENTS_PROGRAM FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraint
        $this->addSql('ALTER TABLE program_requirements DROP FOREIGN KEY FK_PROGRAM_REQUIREMENTS_PROGRAM');

        // Drop table
        $this->addSql('DROP TABLE program_requirements');
    }
}
