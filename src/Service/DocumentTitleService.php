<?php

namespace App\Service;

/**
 * Service pour gérer les traductions des clés de documents
 * Le title dans Document est maintenant la clé de l'input (camelCase)
 * Cette clé est utilisée pour générer le titre traduit selon la langue
 */
class DocumentTitleService
{
    /**
     * Mapping des clés vers les titres traduits
     */
    private const KEY_TO_TITLE_MAP = [
        'passport' => ['en' => 'Passport', 'fr' => 'Passeport'],
        'nationalId' => ['en' => 'National ID Card', 'fr' => 'Carte Nationale'],
        'cv' => ['en' => 'Curriculum Vitae (CV)', 'fr' => 'Curriculum Vitae (CV)'],
        'guardian1NationalId' => ['en' => 'Guardian 1 National ID', 'fr' => 'Carte Nationale Tuteur 1'],
        'guardian2NationalId' => ['en' => 'Guardian 2 National ID', 'fr' => 'Carte Nationale Tuteur 2'],
        'transcript' => ['en' => 'Transcript', 'fr' => 'Relevé de Notes'],
        'generalTranscript' => ['en' => 'General Transcript', 'fr' => 'Relevé de note général'],
        'englishTest' => ['en' => 'English Test Certificate', 'fr' => 'Certificat de Test d\'Anglais'],
        'frenchTest' => ['en' => 'French Test Certificate', 'fr' => 'Certificat de Test de Français'],
        'portfolio' => ['en' => 'Portfolio', 'fr' => 'Portfolio'],
        'baccalaureate' => ['en' => 'Baccalaureate Diploma', 'fr' => 'Diplôme du Baccalauréat'],
        'bac2' => ['en' => 'BAC+2 Diploma', 'fr' => 'Diplôme BAC+2'],
        'bac3' => ['en' => 'BAC+3 Diploma', 'fr' => 'Diplôme BAC+3'],
        'bac5' => ['en' => 'BAC+5 Diploma', 'fr' => 'Diplôme BAC+5'],
        'enrollmentCertificate' => ['en' => 'Enrollment Certificate', 'fr' => 'Attestation de Scolarité'],
        'recommendationLetter1' => ['en' => 'Recommendation Letter 1', 'fr' => 'Lettre de Recommandation 1'],
        'recommendationLetter2' => ['en' => 'Recommendation Letter 2', 'fr' => 'Lettre de Recommandation 2'],
        'motivationLetter' => ['en' => 'Motivation Letter', 'fr' => 'Lettre de Motivation'],
        'medicalHealthCheck' => ['en' => 'Medical Health Check', 'fr' => 'Certificat Médical de Santé'],
        'anthropometricRecord' => ['en' => 'Anthropometric Record (Good Conduct)', 'fr' => 'Fiche Anthropométrique (Bonne Conduite)'],
    ];

    /**
     * Convertit une clé (camelCase) vers un titre traduit
     */
    public function keyToTitle(string $key, string $language = 'en'): ?string
    {
        if (!isset(self::KEY_TO_TITLE_MAP[$key])) {
            return $key; // Retourne la clé si pas de traduction
        }

        return self::KEY_TO_TITLE_MAP[$key][$language] ?? self::KEY_TO_TITLE_MAP[$key]['en'];
    }

    /**
     * Obtient toutes les clés de documents disponibles
     */
    public function getAllKeys(): array
    {
        return array_keys(self::KEY_TO_TITLE_MAP);
    }

    /**
     * Vérifie si une clé est valide
     */
    public function isValidKey(?string $key): bool
    {
        return $key !== null && isset(self::KEY_TO_TITLE_MAP[$key]);
    }
}

