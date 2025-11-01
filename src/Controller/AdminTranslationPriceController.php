<?php

namespace App\Controller;

use App\Entity\TranslationPrice;
use App\Repository\TranslationPriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/translation-prices', name: 'api_admin_translation_prices_')]
#[IsGranted('ROLE_ADMIN')]
class AdminTranslationPriceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslationPriceRepository $translationPriceRepository
    ) {}

    /**
     * Get all translation prices
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $prices = $this->translationPriceRepository->findAllOrdered();
            
            $data = array_map(function (TranslationPrice $price) {
                return $this->serializeTranslationPrice($price);
            }, $prices);

            return new JsonResponse([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // Log the full error for debugging
            error_log('TranslationPrice list error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a single translation price by ID
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $price = $this->translationPriceRepository->find($id);

            if (!$price) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Prix non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->serializeTranslationPrice($price)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération du prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new translation price
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation
            if (empty($data['fromLanguage']) || empty($data['toLanguage']) || !isset($data['price'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Les champs fromLanguage, toLanguage et price sont requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($data['fromLanguage'] === $data['toLanguage']) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'La langue source et la langue cible doivent être différentes'
                ], Response::HTTP_BAD_REQUEST);
            }

            $priceValue = (float) $data['price'];
            if ($priceValue <= 0) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le prix doit être un nombre positif'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check if price already exists for this language pair
            $existingPrice = $this->translationPriceRepository->findByLanguages(
                $data['fromLanguage'],
                $data['toLanguage']
            );

            if ($existingPrice) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Un prix existe déjà pour cette combinaison de langues'
                ], Response::HTTP_CONFLICT);
            }

            // Create new price
            $translationPrice = new TranslationPrice();
            $translationPrice->setFromLanguage($data['fromLanguage']);
            $translationPrice->setToLanguage($data['toLanguage']);
            $translationPrice->setPrice($priceValue);
            $translationPrice->setCurrency($data['currency'] ?? 'MAD');

            $this->entityManager->persist($translationPrice);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Prix créé avec succès',
                'data' => $this->serializeTranslationPrice($translationPrice)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la création du prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a translation price
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $price = $this->translationPriceRepository->find($id);

            if (!$price) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Prix non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            // Validation
            if (isset($data['fromLanguage']) && isset($data['toLanguage'])) {
                if ($data['fromLanguage'] === $data['toLanguage']) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'La langue source et la langue cible doivent être différentes'
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Check if another price exists for this language pair (excluding current)
                $existingPrice = $this->translationPriceRepository->findByLanguages(
                    $data['fromLanguage'],
                    $data['toLanguage']
                );

                if ($existingPrice && $existingPrice->getId() !== $id) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Un prix existe déjà pour cette combinaison de langues'
                    ], Response::HTTP_CONFLICT);
                }
            }

            if (isset($data['fromLanguage'])) {
                $price->setFromLanguage($data['fromLanguage']);
            }

            if (isset($data['toLanguage'])) {
                $price->setToLanguage($data['toLanguage']);
            }

            if (isset($data['price'])) {
                $priceValue = (float) $data['price'];
                if ($priceValue <= 0) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Le prix doit être un nombre positif'
                    ], Response::HTTP_BAD_REQUEST);
                }
                $price->setPrice($priceValue);
            }

            if (isset($data['currency'])) {
                $price->setCurrency($data['currency']);
            }

            $price->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Prix mis à jour avec succès',
                'data' => $this->serializeTranslationPrice($price)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a translation price
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $price = $this->translationPriceRepository->find($id);

            if (!$price) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Prix non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->remove($price);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Prix supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la suppression du prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Serialize TranslationPrice to array
     */
    private function serializeTranslationPrice(TranslationPrice $price): array
    {
        return [
            'id' => $price->getId(),
            'fromLanguage' => $price->getFromLanguage(),
            'toLanguage' => $price->getToLanguage(),
            'price' => $price->getPrice(),
            'currency' => $price->getCurrency(),
            'createdAt' => $price->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $price->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }
}

