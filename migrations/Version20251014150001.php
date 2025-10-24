<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251014150001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create parameters table for standardized values';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS parameters (
            id INT AUTO_INCREMENT NOT NULL,
            category VARCHAR(64) NOT NULL,
            code VARCHAR(128) NOT NULL,
            label_en VARCHAR(255) NOT NULL,
            label_fr VARCHAR(255) NOT NULL,
            description_en VARCHAR(1000) DEFAULT NULL,
            description_fr VARCHAR(1000) DEFAULT NULL,
            meta JSON DEFAULT NULL,
            parent_code VARCHAR(128) DEFAULT NULL,
            is_active TINYINT(1) DEFAULT 1 NOT NULL,
            sort_order INT DEFAULT 0 NOT NULL,
            UNIQUE INDEX uniq_param_category_code (category, code),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS parameters');
    }
}
