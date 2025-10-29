<?php

namespace App\Controller;

use App\Entity\ModificationRequest;
use App\Entity\User;
use App\Repository\ModificationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin/modification-requests', name: 'api_admin_modification_requests_')]
#[IsGranted('ROLE_ADMIN')]
class AdminModificationRequestController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ModificationRequestRepository $modificationRequestRepository,
        private SerializerInterface $serializer
    ) {}

    /**
     * Get all modification requests (pending, approved, rejected)
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $status = $request->query->get('status');

        if ($status) {
            $modificationRequests = $this->modificationRequestRepository->findBy(['status' => $status], ['createdAt' => 'DESC']);
        } else {
            $modificationRequests = $this->modificationRequestRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequests, 'json', ['groups' => ['modification_request:read']]), true)
        ]);
    }

    /**
     * Get pending modification requests
     */
    #[Route('/pending', name: 'pending', methods: ['GET'])]
    public function getPending(): JsonResponse
    {
        $modificationRequests = $this->modificationRequestRepository->findPending();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequests, 'json', ['groups' => ['modification_request:read']]), true)
        ]);
    }

    /**
     * Approve a modification request
     */
    #[Route('/{id}/approve', name: 'approve', methods: ['POST'])]
    public function approve(int $id, Request $request): JsonResponse
    {
        /** @var User $admin */
        $admin = $this->getUser();

        $modificationRequest = $this->modificationRequestRepository->find($id);

        if (!$modificationRequest) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Modification request not found'
            ], 404);
        }

        if ($modificationRequest->getStatus() !== ModificationRequest::STATUS_PENDING) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Only pending requests can be approved'
            ], 400);
        }

        $requestData = json_decode($request->getContent(), true);
        $daysAllowed = $requestData['daysAllowed'] ?? 7; // Default 7 days
        $adminResponse = $requestData['adminResponse'] ?? null;

        // Approve the request
        $modificationRequest->setStatus(ModificationRequest::STATUS_APPROVED);
        $modificationRequest->setAdmin($admin);
        $modificationRequest->setAdminResponse($adminResponse);
        $modificationRequest->setRespondedAt(new \DateTime());

        // Set modification allowed until date
        $modificationAllowedUntil = new \DateTime();
        $modificationAllowedUntil->modify("+{$daysAllowed} days");
        $modificationRequest->setModificationAllowedUntil($modificationAllowedUntil);

        $modificationRequest->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequest, 'json', ['groups' => ['modification_request:read']]), true),
            'message' => 'Modification request approved successfully'
        ]);
    }

    /**
     * Reject a modification request
     */
    #[Route('/{id}/reject', name: 'reject', methods: ['POST'])]
    public function reject(int $id, Request $request): JsonResponse
    {
        /** @var User $admin */
        $admin = $this->getUser();

        $modificationRequest = $this->modificationRequestRepository->find($id);

        if (!$modificationRequest) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Modification request not found'
            ], 404);
        }

        if ($modificationRequest->getStatus() !== ModificationRequest::STATUS_PENDING) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Only pending requests can be rejected'
            ], 400);
        }

        $requestData = json_decode($request->getContent(), true);
        $adminResponse = $requestData['adminResponse'] ?? null;

        // Reject the request
        $modificationRequest->setStatus(ModificationRequest::STATUS_REJECTED);
        $modificationRequest->setAdmin($admin);
        $modificationRequest->setAdminResponse($adminResponse);
        $modificationRequest->setRespondedAt(new \DateTime());
        $modificationRequest->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequest, 'json', ['groups' => ['modification_request:read']]), true),
            'message' => 'Modification request rejected'
        ]);
    }

    /**
     * Get a specific modification request
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $modificationRequest = $this->modificationRequestRepository->find($id);

        if (!$modificationRequest) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Modification request not found'
            ], 404);
        }

        return new JsonResponse([
            'success' => true,
            'data' => json_decode($this->serializer->serialize($modificationRequest, 'json', ['groups' => ['modification_request:read']]), true)
        ]);
    }
}
