<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250116170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add multilingual support fields (description_fr, mission, mission_fr, founded_year) to establishments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments ADD description_fr LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD mission LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD mission_fr LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE establishments ADD founded_year INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE establishments DROP description_fr');
        $this->addSql('ALTER TABLE establishments DROP mission');
        $this->addSql('ALTER TABLE establishments DROP mission_fr');
        $this->addSql('ALTER TABLE establishments DROP founded_year');
    }
}
