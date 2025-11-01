<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter les nouveaux champs de préférences
 */
final class Version20250101000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new preference fields: preferredTeachingLanguage, mainPriority, scholarshipSearch, englishTest, frenchTest';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('preferences');
        
        // Ajouter les nouvelles colonnes si elles n'existent pas déjà
        if (!$table->hasColumn('preferred_teaching_language')) {
            $table->addColumn('preferred_teaching_language', 'string', ['length' => 50, 'notnull' => false]);
        }
        if (!$table->hasColumn('main_priority')) {
            $table->addColumn('main_priority', 'string', ['length' => 100, 'notnull' => false]);
        }
        if (!$table->hasColumn('scholarship_search')) {
            $table->addColumn('scholarship_search', 'boolean', ['notnull' => false]);
        }
        if (!$table->hasColumn('english_test')) {
            $table->addColumn('english_test', 'string', ['length' => 20, 'notnull' => false]);
        }
        if (!$table->hasColumn('french_test')) {
            $table->addColumn('french_test', 'string', ['length' => 20, 'notnull' => false]);
        }
    }

    public function down(Schema $schema): void
    {
        // Supprimer les colonnes si la migration est annulée
        $this->addSql('ALTER TABLE preferences DROP preferred_teaching_language');
        $this->addSql('ALTER TABLE preferences DROP main_priority');
        $this->addSql('ALTER TABLE preferences DROP scholarship_search');
        $this->addSql('ALTER TABLE preferences DROP english_test');
        $this->addSql('ALTER TABLE preferences DROP french_test');
    }
}

