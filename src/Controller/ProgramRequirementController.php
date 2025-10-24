<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\ProgramRequirement;
use App\Service\ProgramRequirementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/program-requirements')]
class ProgramRequirementController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProgramRequirementService $requirementService;

    public function __construct(EntityManagerInterface $entityManager, ProgramRequirementService $requirementService)
    {
        $this->entityManager = $entityManager;
        $this->requirementService = $requirementService;
    }

    #[Route('/types', name: 'api_program_requirements_types', methods: ['GET'])]
    public function getRequirementTypes(): JsonResponse
    {
        $types = $this->requirementService->getStandardizedRequirementTypes();

        return $this->json([
            'success' => true,
            'data' => $types
        ]);
    }

    #[Route('/program/{id}', name: 'api_program_requirements_by_program', methods: ['GET'])]
    public function getRequirementsByProgram(int $id): JsonResponse
    {
        $program = $this->entityManager->getRepository(Program::class)->find($id);

        if (!$program) {
            return $this->json([
                'success' => false,
                'message' => 'Program not found'
            ], 404);
        }

        // Use structured requirements instead of deprecated requirements collection
        $structuredRequirements = $program->getStructuredRequirements();

        return new JsonResponse([
            'success' => true,
            'data' => $structuredRequirements
        ]);
    }
}
