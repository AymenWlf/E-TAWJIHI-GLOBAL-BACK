<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029121050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create modification_requests table';
    }

    public function up(Schema $schema): void
    {
        // Create modification_requests table
        $this->addSql('CREATE TABLE modification_requests (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            application_id INT NOT NULL,
            reason LONGTEXT NOT NULL,
            status VARCHAR(20) NOT NULL,
            admin_response LONGTEXT DEFAULT NULL,
            admin_id INT DEFAULT NULL,
            modification_allowed_until DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            responded_at DATETIME DEFAULT NULL,
            INDEX IDX_MODIFICATION_REQUESTS_USER (user_id),
            INDEX IDX_MODIFICATION_REQUESTS_APPLICATION (application_id),
            INDEX IDX_MODIFICATION_REQUESTS_ADMIN (admin_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign keys
        $this->addSql('ALTER TABLE modification_requests ADD CONSTRAINT FK_MODIFICATION_REQUESTS_USER FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE modification_requests ADD CONSTRAINT FK_MODIFICATION_REQUESTS_APPLICATION FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE modification_requests ADD CONSTRAINT FK_MODIFICATION_REQUESTS_ADMIN FOREIGN KEY (admin_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove foreign keys
        $this->addSql('ALTER TABLE modification_requests DROP FOREIGN KEY FK_MODIFICATION_REQUESTS_USER');
        $this->addSql('ALTER TABLE modification_requests DROP FOREIGN KEY FK_MODIFICATION_REQUESTS_APPLICATION');
        $this->addSql('ALTER TABLE modification_requests DROP FOREIGN KEY FK_MODIFICATION_REQUESTS_ADMIN');

        // Drop table
        $this->addSql('DROP TABLE modification_requests');
    }
}
