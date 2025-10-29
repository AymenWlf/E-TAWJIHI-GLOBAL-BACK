<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FinalStep;
use App\Entity\FinalStepDocument;
use App\Entity\UserFinalStepStatus;
use App\Repository\FinalStepRepository;
use App\Repository\FinalStepDocumentRepository;
use App\Repository\UserFinalStepStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/final-steps')]
class FinalStepController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FinalStepRepository $finalStepRepository,
        private FinalStepDocumentRepository $finalStepDocumentRepository,
        private UserFinalStepStatusRepository $userFinalStepStatusRepository
    ) {}

    #[Route('', name: 'get_final_steps', methods: ['GET'])]
    public function getFinalSteps(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $language = $request->query->get('language', 'en');

        // Get all active final steps ordered by order
        $finalSteps = $this->finalStepRepository->findActiveOrdered();

        // Get user's statuses for these steps
        $userStatuses = $this->userFinalStepStatusRepository->findByUser($user->getId());
        $statusMap = [];
        foreach ($userStatuses as $status) {
            $statusMap[$status->getFinalStep()->getId()] = $status;
        }

        $result = [];
        foreach ($finalSteps as $step) {
            $userStatus = $statusMap[$step->getId()] ?? null;

            // Get documents for this step
            $documents = $this->finalStepDocumentRepository->findActiveByFinalStep($step->getId());

            $stepData = [
                'id' => $step->getId(),
                'name' => $language === 'fr' ? $step->getNameFr() : $step->getNameEn(),
                'description' => $language === 'fr' ? $step->getDescriptionFr() : $step->getDescriptionEn(),
                'order' => $step->getStepOrder(),
                'status' => $userStatus ? $userStatus->getStatus() : 'pending',
                'notes' => $userStatus ? $userStatus->getNotes() : null,
                'completedAt' => $userStatus ? $userStatus->getCompletedAt() : null,
                'documents' => []
            ];

            // Add documents if any exist
            foreach ($documents as $document) {
                $stepData['documents'][] = [
                    'id' => $document->getId(),
                    'title' => $language === 'fr' ? $document->getTitleFr() : $document->getTitleEn(),
                    'filePath' => $document->getFilePath(),
                    'fileType' => $document->getFileType(),
                    'fileSize' => $document->getFileSize()
                ];
            }

            $result[] = $stepData;
        }

        return new JsonResponse($result);
    }

    #[Route('/{id}/status', name: 'update_final_step_status', methods: ['POST'])]
    public function updateFinalStepStatus(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $finalStep = $this->finalStepRepository->find($id);
        if (!$finalStep) {
            return new JsonResponse(['error' => 'Final step not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $status = $data['status'] ?? null;
        $notes = $data['notes'] ?? null;

        if (!in_array($status, ['pending', 'in_progress', 'completed', 'rejected'])) {
            return new JsonResponse(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        }

        // Find existing status or create new one
        $userStatus = $this->userFinalStepStatusRepository->findByUserAndFinalStep($user->getId(), $id);

        if (!$userStatus) {
            $userStatus = new UserFinalStepStatus();
            $userStatus->setUser($user);
            $userStatus->setFinalStep($finalStep);
        }

        $userStatus->setStatus($status);
        $userStatus->setNotes($notes);

        if ($status === 'completed') {
            $userStatus->setCompletedAt(new \DateTimeImmutable());
        }

        $this->entityManager->persist($userStatus);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'status' => $status,
            'notes' => $notes,
            'completedAt' => $userStatus->getCompletedAt()
        ]);
    }

    #[Route('/documents/{id}/download', name: 'download_final_step_document', methods: ['GET'])]
    public function downloadFinalStepDocument(int $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $document = $this->finalStepDocumentRepository->find($id);
        if (!$document || !$document->getIsActive()) {
            return new JsonResponse(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
        }

        $filePath = $document->getFilePath();
        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        $response = new Response();
        $response->headers->set('Content-Type', $document->getFileType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');
        $response->setContent(file_get_contents($filePath));

        return $response;
    }
}
