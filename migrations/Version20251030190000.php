<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251030190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add application_id to user_final_step_status and FK to applications (nullable)';
    }

    public function up(Schema $schema): void
    {
        // Add column if not exists
        $colExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user_final_step_status' AND COLUMN_NAME = 'application_id'"
        );
        if (!$colExists) {
            $this->addSql("ALTER TABLE user_final_step_status ADD application_id INT DEFAULT NULL");
        }

        // Create index if not exists
        $idxExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME = 'user_final_step_status' AND INDEX_NAME = 'IDX_58BBA5523E030ACD'"
        );
        if (!$idxExists) {
            $this->addSql("CREATE INDEX IDX_58BBA5523E030ACD ON user_final_step_status (application_id)");
        }

        // Add FK if not exists
        // MySQL/MariaDB does not expose constraints in a simple way; try adding and ignore if exists
        try {
            $this->addSql("ALTER TABLE user_final_step_status ADD CONSTRAINT FK_58BBA5523E030ACD FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE SET NULL");
        } catch (\Throwable $e) {
            // ignore if already exists
        }
    }

    public function down(Schema $schema): void
    {
        // Drop FK if exists
        try {
            $this->addSql('ALTER TABLE user_final_step_status DROP FOREIGN KEY FK_58BBA5523E030ACD');
        } catch (\Throwable $e) {
        }
        // Drop index if exists
        $idxExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME = 'user_final_step_status' AND INDEX_NAME = 'IDX_58BBA5523E030ACD'"
        );
        if ($idxExists) {
            $this->addSql('DROP INDEX IDX_58BBA5523E030ACD ON user_final_step_status');
        }
        // Drop column if exists
        $colExists = (bool) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user_final_step_status' AND COLUMN_NAME = 'application_id'"
        );
        if ($colExists) {
            $this->addSql('ALTER TABLE user_final_step_status DROP COLUMN application_id');
        }
    }
}
