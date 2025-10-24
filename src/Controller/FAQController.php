<?php

namespace App\Controller;

use App\Entity\FAQ;
use App\Repository\FAQRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/faqs')]
class FAQController extends AbstractController
{
    private FAQRepository $faqRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        FAQRepository $faqRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->faqRepository = $faqRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'get_faqs', methods: ['GET'])]
    public function getFAQs(Request $request): JsonResponse
    {
        try {
            $language = $request->query->get('language', 'en');
            $category = $request->query->get('category', 'all');
            $search = $request->query->get('search', '');
            $popular = $request->query->get('popular', false);

            $faqs = [];

            if ($popular === 'true') {
                $faqs = $this->faqRepository->findPopular();
            } elseif (!empty($search)) {
                $faqs = $this->faqRepository->search($search);
            } elseif ($category !== 'all') {
                $faqs = $this->faqRepository->findByCategory($category);
            } else {
                $faqs = $this->faqRepository->findActiveOrdered();
            }

            $formattedFAQs = [];
            foreach ($faqs as $faq) {
                $formattedFAQs[] = [
                    'id' => $faq->getId(),
                    'category' => $faq->getCategory(),
                    'question' => $language === 'fr' ? $faq->getQuestionFr() : $faq->getQuestion(),
                    'answer' => $language === 'fr' ? $faq->getAnswerFr() : $faq->getAnswer(),
                    'sortOrder' => $faq->getSortOrder(),
                    'isActive' => $faq->getIsActive(),
                    'isPopular' => $faq->getIsPopular(),
                    'icon' => $faq->getIcon(),
                    'color' => $faq->getColor(),
                    'createdAt' => $faq->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $faq->getUpdatedAt()->format('Y-m-d H:i:s')
                ];
            }

            // Group by category if no specific category is requested
            if ($category === 'all' && empty($search) && $popular !== 'true') {
                $groupedFAQs = [];
                foreach ($formattedFAQs as $faq) {
                    $groupedFAQs[$faq['category']][] = $faq;
                }
                $formattedFAQs = $groupedFAQs;
            }

            return new JsonResponse([
                'success' => true,
                'data' => $formattedFAQs,
                'message' => 'FAQs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve FAQs: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/categories', name: 'get_faq_categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        try {
            $categories = $this->faqRepository->getCategories();
            $categoryList = array_column($categories, 'category');

            return new JsonResponse([
                'success' => true,
                'data' => $categoryList,
                'message' => 'FAQ categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve FAQ categories: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'get_faq', methods: ['GET'])]
    public function getFAQ(int $id, Request $request): JsonResponse
    {
        try {
            $language = $request->query->get('language', 'en');
            $faq = $this->faqRepository->find($id);

            if (!$faq) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            $formattedFAQ = [
                'id' => $faq->getId(),
                'category' => $faq->getCategory(),
                'question' => $language === 'fr' ? $faq->getQuestionFr() : $faq->getQuestion(),
                'answer' => $language === 'fr' ? $faq->getAnswerFr() : $faq->getAnswer(),
                'sortOrder' => $faq->getSortOrder(),
                'isActive' => $faq->getIsActive(),
                'isPopular' => $faq->getIsPopular(),
                'icon' => $faq->getIcon(),
                'color' => $faq->getColor(),
                'createdAt' => $faq->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $faq->getUpdatedAt()->format('Y-m-d H:i:s')
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $formattedFAQ,
                'message' => 'FAQ retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    // Admin routes for POST, PUT, DELETE (omitted for brevity)
    // These would be protected with ROLE_ADMIN
}
