<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250117000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add multilingual fields to programs table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE programs ADD name_fr VARCHAR(255) DEFAULT NULL, ADD description TEXT DEFAULT NULL, ADD description_fr TEXT DEFAULT NULL, ADD curriculum TEXT DEFAULT NULL, ADD curriculum_fr TEXT DEFAULT NULL, ADD requirements TEXT DEFAULT NULL, ADD requirements_fr TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE programs DROP name_fr, DROP description, DROP description_fr, DROP curriculum, DROP curriculum_fr, DROP requirements, DROP requirements_fr');
    }
}
