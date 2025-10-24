<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250117010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create application system tables for type A programs';
    }

    public function up(Schema $schema): void
    {
        // Create applications table
        $this->addSql('CREATE TABLE applications (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            program_id INT NOT NULL,
            agent_id INT DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            current_step INT NOT NULL,
            progress_percentage DECIMAL(5,2) NOT NULL,
            notes LONGTEXT DEFAULT NULL,
            application_data JSON DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            submitted_at DATETIME DEFAULT NULL,
            INDEX IDX_F1337F0AA76ED395 (user_id),
            INDEX IDX_F1337F0A3EB8070A (program_id),
            INDEX IDX_F1337F0A3414710B (agent_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create application_steps table
        $this->addSql('CREATE TABLE application_steps (
            id INT AUTO_INCREMENT NOT NULL,
            application_id INT NOT NULL,
            step_number INT NOT NULL,
            step_name VARCHAR(100) NOT NULL,
            step_title VARCHAR(255) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            is_completed TINYINT(1) NOT NULL,
            completed_at DATETIME DEFAULT NULL,
            step_data JSON DEFAULT NULL,
            notes LONGTEXT DEFAULT NULL,
            required_documents JSON DEFAULT NULL,
            validation_errors JSON DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_APPLICATION_STEPS_APPLICATION (application_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create application_documents table
        $this->addSql('CREATE TABLE application_documents (
            id INT AUTO_INCREMENT NOT NULL,
            application_id INT NOT NULL,
            document_type VARCHAR(50) NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            mime_type VARCHAR(50) NOT NULL,
            file_size INT NOT NULL,
            status VARCHAR(20) NOT NULL,
            notes LONGTEXT DEFAULT NULL,
            rejection_reason LONGTEXT DEFAULT NULL,
            uploaded_at DATETIME NOT NULL,
            reviewed_at DATETIME DEFAULT NULL,
            reviewed_by_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_APPLICATION_DOCUMENTS_APPLICATION (application_id),
            INDEX IDX_APPLICATION_DOCUMENTS_REVIEWED_BY (reviewed_by_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create agent_assignments table
        $this->addSql('CREATE TABLE agent_assignments (
            id INT AUTO_INCREMENT NOT NULL,
            student_id INT NOT NULL,
            agent_id INT NOT NULL,
            application_id INT DEFAULT NULL,
            status VARCHAR(20) NOT NULL,
            agent_code VARCHAR(50) DEFAULT NULL,
            notes LONGTEXT DEFAULT NULL,
            assigned_at DATETIME NOT NULL,
            completed_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_AGENT_ASSIGNMENTS_STUDENT (student_id),
            INDEX IDX_AGENT_ASSIGNMENTS_AGENT (agent_id),
            INDEX IDX_AGENT_ASSIGNMENTS_APPLICATION (application_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F1337F0AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F1337F0A3EB8070A FOREIGN KEY (program_id) REFERENCES programs (id)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F1337F0A3414710B FOREIGN KEY (agent_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE application_steps ADD CONSTRAINT FK_APPLICATION_STEPS_APPLICATION FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE application_documents ADD CONSTRAINT FK_APPLICATION_DOCUMENTS_APPLICATION FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_documents ADD CONSTRAINT FK_APPLICATION_DOCUMENTS_REVIEWED_BY FOREIGN KEY (reviewed_by_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE agent_assignments ADD CONSTRAINT FK_AGENT_ASSIGNMENTS_STUDENT FOREIGN KEY (student_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE agent_assignments ADD CONSTRAINT FK_AGENT_ASSIGNMENTS_AGENT FOREIGN KEY (agent_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE agent_assignments ADD CONSTRAINT FK_AGENT_ASSIGNMENTS_APPLICATION FOREIGN KEY (application_id) REFERENCES applications (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraints first
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F1337F0AA76ED395');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F1337F0A3EB8070A');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F1337F0A3414710B');

        $this->addSql('ALTER TABLE application_steps DROP FOREIGN KEY FK_APPLICATION_STEPS_APPLICATION');

        $this->addSql('ALTER TABLE application_documents DROP FOREIGN KEY FK_APPLICATION_DOCUMENTS_APPLICATION');
        $this->addSql('ALTER TABLE application_documents DROP FOREIGN KEY FK_APPLICATION_DOCUMENTS_REVIEWED_BY');

        $this->addSql('ALTER TABLE agent_assignments DROP FOREIGN KEY FK_AGENT_ASSIGNMENTS_STUDENT');
        $this->addSql('ALTER TABLE agent_assignments DROP FOREIGN KEY FK_AGENT_ASSIGNMENTS_AGENT');
        $this->addSql('ALTER TABLE agent_assignments DROP FOREIGN KEY FK_AGENT_ASSIGNMENTS_APPLICATION');

        // Drop tables
        $this->addSql('DROP TABLE applications');
        $this->addSql('DROP TABLE application_steps');
        $this->addSql('DROP TABLE application_documents');
        $this->addSql('DROP TABLE agent_assignments');
    }
}
