<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/services')]
#[IsGranted('ROLE_USER')]
class ServiceController extends AbstractController
{
    private ServiceRepository $serviceRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ServiceRepository $serviceRepository, EntityManagerInterface $entityManager)
    {
        $this->serviceRepository = $serviceRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'get_services', methods: ['GET'])]
    public function getServices(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userCountry = $user->getCountry();
            $userStudyCountry = $user->getPreferredCountry();
            // Use currency parameter if provided, otherwise use user's preference
            $requestedCurrency = $request->query->get('currency');
            $userCurrency = $requestedCurrency ?: ($user->getPreferredCurrency() ?? 'USD');
            $requestedLanguage = $request->query->get('language', 'en');
            // Normalize language to 'fr' or 'en'
            $language = (strtolower($requestedLanguage) === 'fr' || strtolower($requestedLanguage) === 'français') ? 'fr' : 'en';

            // Get all active services
            $services = $this->serviceRepository->findBy(['isActive' => true]);

            $filteredServices = [];
            foreach ($services as $service) {
                // Check if service is available for user's country
                $isAvailable = $this->isServiceAvailableForUser($service, $userCountry, $userStudyCountry);

                // Convert price to user's preferred currency
                $convertedPrice = $this->convertPrice($service->getPrice(), $service->getCurrency(), $userCurrency);
                
                // Convert promotional price if it exists
                $convertedPromotionalPrice = null;
                if ($service->getPromotionalPrice()) {
                    $convertedPromotionalPrice = $this->convertPrice($service->getPromotionalPrice(), $service->getCurrency(), $userCurrency);
                }

                // Calculate discount amount and percentage
                $discountAmount = null;
                $discountPercentage = null;
                if ($convertedPromotionalPrice && $convertedPromotionalPrice < $convertedPrice) {
                    $discountAmount = $convertedPrice - $convertedPromotionalPrice;
                    $discountPercentage = round(($discountAmount / $convertedPrice) * 100, 0);
                }

                $serviceData = [
                    'id' => $service->getId(),
                    'name' => $language === 'fr' ? $service->getNameFr() : $service->getName(),
                    'description' => $language === 'fr' ? $service->getDescriptionFr() : $service->getDescription(),
                    'price' => $convertedPrice,
                    'originalPrice' => $service->getPrice(),
                    'promotionalPrice' => $convertedPromotionalPrice,
                    'discountAmount' => $discountAmount,
                    'discountPercentage' => $discountPercentage,
                    'currency' => $userCurrency,
                    'originalCurrency' => $service->getCurrency(),
                    'category' => $service->getCategory(),
                    'targetCountries' => $service->getTargetCountries(),
                    'features' => $language === 'fr' ? $service->getFeaturesFr() : $service->getFeatures(),
                    'icon' => $service->getIcon(),
                    'color' => $service->getColor(),
                    'duration' => $service->getDuration(),
                    'durationUnit' => $service->getDurationUnit(),
                    'images' => $service->getImages() ?? [],
                    'isAvailable' => $isAvailable,
                    'availabilityMessage' => $this->getAvailabilityMessage($service, $userCountry, $userStudyCountry, $language)
                ];

                $filteredServices[] = $serviceData;
            }

            return new JsonResponse([
                'success' => true,
                'data' => $filteredServices,
                'userCountry' => $userCountry,
                'userStudyCountry' => $userStudyCountry,
                'userCurrency' => $userCurrency
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error fetching services: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'get_service', methods: ['GET'])]
    public function getService(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            // Use currency parameter if provided, otherwise use user's preference
            $requestedCurrency = $request->query->get('currency');
            $userCurrency = $requestedCurrency ?: ($user->getPreferredCurrency() ?? 'USD');
            $requestedLanguage = $request->query->get('language', 'en');
            // Normalize language to 'fr' or 'en'
            $language = (strtolower($requestedLanguage) === 'fr' || strtolower($requestedLanguage) === 'français') ? 'fr' : 'en';

            $service = $this->serviceRepository->find($id);
            if (!$service) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $convertedPrice = $this->convertPrice($service->getPrice(), $service->getCurrency(), $userCurrency);
            
            // Convert promotional price if it exists
            $convertedPromotionalPrice = null;
            if ($service->getPromotionalPrice()) {
                $convertedPromotionalPrice = $this->convertPrice($service->getPromotionalPrice(), $service->getCurrency(), $userCurrency);
            }

            // Calculate discount amount and percentage
            $discountAmount = null;
            $discountPercentage = null;
            if ($convertedPromotionalPrice && $convertedPromotionalPrice < $convertedPrice) {
                $discountAmount = $convertedPrice - $convertedPromotionalPrice;
                $discountPercentage = round(($discountAmount / $convertedPrice) * 100, 0);
            }

            $isAvailable = $this->isServiceAvailableForUser($service, $user->getCountry(), $user->getPreferredCountry());

            $serviceData = [
                'id' => $service->getId(),
                'name' => $language === 'fr' ? $service->getNameFr() : $service->getName(),
                'description' => $language === 'fr' ? $service->getDescriptionFr() : $service->getDescription(),
                'price' => $convertedPrice,
                'originalPrice' => $service->getPrice(),
                'promotionalPrice' => $convertedPromotionalPrice,
                'discountAmount' => $discountAmount,
                'discountPercentage' => $discountPercentage,
                'currency' => $userCurrency,
                'originalCurrency' => $service->getCurrency(),
                'category' => $service->getCategory(),
                'targetCountries' => $service->getTargetCountries(),
                'features' => $language === 'fr' ? $service->getFeaturesFr() : $service->getFeatures(),
                'icon' => $service->getIcon(),
                'color' => $service->getColor(),
                'duration' => $service->getDuration(),
                'durationUnit' => $service->getDurationUnit(),
                'images' => $service->getImages() ?? [],
                'isAvailable' => $isAvailable,
                'availabilityMessage' => $this->getAvailabilityMessage($service, $user->getCountry(), $user->getPreferredCountry(), $language)
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $serviceData
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error fetching service: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isServiceAvailableForUser(Service $service, ?string $userCountry, ?string $userStudyCountry): bool
    {
        $targetCountries = $service->getTargetCountries();

        // If no target countries specified, service is available to all
        if (empty($targetCountries)) {
            return true;
        }

        // If user hasn't set country, service is not available
        if (!$userCountry && !$userStudyCountry) {
            return false;
        }

        // Check if user's country or study country is in target countries
        return in_array($userCountry, $targetCountries) || in_array($userStudyCountry, $targetCountries);
    }

    private function getAvailabilityMessage(Service $service, ?string $userCountry, ?string $userStudyCountry, string $language): string
    {
        if (!$userCountry && !$userStudyCountry) {
            return $language === 'fr'
                ? 'Veuillez renseigner votre pays pour voir les services compatibles'
                : 'Please set your country to see compatible services';
        }

        $isAvailable = $this->isServiceAvailableForUser($service, $userCountry, $userStudyCountry);

        if ($isAvailable) {
            return $language === 'fr'
                ? 'Service disponible pour votre pays'
                : 'Service available for your country';
        } else {
            return $language === 'fr'
                ? 'Service disponible pour d\'autres pays'
                : 'Service available for other countries';
        }
    }

    private function convertPrice(float $price, string $fromCurrency, string $toCurrency): float
    {
        // If same currency, return original price
        if ($fromCurrency === $toCurrency) {
            return $price;
        }

        // Exchange rates (in real app, you'd use an API like fixer.io or exchangerate-api.com)
        $exchangeRates = [
            'USD' => 1.0,
            'EUR' => 0.85,
            'MAD' => 10.0,
            'CAD' => 1.35,
            'GBP' => 0.73,
            'AUD' => 1.45,
            'CHF' => 0.92,
            'JPY' => 110.0,
            'CNY' => 6.45,
            'AED' => 3.67,
            'SAR' => 3.75,
            'QAR' => 3.64,
            'KWD' => 0.30,
            'BHD' => 0.38,
            'OMR' => 0.38,
            'JOD' => 0.71,
            'LBP' => 1500.0,
            'EGP' => 15.7,
            'TND' => 2.85,
            'DZD' => 135.0,
            'XOF' => 550.0,
            'XAF' => 550.0,
            'NGN' => 410.0,
            'ZAR' => 14.5,
            'KES' => 110.0,
            'GHS' => 5.8,
            'ETB' => 44.0,
            'UGX' => 3500.0,
            'TZS' => 2300.0,
            'RWF' => 1000.0,
            'MWK' => 800.0,
            'ZMW' => 18.0,
            'BWP' => 11.0,
            'SZL' => 14.5,
            'LSL' => 14.5,
            'NAD' => 14.5,
            'MZN' => 64.0,
            'AOA' => 650.0,
            'CDF' => 2000.0,
            'BIF' => 2000.0,
            'KMF' => 450.0,
            'DJF' => 180.0,
            'ERN' => 15.0,
            'SOS' => 580.0,
            'SSP' => 130.0,
            'SDG' => 55.0,
            'LYD' => 4.5,
        ];

        // Convert to USD first, then to target currency
        $usdPrice = $price / ($exchangeRates[$fromCurrency] ?? 1.0);
        $convertedPrice = $usdPrice * ($exchangeRates[$toCurrency] ?? 1.0);

        return round($convertedPrice, 2);
    }
}
