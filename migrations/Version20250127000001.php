<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250127000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add document translations and language fields';
    }

    public function up(Schema $schema): void
    {
        // Add new fields to documents table
        $this->addSql('ALTER TABLE documents ADD original_language VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE documents ADD etawjihi_notes LONGTEXT DEFAULT NULL');

        // Create document_translations table
        $this->addSql('CREATE TABLE document_translations (
            id INT AUTO_INCREMENT NOT NULL,
            original_document_id INT NOT NULL,
            target_language VARCHAR(255) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            file_size INT NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT \'pending\',
            notes LONGTEXT DEFAULT NULL,
            etawjihi_notes LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            completed_at DATETIME DEFAULT NULL,
            INDEX IDX_DOCUMENT_TRANSLATIONS_ORIGINAL (original_document_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE document_translations ADD CONSTRAINT FK_DOCUMENT_TRANSLATIONS_ORIGINAL FOREIGN KEY (original_document_id) REFERENCES documents (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE document_translations');
        $this->addSql('ALTER TABLE documents DROP original_language');
        $this->addSql('ALTER TABLE documents DROP etawjihi_notes');
    }
}
