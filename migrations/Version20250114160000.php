<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250114160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix annual_budget column type and handle empty values';
    }

    public function up(Schema $schema): void
    {
        // First, update any empty string values to NULL
        $this->addSql("UPDATE user_profiles SET annual_budget = NULL WHERE annual_budget = '' OR annual_budget = '0'");

        // The column is already defined as DECIMAL in the previous migration
        // No need to alter the column type as it's already correct
    }

    public function down(Schema $schema): void
    {
        // No need to revert as we're just cleaning data
    }
}
