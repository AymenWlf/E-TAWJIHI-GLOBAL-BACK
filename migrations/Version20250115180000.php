<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add GPA scale and score fields to programs table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programs ADD gpa_scale VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD gpa_score DECIMAL(5,2) DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD requires_gpa TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programs DROP gpa_scale');
        $this->addSql('ALTER TABLE programs DROP gpa_score');
        $this->addSql('ALTER TABLE programs DROP requires_gpa');
    }
}
