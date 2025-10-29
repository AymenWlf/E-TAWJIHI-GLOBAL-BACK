<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250113130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create applications table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE applications (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            program_id INT NOT NULL,
            status VARCHAR(50) NOT NULL,
            personal_info JSON NOT NULL,
            academic_info JSON NOT NULL,
            documents JSON NOT NULL,
            preferences JSON NOT NULL,
            qualifications JSON NOT NULL,
            pre_admission JSON NOT NULL,
            enrollment JSON NOT NULL,
            final_offer JSON NOT NULL,
            visa_application JSON NOT NULL,
            enroll JSON NOT NULL,
            language VARCHAR(10) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            submitted_at DATETIME DEFAULT NULL,
            INDEX IDX_F7C966F0A76ED395 (user_id),
            INDEX IDX_F7C966F03EB8070A (program_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F03EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0A76ED395');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F03EB8070A');
        $this->addSql('DROP TABLE applications');
    }
}
