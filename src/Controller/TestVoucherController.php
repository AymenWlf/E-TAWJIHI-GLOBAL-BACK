<?php

namespace App\Controller;

use App\Entity\TestVoucher;
use App\Repository\TestVoucherRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/test-vouchers')]
class TestVoucherController extends AbstractController
{
    private TestVoucherRepository $testVoucherRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        TestVoucherRepository $testVoucherRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->testVoucherRepository = $testVoucherRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('', name: 'get_test_vouchers', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getTestVouchers(Request $request): JsonResponse
    {
        try {
            $language = $request->query->get('language', 'en');
            $category = $request->query->get('category', 'all');

            // Get user for currency conversion
            $user = $this->getUser();
            $userCurrency = $user->getPreferences()?->getPreferredCurrency() ?? 'USD';

            // Get active test vouchers
            $testVouchers = $this->testVoucherRepository->findBy(
                ['isActive' => true],
                ['sortOrder' => 'ASC', 'id' => 'ASC']
            );

            // Filter by category if specified
            if ($category !== 'all') {
                $testVouchers = array_filter($testVouchers, function ($voucher) use ($category) {
                    return $voucher->getCategory() === $category;
                });
            }

            $formattedVouchers = [];
            foreach ($testVouchers as $voucher) {
                $formattedVouchers[] = [
                    'id' => $voucher->getId(),
                    'name' => $language === 'fr' ? $voucher->getNameFr() : $voucher->getName(),
                    'vendor' => $voucher->getVendor(),
                    'vendorLogo' => $voucher->getVendorLogo(),
                    'originalPrice' => (float) $voucher->getOriginalPrice(),
                    'discountedPrice' => (float) $voucher->getDiscountedPrice(),
                    'currency' => $voucher->getCurrency(),
                    'category' => $voucher->getCategory(),
                    'status' => $voucher->getStatus(),
                    'description' => $language === 'fr' ? $voucher->getDescriptionFr() : $voucher->getDescription(),
                    'recognition' => $language === 'fr' ? $voucher->getRecognitionFr() : $voucher->getRecognition(),
                    'features' => $language === 'fr' ? $voucher->getFeaturesFr() : $voucher->getFeatures(),
                    'validity' => $language === 'fr' ? $voucher->getValidityFr() : $voucher->getValidity(),
                    'shareLink' => $voucher->getShareLink(),
                    'buyLink' => $voucher->getBuyLink(),
                    'icon' => $voucher->getIcon(),
                    'color' => $voucher->getColor(),
                    'isActive' => $voucher->getIsActive(),
                    'sortOrder' => $voucher->getSortOrder()
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $formattedVouchers,
                'userCurrency' => $userCurrency,
                'message' => 'Test vouchers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve test vouchers: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'get_test_voucher', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getTestVoucher(int $id, Request $request): JsonResponse
    {
        try {
            $language = $request->query->get('language', 'en');

            $voucher = $this->testVoucherRepository->find($id);
            if (!$voucher || !$voucher->getIsActive()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Test voucher not found'
                ], 404);
            }

            $formattedVoucher = [
                'id' => $voucher->getId(),
                'name' => $language === 'fr' ? $voucher->getNameFr() : $voucher->getName(),
                'vendor' => $voucher->getVendor(),
                'vendorLogo' => $voucher->getVendorLogo(),
                'originalPrice' => (float) $voucher->getOriginalPrice(),
                'discountedPrice' => (float) $voucher->getDiscountedPrice(),
                'currency' => $voucher->getCurrency(),
                'category' => $voucher->getCategory(),
                'status' => $voucher->getStatus(),
                'description' => $language === 'fr' ? $voucher->getDescriptionFr() : $voucher->getDescription(),
                'recognition' => $language === 'fr' ? $voucher->getRecognitionFr() : $voucher->getRecognition(),
                'features' => $language === 'fr' ? $voucher->getFeaturesFr() : $voucher->getFeatures(),
                'validity' => $language === 'fr' ? $voucher->getValidityFr() : $voucher->getValidity(),
                'shareLink' => $voucher->getShareLink(),
                'buyLink' => $voucher->getBuyLink(),
                'icon' => $voucher->getIcon(),
                'color' => $voucher->getColor(),
                'isActive' => $voucher->getIsActive(),
                'sortOrder' => $voucher->getSortOrder()
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $formattedVoucher,
                'message' => 'Test voucher retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to retrieve test voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('', name: 'create_test_voucher', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createTestVoucher(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $voucher = new TestVoucher();
            $voucher->setName($data['name'] ?? '');
            $voucher->setNameFr($data['nameFr'] ?? '');
            $voucher->setVendor($data['vendor'] ?? '');
            $voucher->setVendorLogo($data['vendorLogo'] ?? '');
            $voucher->setOriginalPrice((float) ($data['originalPrice'] ?? 0));
            $voucher->setDiscountedPrice((float) ($data['discountedPrice'] ?? 0));
            $voucher->setCurrency($data['currency'] ?? 'USD');
            $voucher->setCategory($data['category'] ?? '');
            $voucher->setStatus($data['status'] ?? 'available');
            $voucher->setDescription($data['description'] ?? '');
            $voucher->setDescriptionFr($data['descriptionFr'] ?? '');
            $voucher->setRecognition($data['recognition'] ?? '');
            $voucher->setRecognitionFr($data['recognitionFr'] ?? '');
            $voucher->setFeatures($data['features'] ?? []);
            $voucher->setFeaturesFr($data['featuresFr'] ?? []);
            $voucher->setValidity($data['validity'] ?? '');
            $voucher->setValidityFr($data['validityFr'] ?? '');
            $voucher->setShareLink($data['shareLink'] ?? null);
            $voucher->setBuyLink($data['buyLink'] ?? null);
            $voucher->setIcon($data['icon'] ?? '');
            $voucher->setColor($data['color'] ?? '');
            $voucher->setIsActive($data['isActive'] ?? true);
            $voucher->setSortOrder($data['sortOrder'] ?? null);

            $this->entityManager->persist($voucher);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'data' => ['id' => $voucher->getId()],
                'message' => 'Test voucher created successfully'
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to create test voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'update_test_voucher', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateTestVoucher(int $id, Request $request): JsonResponse
    {
        try {
            $voucher = $this->testVoucherRepository->find($id);
            if (!$voucher) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Test voucher not found'
                ], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['name'])) $voucher->setName($data['name']);
            if (isset($data['nameFr'])) $voucher->setNameFr($data['nameFr']);
            if (isset($data['vendor'])) $voucher->setVendor($data['vendor']);
            if (isset($data['vendorLogo'])) $voucher->setVendorLogo($data['vendorLogo']);
            if (isset($data['originalPrice'])) $voucher->setOriginalPrice((float) $data['originalPrice']);
            if (isset($data['discountedPrice'])) $voucher->setDiscountedPrice((float) $data['discountedPrice']);
            if (isset($data['currency'])) $voucher->setCurrency($data['currency']);
            if (isset($data['category'])) $voucher->setCategory($data['category']);
            if (isset($data['status'])) $voucher->setStatus($data['status']);
            if (isset($data['description'])) $voucher->setDescription($data['description']);
            if (isset($data['descriptionFr'])) $voucher->setDescriptionFr($data['descriptionFr']);
            if (isset($data['recognition'])) $voucher->setRecognition($data['recognition']);
            if (isset($data['recognitionFr'])) $voucher->setRecognitionFr($data['recognitionFr']);
            if (isset($data['features'])) $voucher->setFeatures($data['features']);
            if (isset($data['featuresFr'])) $voucher->setFeaturesFr($data['featuresFr']);
            if (isset($data['validity'])) $voucher->setValidity($data['validity']);
            if (isset($data['validityFr'])) $voucher->setValidityFr($data['validityFr']);
            if (isset($data['shareLink'])) $voucher->setShareLink($data['shareLink']);
            if (isset($data['buyLink'])) $voucher->setBuyLink($data['buyLink']);
            if (isset($data['icon'])) $voucher->setIcon($data['icon']);
            if (isset($data['color'])) $voucher->setColor($data['color']);
            if (isset($data['isActive'])) $voucher->setIsActive($data['isActive']);
            if (isset($data['sortOrder'])) $voucher->setSortOrder($data['sortOrder']);

            $voucher->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Test voucher updated successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to update test voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'delete_test_voucher', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteTestVoucher(int $id): JsonResponse
    {
        try {
            $voucher = $this->testVoucherRepository->find($id);
            if (!$voucher) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Test voucher not found'
                ], 404);
            }

            $this->entityManager->remove($voucher);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Test voucher deleted successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to delete test voucher: ' . $e->getMessage()
            ], 500);
        }
    }
}
