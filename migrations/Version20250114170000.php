<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250114170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add detailed_scores JSON column to qualifications table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE qualifications ADD detailed_scores JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE qualifications DROP detailed_scores');
    }
}
