<?php

namespace App\Service;

class GradeConversionService
{
    // Grade systems mapping
    private const GRADE_SYSTEMS = [
        'CGPA_4' => [
            'name' => 'CGPA (4.0 Scale)',
            'min' => 0,
            'max' => 4.0,
            'step' => 0.01,
            'placeholder' => 'e.g., 3.75'
        ],
        'CGPA_20' => [
            'name' => 'CGPA (20 Scale)',
            'min' => 0,
            'max' => 20,
            'step' => 0.1,
            'placeholder' => 'e.g., 15.5'
        ],
        'PERCENTAGE' => [
            'name' => 'Percentage',
            'min' => 0,
            'max' => 100,
            'step' => 0.1,
            'placeholder' => 'e.g., 85.5'
        ],
        'GPA_5' => [
            'name' => 'GPA (5.0 Scale)',
            'min' => 0,
            'max' => 5.0,
            'step' => 0.01,
            'placeholder' => 'e.g., 4.2'
        ],
        'GPA_10' => [
            'name' => 'GPA (10.0 Scale)',
            'min' => 0,
            'max' => 10.0,
            'step' => 0.01,
            'placeholder' => 'e.g., 8.5'
        ]
    ];

    // Conversion matrix between different grade systems
    private const CONVERSION_MATRIX = [
        'CGPA_4' => [
            'CGPA_20' => 'multiply',
            'PERCENTAGE' => 'multiply',
            'GPA_5' => 'multiply',
            'GPA_10' => 'multiply'
        ],
        'CGPA_20' => [
            'CGPA_4' => 'divide',
            'PERCENTAGE' => 'multiply',
            'GPA_5' => 'divide',
            'GPA_10' => 'divide'
        ],
        'PERCENTAGE' => [
            'CGPA_4' => 'divide',
            'CGPA_20' => 'divide',
            'GPA_5' => 'divide',
            'GPA_10' => 'divide'
        ],
        'GPA_5' => [
            'CGPA_4' => 'multiply',
            'CGPA_20' => 'multiply',
            'PERCENTAGE' => 'multiply',
            'GPA_10' => 'multiply'
        ],
        'GPA_10' => [
            'CGPA_4' => 'multiply',
            'CGPA_20' => 'multiply',
            'PERCENTAGE' => 'multiply',
            'GPA_5' => 'multiply'
        ]
    ];

    // Conversion factors
    private const CONVERSION_FACTORS = [
        'CGPA_4_to_CGPA_20' => 5,
        'CGPA_4_to_PERCENTAGE' => 25,
        'CGPA_4_to_GPA_5' => 1.25,
        'CGPA_4_to_GPA_10' => 2.5,
        'CGPA_20_to_CGPA_4' => 0.2,
        'CGPA_20_to_PERCENTAGE' => 5,
        'CGPA_20_to_GPA_5' => 0.25,
        'CGPA_20_to_GPA_10' => 0.5,
        'PERCENTAGE_to_CGPA_4' => 0.04,
        'PERCENTAGE_to_CGPA_20' => 0.2,
        'PERCENTAGE_to_GPA_5' => 0.05,
        'PERCENTAGE_to_GPA_10' => 0.1,
        'GPA_5_to_CGPA_4' => 0.8,
        'GPA_5_to_CGPA_20' => 4,
        'GPA_5_to_PERCENTAGE' => 20,
        'GPA_5_to_GPA_10' => 2,
        'GPA_10_to_CGPA_4' => 0.4,
        'GPA_10_to_CGPA_20' => 2,
        'GPA_10_to_PERCENTAGE' => 10,
        'GPA_10_to_GPA_5' => 0.5
    ];

    /**
     * Get all available grade systems
     */
    public function getGradeSystems(): array
    {
        return self::GRADE_SYSTEMS;
    }

    /**
     * Get grade system configuration
     */
    public function getGradeSystemConfig(string $system): ?array
    {
        return self::GRADE_SYSTEMS[$system] ?? null;
    }

    /**
     * Convert grade from one system to another
     */
    public function convertGrade(float $grade, string $fromSystem, string $toSystem): float
    {
        if ($fromSystem === $toSystem) {
            return $grade;
        }

        $conversionKey = $fromSystem . '_to_' . $toSystem;
        $factor = self::CONVERSION_FACTORS[$conversionKey] ?? null;

        if ($factor === null) {
            throw new \InvalidArgumentException("Conversion from {$fromSystem} to {$toSystem} is not supported");
        }

        $convertedGrade = $grade * $factor;

        // Round to appropriate decimal places
        $config = $this->getGradeSystemConfig($toSystem);
        $step = $config['step'] ?? 0.01;
        $decimalPlaces = $step >= 1 ? 0 : ($step >= 0.1 ? 1 : 2);

        return round($convertedGrade, $decimalPlaces);
    }

    /**
     * Check if a grade meets the minimum requirement
     */
    public function meetsMinimumRequirement(float $userGrade, string $userGradeSystem, float $requiredGrade, string $requiredGradeSystem): bool
    {
        $convertedUserGrade = $this->convertGrade($userGrade, $userGradeSystem, $requiredGradeSystem);
        return $convertedUserGrade >= $requiredGrade;
    }

    /**
     * Get equivalent grade in target system
     */
    public function getEquivalentGrade(float $grade, string $fromSystem, string $toSystem): array
    {
        $convertedGrade = $this->convertGrade($grade, $fromSystem, $toSystem);
        $targetConfig = $this->getGradeSystemConfig($toSystem);

        return [
            'grade' => $convertedGrade,
            'system' => $toSystem,
            'systemName' => $targetConfig['name'],
            'min' => $targetConfig['min'],
            'max' => $targetConfig['max'],
            'step' => $targetConfig['step']
        ];
    }

    /**
     * Validate grade against system constraints
     */
    public function validateGrade(float $grade, string $system): bool
    {
        $config = $this->getGradeSystemConfig($system);
        if (!$config) {
            return false;
        }

        return $grade >= $config['min'] && $grade <= $config['max'];
    }

    /**
     * Get grade comparison result
     */
    public function compareGrades(float $userGrade, string $userSystem, float $requiredGrade, string $requiredSystem): array
    {
        $convertedUserGrade = $this->convertGrade($userGrade, $userSystem, $requiredSystem);
        $meetsRequirement = $convertedUserGrade >= $requiredGrade;
        $difference = $convertedUserGrade - $requiredGrade;

        return [
            'meetsRequirement' => $meetsRequirement,
            'userGrade' => $convertedUserGrade,
            'requiredGrade' => $requiredGrade,
            'difference' => $difference,
            'system' => $requiredSystem
        ];
    }
}
