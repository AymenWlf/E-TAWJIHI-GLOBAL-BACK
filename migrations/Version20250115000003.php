<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new fields for YouTube videos, brochures, campus locations, and languages';
    }

    public function up(Schema $schema): void
    {
        // Add new JSON fields to establishments table
        $this->addSql('ALTER TABLE establishments ADD languages JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD youtube_videos JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD brochures JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD campus_locations JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD campus_photos JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD status VARCHAR(20) DEFAULT \'draft\'');
    }

    public function down(Schema $schema): void
    {
        // Remove the added fields
        $this->addSql('ALTER TABLE establishments DROP languages');
        $this->addSql('ALTER TABLE establishments DROP youtube_videos');
        $this->addSql('ALTER TABLE establishments DROP brochures');
        $this->addSql('ALTER TABLE establishments DROP campus_locations');
        $this->addSql('ALTER TABLE establishments DROP campus_photos');
        $this->addSql('ALTER TABLE establishments DROP status');
    }
}
