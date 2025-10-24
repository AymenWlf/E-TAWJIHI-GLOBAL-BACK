<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250116180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add admission requirements fields to establishments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments ADD admission_requirements JSON DEFAULT NULL, ADD admission_requirements_fr JSON DEFAULT NULL, ADD english_test_requirements JSON DEFAULT NULL, ADD academic_requirements JSON DEFAULT NULL, ADD document_requirements JSON DEFAULT NULL, ADD visa_requirements JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments DROP admission_requirements, DROP admission_requirements_fr, DROP english_test_requirements, DROP academic_requirements, DROP document_requirements, DROP visa_requirements');
    }
}
