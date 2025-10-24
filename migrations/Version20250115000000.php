<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250115000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add preferred destinations, intakes, and subjects columns to user_profiles table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_profiles ADD preferred_destinations JSON DEFAULT NULL, ADD preferred_intakes JSON DEFAULT NULL, ADD preferred_subjects JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_profiles DROP preferred_destinations, DROP preferred_intakes, DROP preferred_subjects');
    }
}
