<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create faqs table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE faqs (
            id INT AUTO_INCREMENT NOT NULL,
            category VARCHAR(50) NOT NULL,
            question VARCHAR(200) NOT NULL,
            question_fr VARCHAR(200) NOT NULL,
            answer LONGTEXT NOT NULL,
            answer_fr LONGTEXT NOT NULL,
            sort_order INT NOT NULL,
            is_active TINYINT(1) NOT NULL,
            is_popular TINYINT(1) NOT NULL,
            icon VARCHAR(20) DEFAULT NULL,
            color VARCHAR(20) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE faqs');
    }
}
