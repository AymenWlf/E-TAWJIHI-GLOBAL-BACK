<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021181410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add service_pricing field to establishments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments ADD service_pricing JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments DROP service_pricing');
    }
}
