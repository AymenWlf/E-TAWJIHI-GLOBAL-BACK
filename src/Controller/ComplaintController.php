<?php

namespace App\Controller;

use App\Entity\Complaint;
use App\Entity\ComplaintMessage;
use App\Repository\ComplaintRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/complaints')]
class ComplaintController extends AbstractController
{
    private ComplaintRepository $complaintRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        ComplaintRepository $complaintRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->complaintRepository = $complaintRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('', name: 'get_complaints', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getComplaints(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $status = $request->query->get('status');
            $category = $request->query->get('category');

            $complaints = $this->complaintRepository->findByUser($user);

            // Filter by status if provided
            if ($status) {
                $complaints = array_filter($complaints, function ($complaint) use ($status) {
                    return $complaint->getStatus() === $status;
                });
            }

            // Filter by category if provided
            if ($category) {
                $complaints = array_filter($complaints, function ($complaint) use ($category) {
                    return $complaint->getCategory() === $category;
                });
            }

            $formattedComplaints = [];
            foreach ($complaints as $complaint) {
                $formattedComplaints[] = [
                    'id' => $complaint->getId(),
                    'category' => $complaint->getCategory(),
                    'subject' => $complaint->getSubject(),
                    'description' => $complaint->getDescription(),
                    'priority' => $complaint->getPriority(),
                    'status' => $complaint->getStatus(),
                    'attachments' => $complaint->getAttachments(),
                    'relatedService' => $complaint->getRelatedService(),
                    'relatedDocument' => $complaint->getRelatedDocument(),
                    'relatedTest' => $complaint->getRelatedTest(),
                    'adminResponse' => $complaint->getAdminResponse(),
                    'adminResponseDate' => $complaint->getAdminResponseDate()?->format('Y-m-d H:i:s'),
                    'createdAt' => $complaint->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $complaint->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'messageCount' => $complaint->getMessages()->count()
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $formattedComplaints,
                'message' => 'Complaints retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve complaints: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'get_complaint', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getComplaint(int $id): JsonResponse
    {
        try {
            $user = $this->getUser();
            $complaint = $this->complaintRepository->find($id);

            if (!$complaint) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Complaint not found'
                ], 404);
            }

            // Check if user owns this complaint or is admin
            if ($complaint->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $messages = [];
            foreach ($complaint->getMessages() as $message) {
                $messages[] = [
                    'id' => $message->getId(),
                    'message' => $message->getMessage(),
                    'attachments' => $message->getAttachments(),
                    'isFromAdmin' => $message->getIsFromAdmin(),
                    'senderName' => $message->getSender()->getFirstName() . ' ' . $message->getSender()->getLastName(),
                    'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            $formattedComplaint = [
                'id' => $complaint->getId(),
                'category' => $complaint->getCategory(),
                'subject' => $complaint->getSubject(),
                'description' => $complaint->getDescription(),
                'priority' => $complaint->getPriority(),
                'status' => $complaint->getStatus(),
                'attachments' => $complaint->getAttachments(),
                'relatedService' => $complaint->getRelatedService(),
                'relatedDocument' => $complaint->getRelatedDocument(),
                'relatedTest' => $complaint->getRelatedTest(),
                'adminResponse' => $complaint->getAdminResponse(),
                'adminResponseDate' => $complaint->getAdminResponseDate()?->format('Y-m-d H:i:s'),
                'createdAt' => $complaint->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $complaint->getUpdatedAt()->format('Y-m-d H:i:s'),
                'messages' => $messages
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $formattedComplaint,
                'message' => 'Complaint retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve complaint: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('', name: 'create_complaint', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createComplaint(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            $complaint = new Complaint();
            $complaint->setUser($user);
            $complaint->setCategory($data['category'] ?? '');
            $complaint->setSubject($data['subject'] ?? '');
            $complaint->setDescription($data['description'] ?? '');
            $complaint->setPriority($data['priority'] ?? 'medium');
            $complaint->setStatus('open');
            $complaint->setAttachments($data['attachments'] ?? null);
            $complaint->setRelatedService($data['relatedService'] ?? null);
            $complaint->setRelatedDocument($data['relatedDocument'] ?? null);
            $complaint->setRelatedTest($data['relatedTest'] ?? null);

            $this->entityManager->persist($complaint);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'data' => ['id' => $complaint->getId()],
                'message' => 'Complaint created successfully'
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to create complaint: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}/messages', name: 'add_complaint_message', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addMessage(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            $complaint = $this->complaintRepository->find($id);
            if (!$complaint) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Complaint not found'
                ], 404);
            }

            // Check if user owns this complaint or is admin
            if ($complaint->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $message = new ComplaintMessage();
            $message->setComplaint($complaint);
            $message->setSender($user);
            $message->setMessage($data['message'] ?? '');
            $message->setAttachments($data['attachments'] ?? null);
            $message->setIsFromAdmin($this->isGranted('ROLE_ADMIN'));

            $complaint->addMessage($message);
            $complaint->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'data' => ['id' => $message->getId()],
                'message' => 'Message added successfully'
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to add message: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'update_complaint', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateComplaint(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            $complaint = $this->complaintRepository->find($id);
            if (!$complaint) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Complaint not found'
                ], 404);
            }

            // Check if user owns this complaint or is admin
            if ($complaint->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Only allow certain fields to be updated by users
            if (isset($data['subject'])) $complaint->setSubject($data['subject']);
            if (isset($data['description'])) $complaint->setDescription($data['description']);
            if (isset($data['attachments'])) $complaint->setAttachments($data['attachments']);

            // Admin-only fields
            if ($this->isGranted('ROLE_ADMIN')) {
                if (isset($data['status'])) $complaint->setStatus($data['status']);
                if (isset($data['priority'])) $complaint->setPriority($data['priority']);
                if (isset($data['adminResponse'])) {
                    $complaint->setAdminResponse($data['adminResponse']);
                    $complaint->setAdminResponseDate(new \DateTime());
                    $complaint->setAdminUserId($user->getId());
                }
            }

            $complaint->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Complaint updated successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to update complaint: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'delete_complaint', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteComplaint(int $id): JsonResponse
    {
        try {
            $user = $this->getUser();
            $complaint = $this->complaintRepository->find($id);

            if (!$complaint) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Complaint not found'
                ], 404);
            }

            // Check if user owns this complaint or is admin
            if ($complaint->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $this->entityManager->remove($complaint);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Complaint deleted successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to delete complaint: ' . $e->getMessage()
            ], 500);
        }
    }
}
