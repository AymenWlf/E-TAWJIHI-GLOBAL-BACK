<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter le champ chinaFamilyMembers à la table user_profiles
 */
final class Version20250101000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add chinaFamilyMembers field to user_profiles table';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne china_family_members de type JSON
        $this->addSql('ALTER TABLE user_profiles ADD china_family_members JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la colonne china_family_members
        $this->addSql('ALTER TABLE user_profiles DROP COLUMN china_family_members');
    }
}

