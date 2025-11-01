<?php

namespace App\Controller;

use App\Entity\Translation;
use App\Entity\User;
use App\Repository\TranslationRepository;
use App\Repository\TranslationPriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/translations', name: 'app_translations_')]
#[IsGranted('ROLE_USER')]
class TranslationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslationRepository $translationRepository,
        private TranslationPriceRepository $translationPriceRepository,
        private SluggerInterface $slugger
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getTranslations(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $translations = $this->translationRepository->findByUser($user);
        $data = array_map([$this, 'serializeTranslation'], $translations);

        return new JsonResponse([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get translation price by languages (public endpoint - no auth required for price lookup)
     * This route must be defined BEFORE /{id} routes to avoid "price" being interpreted as an ID
     */
    #[Route('/price', name: 'get_price', methods: ['GET'], priority: 2)]
    public function getTranslationPrice(Request $request): JsonResponse
    {
        $fromLanguage = trim($request->query->get('fromLanguage', ''));
        $toLanguage = trim($request->query->get('toLanguage', ''));

        if (empty($fromLanguage) || empty($toLanguage)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'fromLanguage and toLanguage parameters are required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Find exact match (case-insensitive)
            $translationPrice = $this->translationPriceRepository->findByLanguages($fromLanguage, $toLanguage);
            
            if (!$translationPrice) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Price not found for this language pair',
                    'data' => null
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'fromLanguage' => $translationPrice->getFromLanguage(),
                    'toLanguage' => $translationPrice->getToLanguage(),
                    'price' => $translationPrice->getPrice(),
                    'currency' => $translationPrice->getCurrency()
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error retrieving translation price',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createTranslation(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Get data from form data
        $dataString = $request->request->get('data');
        if (!$dataString) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Data field is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($dataString, true);
        if (!$data) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid JSON data'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate required fields
        $requiredFields = ['originalFilename', 'originalLanguage', 'targetLanguage', 'documentType', 'numberOfPages', 'pricePerPage', 'totalPrice'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // Handle file upload
        $uploadedFile = $request->files->get('originalDocument');
        if (!$uploadedFile) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Original document file is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate file
        if (!$uploadedFile->isValid()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid file upload'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Create upload directory
        $uploadDir = $this->getProjectDir() . '/public/uploads/translations';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $originalFilename = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->guessExtension() ?: $uploadedFile->getClientOriginalExtension();
        $safeFilename = $this->slugger->slug(pathinfo($originalFilename, PATHINFO_FILENAME));
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . '/' . $newFilename;

        // Move file
        try {
            $uploadedFile->move($uploadDir, $newFilename);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to save file: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Create translation entity
        $translation = new Translation();
        $translation->setUser($user);
        $translation->setOriginalFilename($originalFilename);
        $translation->setOriginalFilePath($newFilename);
        $translation->setOriginalLanguage($data['originalLanguage']);
        $translation->setTargetLanguage($data['targetLanguage']);
        $translation->setDocumentType($data['documentType']);
        $translation->setNumberOfPages($data['numberOfPages']);
        $translation->setPricePerPage($data['pricePerPage']);
        $translation->setTotalPrice($data['totalPrice']);
        $translation->setCurrency($data['currency'] ?? 'MAD');
        $translation->setNotes($data['notes'] ?? null);

        $this->entityManager->persist($translation);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Translation request created successfully',
            'data' => $this->serializeTranslation($translation)
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getTranslation(string $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $translation = $this->translationRepository->find((int) $id);
        if (!$translation || $translation->getUser() !== $user) {
            return new JsonResponse(['error' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'success' => true,
            'data' => $this->serializeTranslation($translation)
        ]);
    }

    #[Route('/{id}/original', name: 'download_original', methods: ['GET'])]
    public function downloadOriginal(string $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $translation = $this->translationRepository->find((int) $id);
        if (!$translation || $translation->getUser() !== $user) {
            return new JsonResponse(['error' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        $filePath = $this->getProjectDir() . '/public/uploads/translations/' . $translation->getOriginalFilePath();
        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $translation->getOriginalFilename()
        );

        // Set proper content type based on file extension
        $extension = pathinfo($translation->getOriginalFilename(), PATHINFO_EXTENSION);
        $mimeType = match (strtolower($extension)) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'application/octet-stream'
        };
        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    #[Route('/{id}/translated', name: 'download_translated', methods: ['GET'])]
    public function downloadTranslated(string $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $translation = $this->translationRepository->find((int) $id);
        if (!$translation || $translation->getUser() !== $user) {
            return new JsonResponse(['error' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$translation->getTranslatedFilePath()) {
            return new JsonResponse(['error' => 'Translated file not available'], Response::HTTP_NOT_FOUND);
        }

        $filePath = $this->getProjectDir() . '/public/uploads/translations/' . $translation->getTranslatedFilePath();
        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'Translated file not found'], Response::HTTP_NOT_FOUND);
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $translation->getTranslatedFilename() ?: 'translated_' . $translation->getOriginalFilename()
        );

        // Set proper content type based on file extension
        $filename = $translation->getTranslatedFilename() ?: 'translated_' . $translation->getOriginalFilename();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $mimeType = match (strtolower($extension)) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'application/octet-stream'
        };
        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    #[Route('/pending-payment', name: 'pending_payment', methods: ['GET'], priority: 1)]
    public function getPendingPayment(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $pendingTranslations = $this->translationRepository->findPendingForPayment($user);
        $totalAmount = $this->translationRepository->getTotalPendingAmount($user);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'translations' => array_map([$this, 'serializeTranslation'], $pendingTranslations),
                'totalAmount' => $totalAmount,
                'count' => count($pendingTranslations)
            ]
        ]);
    }

    #[Route('/pending-payment', name: 'pay_pending', methods: ['POST'], priority: 1)]
    public function payPendingTranslations(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $pendingTranslations = $this->translationRepository->findPendingForPayment($user);

        if (empty($pendingTranslations)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No pending translations to pay'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Here you would integrate with your payment system (Stripe, PayPal, etc.)
        // For now, we'll just mark them as paid
        foreach ($pendingTranslations as $translation) {
            $translation->setPaymentStatus('paid');
            $translation->setUpdatedAt(new \DateTimeImmutable());
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Payment processed successfully',
            'data' => [
                'paidCount' => count($pendingTranslations),
                'totalAmount' => $this->translationRepository->getTotalPendingAmount($user)
            ]
        ]);
    }

    private function serializeTranslation(Translation $translation): array
    {
        return [
            'id' => $translation->getId(),
            'originalFilename' => $translation->getOriginalFilename(),
            'originalLanguage' => $translation->getOriginalLanguage(),
            'targetLanguage' => $translation->getTargetLanguage(),
            'documentType' => $translation->getDocumentType(),
            'numberOfPages' => $translation->getNumberOfPages(),
            'pricePerPage' => $translation->getPricePerPage(),
            'totalPrice' => $translation->getTotalPrice(),
            'currency' => $translation->getCurrency(),
            'status' => $translation->getStatus(),
            'paymentStatus' => $translation->getPaymentStatus(),
            'notes' => $translation->getNotes(),
            'translatedFilename' => $translation->getTranslatedFilename(),
            'deliveryDate' => $translation->getDeliveryDate()?->format('Y-m-d H:i:s'),
            'createdAt' => $translation->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $translation->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteTranslation(string $id): JsonResponse
    {
        $translation = $this->translationRepository->find((int) $id);

        if (!$translation) {
            return new JsonResponse(['success' => false, 'message' => 'Translation not found'], 404);
        }

        // Check if user owns this translation
        if ($translation->getUser() !== $this->getUser()) {
            return new JsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }

        // Check if translation is already paid
        if ($translation->getPaymentStatus() === 'paid') {
            return new JsonResponse(['success' => false, 'message' => 'Cannot delete a paid translation'], 400);
        }

        try {
            // Delete the original file if it exists
            if ($translation->getOriginalFilePath()) {
                $filePath = $this->getProjectDir() . '/public' . $translation->getOriginalFilePath();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete the translated file if it exists
            if ($translation->getTranslatedFilePath()) {
                $filePath = $this->getProjectDir() . '/public' . $translation->getTranslatedFilePath();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Remove from database
            $this->entityManager->remove($translation);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Translation deleted successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to delete translation'], 500);
        }
    }

    private function getProjectDir(): string
    {
        return dirname(__DIR__, 2);
    }
}
