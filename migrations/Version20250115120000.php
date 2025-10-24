<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create test_vouchers table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_vouchers (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(100) NOT NULL,
            name_fr VARCHAR(100) NOT NULL,
            vendor VARCHAR(50) NOT NULL,
            vendor_logo VARCHAR(255) NOT NULL,
            original_price NUMERIC(10, 2) NOT NULL,
            discounted_price NUMERIC(10, 2) NOT NULL,
            currency VARCHAR(3) NOT NULL,
            category VARCHAR(20) NOT NULL,
            status VARCHAR(20) NOT NULL,
            description LONGTEXT NOT NULL,
            description_fr LONGTEXT NOT NULL,
            recognition LONGTEXT NOT NULL,
            recognition_fr LONGTEXT NOT NULL,
            features JSON NOT NULL,
            features_fr JSON NOT NULL,
            validity VARCHAR(50) NOT NULL,
            validity_fr VARCHAR(50) NOT NULL,
            share_link VARCHAR(255) DEFAULT NULL,
            buy_link VARCHAR(255) DEFAULT NULL,
            icon VARCHAR(10) NOT NULL,
            color VARCHAR(20) NOT NULL,
            is_active TINYINT(1) NOT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE test_vouchers');
    }
}
