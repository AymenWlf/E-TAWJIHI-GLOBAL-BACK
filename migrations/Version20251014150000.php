<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add profile management tables';
    }

    public function up(Schema $schema): void
    {
        // Create user_profiles table
        $this->addSql('CREATE TABLE user_profiles (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            first_name VARCHAR(255) DEFAULT NULL,
            last_name VARCHAR(255) DEFAULT NULL,
            country VARCHAR(255) DEFAULT NULL,
            city VARCHAR(255) DEFAULT NULL,
            nationality VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            date_of_birth DATE DEFAULT NULL,
            avatar VARCHAR(255) DEFAULT NULL,
            study_level VARCHAR(50) DEFAULT NULL,
            field_of_study VARCHAR(255) DEFAULT NULL,
            preferred_country VARCHAR(255) DEFAULT NULL,
            start_date VARCHAR(50) DEFAULT NULL,
            preferred_currency VARCHAR(10) DEFAULT NULL,
            annual_budget NUMERIC(10, 2) DEFAULT NULL,
            scholarship_required TINYINT(1) DEFAULT NULL,
            language_preferences JSON DEFAULT NULL,
            onboarding_progress JSON DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_8D93D649A76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create qualifications table
        $this->addSql('CREATE TABLE qualifications (
            id INT AUTO_INCREMENT NOT NULL,
            user_profile_id INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            institution VARCHAR(255) DEFAULT NULL,
            field VARCHAR(255) DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            grade VARCHAR(50) DEFAULT NULL,
            score NUMERIC(5, 2) DEFAULT NULL,
            score_type VARCHAR(10) DEFAULT NULL,
            expiry_date DATE DEFAULT NULL,
            status VARCHAR(20) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_4A7B0E3D6B9DD454 (user_profile_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create documents table
        $this->addSql('CREATE TABLE documents (
            id INT AUTO_INCREMENT NOT NULL,
            user_profile_id INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            file_size INT NOT NULL,
            status VARCHAR(20) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            expiry_date DATE DEFAULT NULL,
            rejection_reason LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_AC69F4746B9DD454 (user_profile_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create applications table
        $this->addSql('CREATE TABLE applications (
            id INT AUTO_INCREMENT NOT NULL,
            user_profile_id INT NOT NULL,
            university_name VARCHAR(255) NOT NULL,
            program_name VARCHAR(255) NOT NULL,
            country VARCHAR(255) DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            application_fee NUMERIC(10, 2) DEFAULT NULL,
            tuition_fee NUMERIC(10, 2) DEFAULT NULL,
            application_deadline DATE DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            notes LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_F7C966F06B9DD454 (user_profile_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create shortlist table
        $this->addSql('CREATE TABLE shortlist (
            id INT AUTO_INCREMENT NOT NULL,
            user_profile_id INT NOT NULL,
            university_name VARCHAR(255) NOT NULL,
            program_name VARCHAR(255) NOT NULL,
            country VARCHAR(255) DEFAULT NULL,
            field VARCHAR(255) DEFAULT NULL,
            level VARCHAR(50) DEFAULT NULL,
            tuition_fee NUMERIC(10, 2) DEFAULT NULL,
            currency VARCHAR(10) DEFAULT NULL,
            application_deadline DATE DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            notes LONGTEXT DEFAULT NULL,
            priority INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX IDX_8C9A2C4B6B9DD454 (user_profile_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE user_profiles ADD CONSTRAINT FK_8D93D649A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE qualifications ADD CONSTRAINT FK_4A7B0E3D6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_AC69F4746B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F06B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id)');
        $this->addSql('ALTER TABLE shortlist ADD CONSTRAINT FK_8C9A2C4B6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profiles (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraints first
        $this->addSql('ALTER TABLE user_profiles DROP FOREIGN KEY FK_8D93D649A76ED395');
        $this->addSql('ALTER TABLE qualifications DROP FOREIGN KEY FK_4A7B0E3D6B9DD454');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_AC69F4746B9DD454');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F06B9DD454');
        $this->addSql('ALTER TABLE shortlist DROP FOREIGN KEY FK_8C9A2C4B6B9DD454');

        // Drop tables
        $this->addSql('DROP TABLE shortlist');
        $this->addSql('DROP TABLE applications');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE qualifications');
        $this->addSql('DROP TABLE user_profiles');
    }
}
