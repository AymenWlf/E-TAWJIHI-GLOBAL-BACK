<?php

namespace App\Controller;

use App\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/public/parameters')]
class PublicParameterController extends AbstractController
{
    public function __construct(
        private readonly ParameterRepository $parameterRepository,
    ) {}

    #[Route('/all', methods: ['GET'], name: 'public_parameters_all')]
    public function getAll(Request $request): JsonResponse
    {
        try {
            $activeOnly = filter_var($request->query->get('activeOnly', 'true'), FILTER_VALIDATE_BOOLEAN);

            // Define all parameter categories
            $categories = [
                'country',
                'city',
                'language',
                'studyLevel',
                'degree',
                'currency',
                'schoolType',
                'procedureType',
                'fieldCategory',
                'field',
                'gradeSystem',
                'englishTest',
                'standardizedTest',
                'universityType',
                'studyType'
            ];

            // Single optimized query to get all parameters
            $items = $activeOnly
                ? $this->parameterRepository->findAllActiveByCategories($categories)
                : $this->parameterRepository->findBy(['category' => $categories], ['category' => 'ASC', 'sortOrder' => 'ASC', 'labelEn' => 'ASC']);

            // Group parameters by category
            $groupedData = [];
            foreach ($items as $parameter) {
                $category = $parameter->getCategory();

                // Map category names to match frontend expectations
                $categoryKey = match ($category) {
                    'country' => 'countries',
                    'city' => 'cities',
                    'language' => 'languages',
                    'studyLevel' => 'studyLevels',
                    'degree' => 'degrees',
                    'currency' => 'currencies',
                    'schoolType' => 'schoolTypes',
                    'procedureType' => 'procedureTypes',
                    'fieldCategory' => 'fieldCategories',
                    'field' => 'fields',
                    'gradeSystem' => 'gradeSystems',
                    'englishTest' => 'englishTests',
                    'standardizedTest' => 'standardizedTests',
                    'universityType' => 'universityTypes',
                    'studyType' => 'studyTypes',
                    default => $category
                };

                if (!isset($groupedData[$categoryKey])) {
                    $groupedData[$categoryKey] = [];
                }

                // Special handling for languages - return full name instead of code
                if ($category === 'language') {
                    $groupedData[$categoryKey][] = [
                        'id' => $parameter->getId(),
                        'category' => $parameter->getCategory(),
                        'code' => $parameter->getLabelEn(), // Use full name as code
                        'labelEn' => $parameter->getLabelEn(),
                        'labelFr' => $parameter->getLabelFr(),
                        'descriptionEn' => $parameter->getDescriptionEn(),
                        'descriptionFr' => $parameter->getDescriptionFr(),
                        'scoreRange' => $parameter->getScoreRange(),
                        'meta' => $parameter->getMeta(),
                        'parentCode' => $parameter->getParentCode(),
                        'isActive' => $parameter->isActive(),
                        'sortOrder' => $parameter->getSortOrder(),
                        'flag' => $parameter->getMeta()['flag'] ?? '🌐', // Extract flag from meta
                    ];
                } else {
                    $groupedData[$categoryKey][] = [
                        'id' => $parameter->getId(),
                        'category' => $parameter->getCategory(),
                        'code' => $parameter->getCode(),
                        'labelEn' => $parameter->getLabelEn(),
                        'labelFr' => $parameter->getLabelFr(),
                        'descriptionEn' => $parameter->getDescriptionEn(),
                        'descriptionFr' => $parameter->getDescriptionFr(),
                        'scoreRange' => $parameter->getScoreRange(),
                        'meta' => $parameter->getMeta(),
                        'parentCode' => $parameter->getParentCode(),
                        'isActive' => $parameter->isActive(),
                        'sortOrder' => $parameter->getSortOrder(),
                    ];
                }
            }

            // Ensure all categories are present (even if empty)
            $result = [];
            foreach ($categories as $category) {
                $categoryKey = match ($category) {
                    'country' => 'countries',
                    'city' => 'cities',
                    'language' => 'languages',
                    'studyLevel' => 'studyLevels',
                    'degree' => 'degrees',
                    'currency' => 'currencies',
                    'schoolType' => 'schoolTypes',
                    'procedureType' => 'procedureTypes',
                    'fieldCategory' => 'fieldCategories',
                    'field' => 'fields',
                    'gradeSystem' => 'gradeSystems',
                    'englishTest' => 'englishTests',
                    'standardizedTest' => 'standardizedTests',
                    'universityType' => 'universityTypes',
                    'studyType' => 'studyTypes',
                    default => $category
                };

                $result[$categoryKey] = $groupedData[$categoryKey] ?? [];
            }

            return $this->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error loading parameters: ' . $e->getMessage()
            ], 500);
        }
    }
}
