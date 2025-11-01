<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101000006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create translation_prices table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE translation_prices (
            id INT AUTO_INCREMENT NOT NULL,
            from_language VARCHAR(10) NOT NULL,
            to_language VARCHAR(10) NOT NULL,
            price NUMERIC(10, 2) NOT NULL,
            currency VARCHAR(3) NOT NULL DEFAULT \'MAD\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            UNIQUE KEY unique_language_pair (from_language, to_language)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE translation_prices');
    }
}

