<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour les tables de test de diagnostic
 */
final class Version20251031013356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add diagnostic test tables: diagnostic_questions and diagnostic_test_sessions';
    }

    public function up(Schema $schema): void
    {
        // Table diagnostic_questions
        $this->addSql('CREATE TABLE diagnostic_questions (
            id INT AUTO_INCREMENT NOT NULL,
            category VARCHAR(100) NOT NULL,
            question_text VARCHAR(255) NOT NULL,
            question_text_en VARCHAR(255) DEFAULT NULL,
            type VARCHAR(50) NOT NULL,
            options JSON NOT NULL,
            order_index INT NOT NULL DEFAULT 0,
            description LONGTEXT DEFAULT NULL,
            is_active TINYINT(1) DEFAULT 1 NOT NULL,
            is_required TINYINT(1) DEFAULT 0 NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_category (category),
            INDEX idx_order (order_index)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Table diagnostic_test_sessions
        $this->addSql('CREATE TABLE diagnostic_test_sessions (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            answers JSON NOT NULL,
            scores JSON DEFAULT NULL,
            diagnostic_result LONGTEXT DEFAULT NULL,
            status VARCHAR(20) DEFAULT \'in_progress\' NOT NULL,
            current_question_index INT DEFAULT 0 NOT NULL,
            started_at DATETIME NOT NULL,
            completed_at DATETIME DEFAULT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_user (user_id),
            INDEX idx_status (status),
            CONSTRAINT FK_diagnostic_test_sessions_user FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS diagnostic_test_sessions');
        $this->addSql('DROP TABLE IF EXISTS diagnostic_questions');
    }
}

