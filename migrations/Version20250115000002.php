<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing columns to establishments table for enriched French schools data';
    }

    public function up(Schema $schema): void
    {
        // Add missing columns to establishments table
        $this->addSql('ALTER TABLE establishments ADD name_fr VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD description_fr LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD admission_requirements JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD admission_requirements_fr JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD english_test_requirements JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD academic_requirements JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD document_requirements JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD visa_requirements JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD application_fee NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD application_fee_currency VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD living_costs NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD living_costs_currency VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the added columns
        $this->addSql('ALTER TABLE establishments DROP name_fr');
        $this->addSql('ALTER TABLE establishments DROP description_fr');
        $this->addSql('ALTER TABLE establishments DROP admission_requirements');
        $this->addSql('ALTER TABLE establishments DROP admission_requirements_fr');
        $this->addSql('ALTER TABLE establishments DROP english_test_requirements');
        $this->addSql('ALTER TABLE establishments DROP academic_requirements');
        $this->addSql('ALTER TABLE establishments DROP document_requirements');
        $this->addSql('ALTER TABLE establishments DROP visa_requirements');
        $this->addSql('ALTER TABLE establishments DROP application_fee');
        $this->addSql('ALTER TABLE establishments DROP application_fee_currency');
        $this->addSql('ALTER TABLE establishments DROP living_costs');
        $this->addSql('ALTER TABLE establishments DROP living_costs_currency');
    }
}
