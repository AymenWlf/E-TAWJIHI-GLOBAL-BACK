<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029124946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new fields to user_profiles table for extended personal information';
    }

    public function up(Schema $schema): void
    {
        // Add new columns to user_profiles table
        $this->addSql('ALTER TABLE user_profiles 
            ADD COLUMN passport_expiration_date DATE DEFAULT NULL,
            ADD COLUMN cin_number VARCHAR(50) DEFAULT NULL,
            ADD COLUMN email VARCHAR(255) DEFAULT NULL,
            ADD COLUMN gender VARCHAR(50) DEFAULT NULL,
            ADD COLUMN marital_status VARCHAR(50) DEFAULT NULL,
            ADD COLUMN country_of_birth VARCHAR(255) DEFAULT NULL,
            ADD COLUMN city_of_birth VARCHAR(255) DEFAULT NULL,
            ADD COLUMN alternate_email VARCHAR(255) DEFAULT NULL,
            ADD COLUMN religion VARCHAR(100) DEFAULT NULL,
            ADD COLUMN native_language VARCHAR(100) DEFAULT NULL,
            ADD COLUMN chinese_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN wechat_id VARCHAR(100) DEFAULT NULL,
            ADD COLUMN skype_no VARCHAR(100) DEFAULT NULL,
            ADD COLUMN emergency_contact_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN emergency_contact_gender VARCHAR(50) DEFAULT NULL,
            ADD COLUMN emergency_contact_relationship VARCHAR(50) DEFAULT NULL,
            ADD COLUMN emergency_contact_phone VARCHAR(50) DEFAULT NULL,
            ADD COLUMN emergency_contact_email VARCHAR(255) DEFAULT NULL,
            ADD COLUMN emergency_contact_address TEXT DEFAULT NULL,
            ADD COLUMN has_work_experience TINYINT(1) DEFAULT NULL,
            ADD COLUMN work_company VARCHAR(255) DEFAULT NULL,
            ADD COLUMN work_position VARCHAR(255) DEFAULT NULL,
            ADD COLUMN work_start_date DATE DEFAULT NULL,
            ADD COLUMN work_end_date DATE DEFAULT NULL,
            ADD COLUMN work_description TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove columns from user_profiles table
        $this->addSql('ALTER TABLE user_profiles 
            DROP COLUMN passport_expiration_date,
            DROP COLUMN cin_number,
            DROP COLUMN email,
            DROP COLUMN gender,
            DROP COLUMN marital_status,
            DROP COLUMN country_of_birth,
            DROP COLUMN city_of_birth,
            DROP COLUMN alternate_email,
            DROP COLUMN religion,
            DROP COLUMN native_language,
            DROP COLUMN chinese_name,
            DROP COLUMN wechat_id,
            DROP COLUMN skype_no,
            DROP COLUMN emergency_contact_name,
            DROP COLUMN emergency_contact_gender,
            DROP COLUMN emergency_contact_relationship,
            DROP COLUMN emergency_contact_phone,
            DROP COLUMN emergency_contact_email,
            DROP COLUMN emergency_contact_address,
            DROP COLUMN has_work_experience,
            DROP COLUMN work_company,
            DROP COLUMN work_position,
            DROP COLUMN work_start_date,
            DROP COLUMN work_end_date,
            DROP COLUMN work_description');
    }
}
