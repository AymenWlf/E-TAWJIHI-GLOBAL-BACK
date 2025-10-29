<?php

namespace App\Controller;

use App\Entity\ModificationRequest;
use App\Entity\Application;
use App\Entity\User;
use App\Repository\ModificationRequestRepository;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/modification-requests', name: 'api_modification_requests_')]
#[IsGranted('ROLE_USER')]
class ModificationRequestController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ModificationRequestRepository $modificationRequestRepository,
        private ApplicationRepository $applicationRepository,
        private SerializerInterface $serializer
    ) {}

    /**
     * Create a modification request
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['applicationId']) || !isset($requestData['reason'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application ID and reason are required'
            ], 400);
        }

        $application = $this->applicationRepository->findByIdAndUser($requestData['applicationId'], $user);

        if (!$application) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        // Check if application is submitted
        if ($application->getStatus() !== 'submitted') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application must be submitted to request modification'
            ], 400);
        }

        // Check if there's already a pending request
        $existingRequest = $this->modificationRequestRepository->findActiveByApplication($application);
        if ($existingRequest && $existingRequest->isPending()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'A pending modification request already exists for this application'
            ], 400);
        }

        // Create new modification request
        $modificationRequest = new ModificationRequest();
        $modificationRequest->setUser($user);
        $modificationRequest->setApplication($application);
        $modificationRequest->setReason($requestData['reason']);
        $modificationRequest->setStatus(ModificationRequest::STATUS_PENDING);

        $this->entityManager->persist($modificationRequest);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequest, 'json', ['groups' => ['modification_request:read']]), true)
        ], 201);
    }

    /**
     * Get modification request status for an application
     */
    #[Route('/application/{applicationId}', name: 'get_status', methods: ['GET'])]
    public function getStatus(int $applicationId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $application = $this->applicationRepository->findByIdAndUser($applicationId, $user);

        if (!$application) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $modificationRequest = $this->modificationRequestRepository->findActiveByApplication($application);

        if (!$modificationRequest) {
            return new JsonResponse([
                'success' => true,
                'data' => null,
                'modificationAllowed' => false
            ]);
        }

        $modificationAllowed = $modificationRequest->isModificationAllowed();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequest, 'json', ['groups' => ['modification_request:read']]), true),
            'modificationAllowed' => $modificationAllowed
        ]);
    }

    /**
     * Get all modification requests for current user
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $modificationRequests = $this->modificationRequestRepository->findByUser($user);

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequests, 'json', ['groups' => ['modification_request:read']]), true)
        ]);
    }
}
