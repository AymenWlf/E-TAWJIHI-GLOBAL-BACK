<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250116190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add application fee and living costs fields to establishments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments ADD application_fee DECIMAL(10,2) DEFAULT NULL, ADD application_fee_currency VARCHAR(10) DEFAULT NULL, ADD living_costs DECIMAL(10,2) DEFAULT NULL, ADD living_costs_currency VARCHAR(10) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments DROP application_fee, DROP application_fee_currency, DROP living_costs, DROP living_costs_currency');
    }
}
