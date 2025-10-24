<?php

namespace App\Controller;

use App\Entity\Suggestion;
use App\Repository\SuggestionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/suggestions')]
class SuggestionController extends AbstractController
{
    private SuggestionRepository $suggestionRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        SuggestionRepository $suggestionRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->suggestionRepository = $suggestionRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('', name: 'get_suggestions', methods: ['GET'])]
    public function getSuggestions(Request $request): JsonResponse
    {
        try {
            $category = $request->query->get('category', 'all');
            $status = $request->query->get('status', 'all');
            $search = $request->query->get('search', '');
            $type = $request->query->get('type', 'public'); // public, my, all

            $suggestions = [];

            if ($type === 'my' && $this->getUser()) {
                $suggestions = $this->suggestionRepository->findByUser($this->getUser());
            } elseif ($type === 'all' && $this->isGranted('ROLE_ADMIN')) {
                $suggestions = $this->suggestionRepository->findAll();
            } else {
                // Public suggestions
                if (!empty($search)) {
                    $suggestions = $this->suggestionRepository->search($search);
                } elseif ($category !== 'all') {
                    $suggestions = $this->suggestionRepository->findByCategory($category);
                } else {
                    $suggestions = $this->suggestionRepository->findPublic();
                }
            }

            // Filter by status if provided
            if ($status !== 'all') {
                $suggestions = array_filter($suggestions, function ($suggestion) use ($status) {
                    return $suggestion->getStatus() === $status;
                });
            }

            $formattedSuggestions = [];
            foreach ($suggestions as $suggestion) {
                $formattedSuggestions[] = [
                    'id' => $suggestion->getId(),
                    'category' => $suggestion->getCategory(),
                    'title' => $suggestion->getTitle(),
                    'description' => $suggestion->getDescription(),
                    'priority' => $suggestion->getPriority(),
                    'status' => $suggestion->getStatus(),
                    'attachments' => $suggestion->getAttachments(),
                    'adminResponse' => $suggestion->getAdminResponse(),
                    'adminResponseDate' => $suggestion->getAdminResponseDate()?->format('Y-m-d H:i:s'),
                    'votes' => $suggestion->getVotes(),
                    'isPublic' => $suggestion->getIsPublic(),
                    'isAnonymous' => $suggestion->getIsAnonymous(),
                    'userName' => $suggestion->getIsAnonymous() ? 'Anonymous' : ($suggestion->getUser() ? $suggestion->getUser()->getFirstName() . ' ' . $suggestion->getUser()->getLastName() : 'Unknown'),
                    'createdAt' => $suggestion->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $suggestion->getUpdatedAt()->format('Y-m-d H:i:s')
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $formattedSuggestions,
                'message' => 'Suggestions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'get_suggestion', methods: ['GET'])]
    public function getSuggestion(int $id): JsonResponse
    {
        try {
            $suggestion = $this->suggestionRepository->find($id);

            if (!$suggestion) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Suggestion not found'
                ], 404);
            }

            // Check if user can view this suggestion
            if (!$suggestion->getIsPublic() && $suggestion->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $formattedSuggestion = [
                'id' => $suggestion->getId(),
                'category' => $suggestion->getCategory(),
                'title' => $suggestion->getTitle(),
                'description' => $suggestion->getDescription(),
                'priority' => $suggestion->getPriority(),
                'status' => $suggestion->getStatus(),
                'attachments' => $suggestion->getAttachments(),
                'adminResponse' => $suggestion->getAdminResponse(),
                'adminResponseDate' => $suggestion->getAdminResponseDate()?->format('Y-m-d H:i:s'),
                'votes' => $suggestion->getVotes(),
                'isPublic' => $suggestion->getIsPublic(),
                'isAnonymous' => $suggestion->getIsAnonymous(),
                'userName' => $suggestion->getIsAnonymous() ? 'Anonymous' : ($suggestion->getUser() ? $suggestion->getUser()->getFirstName() . ' ' . $suggestion->getUser()->getLastName() : 'Unknown'),
                'createdAt' => $suggestion->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $suggestion->getUpdatedAt()->format('Y-m-d H:i:s')
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $formattedSuggestion,
                'message' => 'Suggestion retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve suggestion: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('', name: 'create_suggestion', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createSuggestion(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            $suggestion = new Suggestion();
            $suggestion->setUser($user);
            $suggestion->setCategory($data['category'] ?? '');
            $suggestion->setTitle($data['title'] ?? '');
            $suggestion->setDescription($data['description'] ?? '');
            $suggestion->setPriority($data['priority'] ?? 'medium');
            $suggestion->setStatus('pending');
            $suggestion->setAttachments($data['attachments'] ?? null);
            $suggestion->setIsPublic($data['isPublic'] ?? false);
            $suggestion->setIsAnonymous($data['isAnonymous'] ?? false);

            $this->entityManager->persist($suggestion);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'data' => ['id' => $suggestion->getId()],
                'message' => 'Suggestion created successfully'
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to create suggestion: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'update_suggestion', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateSuggestion(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            $suggestion = $this->suggestionRepository->find($id);
            if (!$suggestion) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Suggestion not found'
                ], 404);
            }

            // Check if user owns this suggestion or is admin
            if ($suggestion->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Only allow certain fields to be updated by users
            if (isset($data['title'])) $suggestion->setTitle($data['title']);
            if (isset($data['description'])) $suggestion->setDescription($data['description']);
            if (isset($data['attachments'])) $suggestion->setAttachments($data['attachments']);
            if (isset($data['isPublic'])) $suggestion->setIsPublic($data['isPublic']);
            if (isset($data['isAnonymous'])) $suggestion->setIsAnonymous($data['isAnonymous']);

            // Admin-only fields
            if ($this->isGranted('ROLE_ADMIN')) {
                if (isset($data['status'])) $suggestion->setStatus($data['status']);
                if (isset($data['priority'])) $suggestion->setPriority($data['priority']);
                if (isset($data['adminResponse'])) {
                    $suggestion->setAdminResponse($data['adminResponse']);
                    $suggestion->setAdminResponseDate(new \DateTime());
                    $suggestion->setAdminUserId($user->getId());
                }
                if (isset($data['votes'])) $suggestion->setVotes($data['votes']);
            }

            $suggestion->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Suggestion updated successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to update suggestion: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}/vote', name: 'vote_suggestion', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function voteSuggestion(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $vote = $data['vote'] ?? 1; // 1 for upvote, -1 for downvote

            $suggestion = $this->suggestionRepository->find($id);
            if (!$suggestion) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Suggestion not found'
                ], 404);
            }

            if (!$suggestion->getIsPublic()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Cannot vote on private suggestions'
                ], 403);
            }

            // Update votes
            $currentVotes = $suggestion->getVotes();
            $suggestion->setVotes($currentVotes + $vote);
            $suggestion->setUpdatedAt(new \DateTime());

            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'data' => ['votes' => $suggestion->getVotes()],
                'message' => 'Vote recorded successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to vote on suggestion: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'delete_suggestion', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteSuggestion(int $id): JsonResponse
    {
        try {
            $user = $this->getUser();
            $suggestion = $this->suggestionRepository->find($id);

            if (!$suggestion) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Suggestion not found'
                ], 404);
            }

            // Check if user owns this suggestion or is admin
            if ($suggestion->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $this->entityManager->remove($suggestion);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Suggestion deleted successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to delete suggestion: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/categories', name: 'get_suggestion_categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        try {
            $categories = $this->suggestionRepository->getCategories();
            $categoryList = array_column($categories, 'category');

            return new JsonResponse([
                'success' => true,
                'data' => $categoryList,
                'message' => 'Suggestion categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve suggestion categories: ' . $e->getMessage()
            ], 500);
        }
    }
}
