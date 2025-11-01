<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter le champ promotional_price à la table services
 */
final class Version20250101000004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add promotionalPrice field to services table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('services');
        
        // Ajouter la colonne promotional_price si elle n'existe pas déjà
        if (!$table->hasColumn('promotional_price')) {
            $table->addColumn('promotional_price', 'decimal', ['precision' => 10, 'scale' => 2, 'notnull' => false]);
        }
    }

    public function down(Schema $schema): void
    {
        // Supprimer la colonne promotional_price si la migration est annulée
        $this->addSql('ALTER TABLE services DROP COLUMN IF EXISTS promotional_price');
    }
}

