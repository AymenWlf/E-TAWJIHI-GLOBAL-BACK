<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250114180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add academic qualification fields to qualifications table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE qualifications ADD country VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE qualifications ADD board VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE qualifications ADD grading_scheme VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE qualifications ADD english_score DECIMAL(5, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE qualifications DROP country');
        $this->addSql('ALTER TABLE qualifications DROP board');
        $this->addSql('ALTER TABLE qualifications DROP grading_scheme');
        $this->addSql('ALTER TABLE qualifications DROP english_score');
    }
}
