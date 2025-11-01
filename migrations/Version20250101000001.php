<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour supprimer documentKey et convertir les titres en clés camelCase
 */
final class Version20250101000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove documentKey field and convert existing titles to camelCase keys';
    }

    public function up(Schema $schema): void
    {
        // Mapping des anciens titres vers les clés camelCase
        $this->addSql("
            UPDATE documents 
            SET title = CASE
                WHEN title IN ('Passport', 'Passeport') THEN 'passport'
                WHEN title IN ('National ID Card', 'Carte Nationale', 'National ID') THEN 'nationalId'
                WHEN title IN ('CV', 'Curriculum Vitae (CV)', 'Curriculum Vitae', 'Resume') THEN 'cv'
                WHEN title IN ('Guardian 1 National ID', 'Carte Nationale Tuteur 1', 'Guardian 1 ID', 'Guardian ID 1', 'Guardian National ID 1', 'Carte Nationale du Tuteur 1') THEN 'guardian1NationalId'
                WHEN title IN ('Guardian 2 National ID', 'Carte Nationale Tuteur 2', 'Guardian 2 ID', 'Guardian ID 2') THEN 'guardian2NationalId'
                WHEN title IN ('General Transcript', 'Relevé de note général', 'Relevé de Notes', 'Transcript', 'Academic Transcript') THEN 'generalTranscript'
                WHEN title IN ('English Test Certificate', 'Certificat de Test d\'Anglais') THEN 'englishTest'
                WHEN title IN ('French Test Certificate', 'Certificat de Test de Français') THEN 'frenchTest'
                WHEN title IN ('Portfolio') THEN 'portfolio'
                WHEN title IN ('Baccalaureate Diploma', 'Diplôme du Baccalauréat', 'Baccalauréat', 'Baccalaureate') THEN 'baccalaureate'
                WHEN title IN ('BAC+2 Diploma', 'Diplôme BAC+2') THEN 'bac2'
                WHEN title IN ('BAC+3 Diploma', 'Diplôme BAC+3') THEN 'bac3'
                WHEN title IN ('BAC+5 Diploma', 'Diplôme BAC+5') THEN 'bac5'
                WHEN title IN ('Enrollment Certificate', 'Attestation de Scolarité', 'Attestation de Scolarite') THEN 'enrollmentCertificate'
                WHEN title IN ('Recommendation Letter 1', 'Lettre de Recommandation 1') THEN 'recommendationLetter1'
                WHEN title IN ('Recommendation Letter 2', 'Lettre de Recommandation 2') THEN 'recommendationLetter2'
                WHEN title IN ('Motivation Letter', 'Lettre de Motivation') THEN 'motivationLetter'
                WHEN title LIKE '%Medical Health Check%' OR title LIKE '%Certificat Médical%' OR title LIKE '%Certificat M%' THEN 'medicalHealthCheck'
                WHEN title LIKE '%Anthropometric%' OR title LIKE '%Anthropom%' OR title LIKE '%Good Conduct%' OR title LIKE '%Bonne Conduite%' THEN 'anthropometricRecord'
                ELSE title
            END
        ");
        
        // Supprimer la colonne document_key si elle existe
        $this->addSql('ALTER TABLE documents DROP COLUMN IF EXISTS document_key');
        
        // Supprimer l'index si il existe
        $this->addSql('DROP INDEX IF EXISTS idx_document_key ON documents');
    }

    public function down(Schema $schema): void
    {
        // Ajouter la colonne document_key
        $this->addSql('ALTER TABLE documents ADD document_key VARCHAR(100) DEFAULT NULL');
        
        // Recréer l'index
        $this->addSql('CREATE INDEX idx_document_key ON documents(document_key)');
    }
}

