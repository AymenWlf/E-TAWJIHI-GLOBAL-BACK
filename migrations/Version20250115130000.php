<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create complaints and complaint_messages tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE complaints (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            category VARCHAR(50) NOT NULL,
            subject VARCHAR(100) NOT NULL,
            description LONGTEXT NOT NULL,
            priority VARCHAR(20) NOT NULL,
            status VARCHAR(20) NOT NULL,
            attachments JSON DEFAULT NULL,
            related_service VARCHAR(50) DEFAULT NULL,
            related_document VARCHAR(50) DEFAULT NULL,
            related_test VARCHAR(50) DEFAULT NULL,
            admin_response LONGTEXT DEFAULT NULL,
            admin_response_date DATETIME DEFAULT NULL,
            admin_user_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_5F2732B5A76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE complaint_messages (
            id INT AUTO_INCREMENT NOT NULL,
            complaint_id INT NOT NULL,
            sender_id INT NOT NULL,
            message LONGTEXT NOT NULL,
            attachments JSON DEFAULT NULL,
            is_from_admin TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX IDX_5F2732B5E2AFE2F8 (complaint_id),
            INDEX IDX_5F2732B5F624B39D (sender_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_5F2732B5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE complaint_messages ADD CONSTRAINT FK_5F2732B5E2AFE2F8 FOREIGN KEY (complaint_id) REFERENCES complaints (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE complaint_messages ADD CONSTRAINT FK_5F2732B5F624B39D FOREIGN KEY (sender_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE complaint_messages DROP FOREIGN KEY FK_5F2732B5E2AFE2F8');
        $this->addSql('ALTER TABLE complaint_messages DROP FOREIGN KEY FK_5F2732B5F624B39D');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_5F2732B5A76ED395');
        $this->addSql('DROP TABLE complaint_messages');
        $this->addSql('DROP TABLE complaints');
    }
}
