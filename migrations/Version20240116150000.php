<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add scholarship types and description fields to establishments table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE establishments ADD scholarship_types JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD scholarship_description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE establishments DROP scholarship_types');
        $this->addSql('ALTER TABLE establishments DROP scholarship_description');
    }
}
