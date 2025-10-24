<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create suggestions table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE suggestions (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            category VARCHAR(50) NOT NULL,
            title VARCHAR(200) NOT NULL,
            description LONGTEXT NOT NULL,
            priority VARCHAR(20) NOT NULL,
            status VARCHAR(20) NOT NULL,
            attachments JSON DEFAULT NULL,
            admin_response LONGTEXT DEFAULT NULL,
            admin_response_date DATETIME DEFAULT NULL,
            admin_user_id INT DEFAULT NULL,
            votes INT NOT NULL,
            is_public TINYINT(1) NOT NULL,
            is_anonymous TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_5F2732B5A76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE suggestions');
    }
}
