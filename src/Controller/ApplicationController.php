<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Program;
use App\Entity\User;
use App\Repository\ApplicationRepository;
use App\Repository\ProgramRepository;
use App\Service\ApplicationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/applications', name: 'app_application_')]
#[IsGranted('ROLE_USER')]
class ApplicationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ApplicationRepository $applicationRepository,
        private ProgramRepository $programRepository,
        private ApplicationService $applicationService,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getApplications(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $applications = $this->applicationRepository->findByUser($user);

        $data = [];
        foreach ($applications as $application) {
            $data[] = $this->serializeApplication($application);
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function getApplication(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $application = $this->applicationRepository->find($id);
        if (!$application || $application->getUser() !== $user) {
            return new JsonResponse(['error' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        $progress = $this->applicationService->getApplicationProgress($application);

        return new JsonResponse([
            'application' => $this->serializeApplication($application),
            'progress' => $progress
        ]);
    }

    #[Route('/by-program/{programId}', name: 'get_by_program', methods: ['GET'])]
    public function getApplicationByProgram(int $programId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $program = $this->programRepository->find($programId);
        if (!$program) {
            return new JsonResponse(['error' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }

        $application = $this->applicationRepository->findUserApplicationForProgram($user, $program);
        if (!$application) {
            return new JsonResponse(['error' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        $progress = $this->applicationService->getApplicationProgress($application);

        return new JsonResponse([
            'application' => $this->serializeApplication($application),
            'progress' => $progress
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createApplication(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $programId = $data['programId'] ?? null;

        if (!$programId) {
            return new JsonResponse(['error' => 'Program ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $program = $this->programRepository->find($programId);
        if (!$program) {
            return new JsonResponse(['error' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if program is type A
        if ($program->getUniversityType() !== 'A') {
            return new JsonResponse(['error' => 'Application system is only available for type A programs'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $application = $this->applicationService->createApplication($user, $program);

            return new JsonResponse([
                'message' => 'Application created successfully',
                'application' => $this->serializeApplication($application)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/steps/{stepNumber}', name: 'update_step', methods: ['PUT'])]
    public function updateApplicationStep(int $id, int $stepNumber, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $application = $this->applicationRepository->find($id);
        if (!$application || $application->getUser() !== $user) {
            return new JsonResponse(['error' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        try {
            $step = $this->applicationService->updateApplicationStep($application, $stepNumber, $data);

            return new JsonResponse([
                'message' => 'Step updated successfully',
                'step' => $this->serializeApplicationStep($step),
                'application' => $this->serializeApplication($application)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/agent', name: 'assign_agent', methods: ['POST'])]
    public function assignAgent(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $application = $this->applicationRepository->find($id);
        if (!$application || $application->getUser() !== $user) {
            return new JsonResponse(['error' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $agentId = $data['agentId'] ?? null;
        $agentCode = $data['agentCode'] ?? null;

        try {
            $agent = null;
            if ($agentId) {
                $agent = $this->entityManager->getRepository(User::class)->find($agentId);
                if (!$agent) {
                    return new JsonResponse(['error' => 'Agent not found'], Response::HTTP_NOT_FOUND);
                }
            }

            $assignment = $this->applicationService->assignAgent($application, $agent, $agentCode);

            return new JsonResponse([
                'message' => 'Agent assigned successfully',
                'assignment' => $this->serializeAgentAssignment($assignment),
                'application' => $this->serializeApplication($application)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/documents', name: 'upload_document', methods: ['POST'])]
    public function uploadDocument(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $application = $this->applicationRepository->find($id);
        if (!$application || $application->getUser() !== $user) {
            return new JsonResponse(['error' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        $documentType = $request->request->get('documentType');
        $uploadedFile = $request->files->get('file');

        if (!$documentType || !$uploadedFile) {
            return new JsonResponse(['error' => 'Document type and file are required'], Response::HTTP_BAD_REQUEST);
        }

        // Validate file
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());

        if (!in_array($fileExtension, $allowedTypes)) {
            return new JsonResponse(['error' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes)], Response::HTTP_BAD_REQUEST);
        }

        if ($uploadedFile->getSize() > 10 * 1024 * 1024) { // 10MB limit
            return new JsonResponse(['error' => 'File size too large. Maximum size is 10MB'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Generate unique filename
            $fileName = uniqid() . '.' . $fileExtension;
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/applications/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadedFile->move($uploadDir, $fileName);

            $fileData = [
                'fileName' => $uploadedFile->getClientOriginalName(),
                'filePath' => '/uploads/applications/' . $fileName,
                'mimeType' => $uploadedFile->getMimeType(),
                'fileSize' => $uploadedFile->getSize()
            ];

            $document = $this->applicationService->uploadDocument($application, $documentType, $fileData);

            return new JsonResponse([
                'message' => 'Document uploaded successfully',
                'document' => $this->serializeApplicationDocument($document)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/submit', name: 'submit', methods: ['POST'])]
    public function submitApplication(int $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $application = $this->applicationRepository->find($id);
        if (!$application || $application->getUser() !== $user) {
            return new JsonResponse(['error' => 'Application not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $application = $this->applicationService->submitApplication($application);

            return new JsonResponse([
                'message' => 'Application submitted successfully',
                'application' => $this->serializeApplication($application)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/steps/config', name: 'steps_config', methods: ['GET'])]
    public function getStepsConfiguration(): JsonResponse
    {
        $configurations = $this->applicationService->getAllStepConfigurations();

        return new JsonResponse($configurations);
    }

    private function serializeApplication(Application $application): array
    {
        return [
            'id' => $application->getId(),
            'status' => $application->getStatus(),
            'currentStep' => $application->getCurrentStep(),
            'progressPercentage' => $application->getProgressPercentage(),
            'notes' => $application->getNotes(),
            'createdAt' => $application->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $application->getUpdatedAt()->format('Y-m-d H:i:s'),
            'submittedAt' => $application->getSubmittedAt()?->format('Y-m-d H:i:s'),
            'program' => [
                'id' => $application->getProgram()->getId(),
                'name' => $application->getProgram()->getName(),
                'nameFr' => $application->getProgram()->getNameFr(),
                'slug' => $application->getProgram()->getSlug(),
                'establishment' => [
                    'id' => $application->getProgram()->getEstablishment()->getId(),
                    'name' => $application->getProgram()->getEstablishment()->getName(),
                    'nameFr' => $application->getProgram()->getEstablishment()->getNameFr(),
                    'slug' => $application->getProgram()->getEstablishment()->getSlug(),
                ]
            ],
            'agent' => $application->getAgent() ? [
                'id' => $application->getAgent()->getId(),
                'email' => $application->getAgent()->getEmail(),
                'firstName' => $application->getAgent()->getFirstName(),
                'lastName' => $application->getAgent()->getLastName(),
            ] : null
        ];
    }

    private function serializeApplicationStep($step): array
    {
        return [
            'id' => $step->getId(),
            'stepNumber' => $step->getStepNumber(),
            'stepName' => $step->getStepName(),
            'stepTitle' => $step->getStepTitle(),
            'description' => $step->getDescription(),
            'isCompleted' => $step->isCompleted(),
            'completedAt' => $step->getCompletedAt()?->format('Y-m-d H:i:s'),
            'stepData' => $step->getStepData(),
            'notes' => $step->getNotes(),
            'requiredDocuments' => $step->getRequiredDocuments(),
            'validationErrors' => $step->getValidationErrors(),
            'createdAt' => $step->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $step->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }

    private function serializeApplicationDocument($document): array
    {
        return [
            'id' => $document->getId(),
            'documentType' => $document->getDocumentType(),
            'fileName' => $document->getFileName(),
            'filePath' => $document->getFilePath(),
            'mimeType' => $document->getMimeType(),
            'fileSize' => $document->getFileSize(),
            'formattedFileSize' => $document->getFormattedFileSize(),
            'status' => $document->getStatus(),
            'notes' => $document->getNotes(),
            'rejectionReason' => $document->getRejectionReason(),
            'uploadedAt' => $document->getUploadedAt()->format('Y-m-d H:i:s'),
            'reviewedAt' => $document->getReviewedAt()?->format('Y-m-d H:i:s'),
            'reviewedBy' => $document->getReviewedBy() ? [
                'id' => $document->getReviewedBy()->getId(),
                'email' => $document->getReviewedBy()->getEmail(),
                'firstName' => $document->getReviewedBy()->getFirstName(),
                'lastName' => $document->getReviewedBy()->getLastName(),
            ] : null
        ];
    }

    private function serializeAgentAssignment($assignment): array
    {
        return [
            'id' => $assignment->getId(),
            'status' => $assignment->getStatus(),
            'agentCode' => $assignment->getAgentCode(),
            'notes' => $assignment->getNotes(),
            'assignedAt' => $assignment->getAssignedAt()->format('Y-m-d H:i:s'),
            'completedAt' => $assignment->getCompletedAt()?->format('Y-m-d H:i:s'),
            'agent' => [
                'id' => $assignment->getAgent()->getId(),
                'email' => $assignment->getAgent()->getEmail(),
                'firstName' => $assignment->getAgent()->getFirstName(),
                'lastName' => $assignment->getAgent()->getLastName(),
            ]
        ];
    }
}
