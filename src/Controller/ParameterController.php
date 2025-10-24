<?php

namespace App\Controller;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/parameters')]
class ParameterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ParameterRepository $parameterRepository,
    ) {}

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $category = (string) $request->query->get('category', '');
        $activeOnly = filter_var($request->query->get('activeOnly', 'true'), FILTER_VALIDATE_BOOLEAN);

        if ($category === '') {
            return $this->json(['success' => false, 'message' => 'Missing category'], 400);
        }

        $items = $activeOnly
            ? $this->parameterRepository->findActiveByCategory($category)
            : $this->parameterRepository->findBy(['category' => $category], ['sortOrder' => 'ASC', 'labelEn' => 'ASC']);

        $data = array_map(function (Parameter $p) {
            return [
                'id' => $p->getId(),
                'category' => $p->getCategory(),
                'code' => $p->getCode(),
                'labelEn' => $p->getLabelEn(),
                'labelFr' => $p->getLabelFr(),
                'descriptionEn' => $p->getDescriptionEn(),
                'descriptionFr' => $p->getDescriptionFr(),
                'scoreRange' => $p->getScoreRange(),
                'meta' => $p->getMeta(),
                'parentCode' => $p->getParentCode(),
                'isActive' => $p->isActive(),
                'sortOrder' => $p->getSortOrder(),
            ];
        }, $items);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/all', methods: ['GET'], name: 'parameters_all')]
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


    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        foreach (['category', 'code', 'labelEn', 'labelFr'] as $required) {
            if (!isset($payload[$required]) || $payload[$required] === '') {
                return $this->json(['success' => false, 'message' => "Missing field: $required"], 400);
            }
        }

        $param = (new Parameter())
            ->setCategory((string) $payload['category'])
            ->setCode((string) $payload['code'])
            ->setLabelEn((string) $payload['labelEn'])
            ->setLabelFr((string) $payload['labelFr'])
            ->setDescriptionEn(isset($payload['descriptionEn']) ? (string) $payload['descriptionEn'] : null)
            ->setDescriptionFr(isset($payload['descriptionFr']) ? (string) $payload['descriptionFr'] : null)
            ->setScoreRange(isset($payload['scoreRange']) ? (string) $payload['scoreRange'] : null)
            ->setMeta(isset($payload['meta']) ? (array) $payload['meta'] : null)
            ->setParentCode(isset($payload['parentCode']) ? (string) $payload['parentCode'] : null)
            ->setIsActive(isset($payload['isActive']) ? (bool) $payload['isActive'] : true)
            ->setSortOrder(isset($payload['sortOrder']) ? (int) $payload['sortOrder'] : 0);

        $this->em->persist($param);
        $this->em->flush();

        return $this->json(['success' => true, 'id' => $param->getId()]);
    }

    #[Route('/{id}', methods: ['PUT'], requirements: ['id' => '\\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $param = $this->parameterRepository->find($id);
        if (!$param) {
            return $this->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $payload = json_decode($request->getContent(), true) ?? [];

        if (isset($payload['category'])) $param->setCategory((string) $payload['category']);
        if (isset($payload['code'])) $param->setCode((string) $payload['code']);
        if (isset($payload['labelEn'])) $param->setLabelEn((string) $payload['labelEn']);
        if (isset($payload['labelFr'])) $param->setLabelFr((string) $payload['labelFr']);
        if (array_key_exists('descriptionEn', $payload)) $param->setDescriptionEn($payload['descriptionEn'] === null ? null : (string) $payload['descriptionEn']);
        if (array_key_exists('descriptionFr', $payload)) $param->setDescriptionFr($payload['descriptionFr'] === null ? null : (string) $payload['descriptionFr']);
        if (array_key_exists('scoreRange', $payload)) $param->setScoreRange($payload['scoreRange'] === null ? null : (string) $payload['scoreRange']);
        if (array_key_exists('meta', $payload)) $param->setMeta($payload['meta'] === null ? null : (array) $payload['meta']);
        if (array_key_exists('parentCode', $payload)) $param->setParentCode($payload['parentCode'] === null ? null : (string) $payload['parentCode']);
        if (isset($payload['isActive'])) $param->setIsActive((bool) $payload['isActive']);
        if (isset($payload['sortOrder'])) $param->setSortOrder((int) $payload['sortOrder']);

        $this->em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/{id}', methods: ['DELETE'], requirements: ['id' => '\\d+'])]
    public function delete(int $id): JsonResponse
    {
        $param = $this->parameterRepository->find($id);
        if (!$param) {
            return $this->json(['success' => false, 'message' => 'Not found'], 404);
        }

        // Soft delete behavior: deactivate
        $param->setIsActive(false);
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
