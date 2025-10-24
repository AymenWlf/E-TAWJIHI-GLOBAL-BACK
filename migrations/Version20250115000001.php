<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add media and SEO fields to programs table';
    }

    public function up(Schema $schema): void
    {
        // Add media fields to programs table
        $this->addSql('ALTER TABLE programs ADD campus_photos JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD campus_locations JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD youtube_videos JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD brochures JSON DEFAULT NULL');

        // Add SEO fields to programs table
        $this->addSql('ALTER TABLE programs ADD seo_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD seo_description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD seo_keywords JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove media fields from programs table
        $this->addSql('ALTER TABLE programs DROP campus_photos');
        $this->addSql('ALTER TABLE programs DROP campus_locations');
        $this->addSql('ALTER TABLE programs DROP youtube_videos');
        $this->addSql('ALTER TABLE programs DROP brochures');

        // Remove SEO fields from programs table
        $this->addSql('ALTER TABLE programs DROP seo_title');
        $this->addSql('ALTER TABLE programs DROP seo_description');
        $this->addSql('ALTER TABLE programs DROP seo_keywords');
    }
}
