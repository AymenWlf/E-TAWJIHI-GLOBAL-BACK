<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Repository\EstablishmentRepository;
use App\Repository\ProgramRepository;
use App\Repository\ShortlistRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/api/establishments')]
class EstablishmentDetailController extends AbstractController
{
    public function __construct(
        private EstablishmentRepository $establishmentRepository,
        private ProgramRepository $programRepository,
        private ShortlistRepository $shortlistRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('/{slug}', name: 'establishment_detail', methods: ['GET'])]
    public function getEstablishmentDetail(string $slug, Request $request): JsonResponse
    {
        $establishment = $this->establishmentRepository->findOneBy(['slug' => $slug]);

        if (!$establishment || !$establishment->isActive()) {
            throw new NotFoundHttpException('Establishment not found');
        }

        // Get programs for this establishment
        $programs = $this->programRepository->findBy([
            'establishment' => $establishment,
            'isActive' => true
        ]);

        // Get language parameter for localized content
        $language = $request->query->get('lang', 'en');

        // Check if establishment is in user's shortlist
        $isShortlisted = false;
        $user = $this->getUserFromToken($request);

        if ($user) {
            $existingShortlist = $this->shortlistRepository->findByUserAndEstablishment($user, $establishment);
            $isShortlisted = $existingShortlist !== null;
        }

        // Get localized description and mission
        $description = $language === 'fr' && $establishment->getDescriptionFr()
            ? $establishment->getDescriptionFr()
            : $establishment->getDescription();

        $mission = $language === 'fr' && $establishment->getMissionFr()
            ? $establishment->getMissionFr()
            : $establishment->getMission();

        // Get admission requirements (always return both EN and FR)
        $admissionRequirements = $establishment->getAdmissionRequirements();

        $data = [
            'id' => $establishment->getId(),
            'name' => $establishment->getName(),
            'slug' => $establishment->getSlug(),
            'country' => $establishment->getCountry(),
            'city' => $establishment->getCity(),
            'type' => $establishment->getType(),
            'rating' => $establishment->getRating(),
            'students' => $establishment->getStudents(),
            'programs' => $establishment->getPrograms(),
            'logo' => $establishment->getLogo(),
            'description' => $description,
            'mission' => $mission,
            'isShortlisted' => $isShortlisted,
            'foundedYear' => $establishment->getFoundedYear(),
            'featured' => $establishment->isFeatured(),
            'sponsored' => $establishment->isSponsored(),
            'tuition' => $establishment->getTuition(),
            'tuitionMin' => $establishment->getTuitionMin(),
            'tuitionMax' => $establishment->getTuitionMax(),
            'tuitionCurrency' => $establishment->getTuitionCurrency(),
            'acceptanceRate' => $establishment->getAcceptanceRate(),
            'worldRanking' => $establishment->getWorldRanking(),
            'qsRanking' => $establishment->getQsRanking(),
            'timesRanking' => $establishment->getTimesRanking(),
            'arwuRanking' => $establishment->getArwuRanking(),
            'usNewsRanking' => $establishment->getUsNewsRanking(),
            'popularPrograms' => $establishment->getPopularPrograms(),
            'applicationDeadline' => $establishment->getApplicationDeadline()?->format('Y-m-d'),
            'scholarships' => $establishment->isScholarships(),
            'scholarshipTypes' => $establishment->getScholarshipTypes(),
            'scholarshipDescription' => $establishment->getScholarshipDescription(),
            'descriptionFr' => $establishment->getDescriptionFr(),
            'descriptionEn' => $establishment->getDescription(),
            'missionFr' => $establishment->getMissionFr(),
            'housing' => $establishment->isHousing(),
            'language' => $establishment->getLanguage(),
            'aidvisorRecommended' => $establishment->isAidvisorRecommended(),
            'easyApply' => $establishment->isEasyApply(),
            'universityType' => $establishment->getUniversityType(),
            'commissionRate' => $establishment->getCommissionRate(),
            'freeApplications' => $establishment->getFreeApplications(),
            'visaSupport' => $establishment->getVisaSupport(),
            'countrySpecific' => $establishment->getCountrySpecific(),
            'website' => $establishment->getWebsite(),
            'email' => $establishment->getEmail(),
            'phone' => $establishment->getPhone(),
            'address' => $establishment->getAddress(),
            'accreditations' => $establishment->getAccreditations(),
            'accommodation' => $establishment->isAccommodation(),
            'careerServices' => $establishment->isCareerServices(),
            'languageSupport' => $establishment->isLanguageSupport(),
            'admissionRequirements' => $admissionRequirements,
            'admissionRequirementsFr' => $establishment->getAdmissionRequirementsFr(),
            'englishTestRequirements' => $establishment->getEnglishTestRequirements(),
            'academicRequirements' => $establishment->getAcademicRequirements(),
            'documentRequirements' => $establishment->getDocumentRequirements(),
            'visaRequirements' => $establishment->getVisaRequirements(),
            'applicationFee' => $establishment->getApplicationFee(),
            'applicationFeeCurrency' => $establishment->getApplicationFeeCurrency(),
            'livingCosts' => $establishment->getLivingCosts(),
            'livingCostsCurrency' => $establishment->getLivingCostsCurrency(),
            'tuitionRange' => $this->calculateTuitionRange($establishment),
            'createdAt' => $establishment->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $establishment->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'programsList' => array_map(function ($program) use ($user) {
                $isProgramShortlisted = false;
                if ($user) {
                    $existingProgramShortlist = $this->shortlistRepository->findByUserAndProgram($user, $program);
                    $isProgramShortlisted = $existingProgramShortlist !== null;
                }

                return [
                    'id' => $program->getId(),
                    'name' => $program->getName(),
                    'description' => $program->getDescription(),
                    'studyLevel' => $program->getStudyLevel(),
                    'subject' => $program->getSubject(),
                    'field' => $program->getField(),
                    'duration' => $program->getDuration(),
                    'durationUnit' => $program->getDurationUnit(),
                    'durationUnit' => $program->getDurationUnit(),
                    'tuition' => $program->getTuition(),
                    'tuitionAmount' => $program->getTuitionAmount(),
                    'tuitionCurrency' => $program->getTuitionCurrency(),
                    'startDate' => $program->getStartDate(),
                    'applicationDeadline' => $program->getApplicationDeadline(),
                    'language' => $program->getLanguage(),
                    'studyType' => $program->getStudyType(),
                    'intake' => $program->getIntake(),
                    'scholarships' => $program->isScholarships(),
                    'ranking' => $program->getRanking(),
                    'isActive' => $program->isActive(),
                    'isShortlisted' => $isProgramShortlisted,
                    'structuredRequirements' => $program->getStructuredRequirements(),
                    'housing' => $program->isHousing(),
                    'establishmentRankings' => $program->getEstablishment()?->getRankings(),
                    'multiIntakes' => $program->getMultiIntakes()
                ];
            }, $programs),
            'rankings' => $establishment->getRankings(),
            'languages' => $establishment->getLanguages() ?? [],
            'campusPhotos' => $establishment->getCampusPhotos() ?? [],
            'youtubeVideos' => $establishment->getYoutubeVideos() ?? [],
            'brochures' => $establishment->getBrochures() ?? [],
            'campusLocations' => $establishment->getCampusLocations() ?? [],
            'seoTitle' => $establishment->getSeoTitle(),
            'seoDescription' => $establishment->getSeoDescription(),
            'seoKeywords' => $establishment->getSeoKeywords() ?? [],
            'seoImageAlt' => $establishment->getSeoImageAlt(),
            'status' => $establishment->getStatus()
        ];

        // Récupérer les données JSON directement depuis la base de données si les getters ne fonctionnent pas
        $connection = $this->entityManager->getConnection();
        $sql = 'SELECT campus_photos, youtube_videos, brochures, campus_locations, seo_title, seo_description, seo_keywords, seo_image_alt FROM establishments WHERE id = ?';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery([$establishment->getId()]);
        $row = $result->fetchAssociative();

        if ($row) {
            $campusPhotos = $row['campus_photos'] ? json_decode($row['campus_photos'], true) : [];
            // Corriger les URLs des photos de campus
            if (is_array($campusPhotos)) {
                foreach ($campusPhotos as &$photo) {
                    if (isset($photo['url']) && strpos($photo['url'], 'http://localhost:5173/uploads/') === 0) {
                        $photo['url'] = str_replace('http://localhost:5173/uploads/', '/uploads/', $photo['url']);
                    }
                }
            }
            $data['campusPhotos'] = $campusPhotos;

            $data['youtubeVideos'] = $row['youtube_videos'] ? json_decode($row['youtube_videos'], true) : [];
            $data['brochures'] = $row['brochures'] ? json_decode($row['brochures'], true) : [];
            $data['campusLocations'] = $row['campus_locations'] ? json_decode($row['campus_locations'], true) : [];
            $data['seoTitle'] = $row['seo_title'] ?: $data['seoTitle'];
            $data['seoDescription'] = $row['seo_description'] ?: $data['seoDescription'];
            $data['seoKeywords'] = $row['seo_keywords'] ? json_decode($row['seo_keywords'], true) : [];
            $data['seoImageAlt'] = $row['seo_image_alt'] ?: $data['seoImageAlt'];
        }

        return new JsonResponse([
            'success' => true,
            'data' => $data
        ]);
    }

    #[Route('/{slug}/programs', name: 'establishment_programs', methods: ['GET'])]
    public function getEstablishmentPrograms(string $slug, Request $request): JsonResponse
    {
        $establishment = $this->establishmentRepository->findOneBy(['slug' => $slug]);

        if (!$establishment || !$establishment->isActive()) {
            throw new NotFoundHttpException('Establishment not found');
        }

        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);
        $offset = ($page - 1) * $limit;

        $programs = $this->programRepository->findBy(
            ['establishment' => $establishment, 'isActive' => true],
            ['name' => 'ASC'],
            $limit,
            $offset
        );

        $totalPrograms = $this->programRepository->count([
            'establishment' => $establishment,
            'isActive' => true
        ]);

        // Check if programs are in user's shortlist
        $user = $this->getUserFromToken($request);

        $programsData = array_map(function ($program) use ($user) {
            $isProgramShortlisted = false;
            if ($user) {
                $existingProgramShortlist = $this->shortlistRepository->findByUserAndProgram($user, $program);
                $isProgramShortlisted = $existingProgramShortlist !== null;
            }

            return [
                'id' => $program->getId(),
                'name' => $program->getName(),
                'description' => $program->getDescription(),
                'studyLevel' => $program->getStudyLevel(),
                'subject' => $program->getSubject(),
                'field' => $program->getField(),
                'duration' => $program->getDuration(),
                'durationUnit' => $program->getDurationUnit(),
                'tuition' => $program->getTuition(),
                'tuitionAmount' => $program->getTuitionAmount(),
                'tuitionCurrency' => $program->getTuitionCurrency(),
                'startDate' => $program->getStartDate(),
                'applicationDeadline' => $program->getApplicationDeadline(),
                'language' => $program->getLanguage(),
                'studyType' => $program->getStudyType(),
                'intake' => $program->getIntake(),
                'scholarships' => $program->isScholarships(),
                'ranking' => $program->getRanking(),
                'isActive' => $program->isActive(),
                'isShortlisted' => $isProgramShortlisted,
                'structuredRequirements' => $program->getStructuredRequirements(),
                'housing' => $program->isHousing(),
                'establishmentRankings' => $program->getEstablishment()?->getRankings()
            ];
        }, $programs);

        return new JsonResponse([
            'success' => true,
            'data' => $programsData,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $totalPrograms,
                'pages' => ceil($totalPrograms / $limit)
            ]
        ]);
    }

    private function calculateTuitionRange($establishment): array
    {
        // Get programs directly from the database
        $programs = $this->programRepository->findBy(['establishment' => $establishment, 'isActive' => true]);

        $tuitionAmounts = [];
        $currencies = [];

        // First, try to get tuition from programs
        foreach ($programs as $program) {
            if ($program->getTuitionAmount() && $program->getTuitionCurrency()) {
                $amount = (float) $program->getTuitionAmount();
                $currency = $program->getTuitionCurrency();

                $tuitionAmounts[] = $amount;
                $currencies[] = $currency;
            }
        }

        // If no programs or no program tuition, use establishment tuition
        if (empty($tuitionAmounts)) {
            if ($establishment->getTuitionMin() && $establishment->getTuitionMax() && $establishment->getTuitionCurrency()) {
                return [
                    'min' => number_format((float) $establishment->getTuitionMin(), 2, '.', ''),
                    'max' => number_format((float) $establishment->getTuitionMax(), 2, '.', ''),
                    'currency' => $establishment->getTuitionCurrency()
                ];
            }

            return [
                'min' => null,
                'max' => null,
                'currency' => null
            ];
        }

        $minAmount = min($tuitionAmounts);
        $maxAmount = max($tuitionAmounts);

        return [
            'min' => number_format($minAmount, 2, '.', ''),
            'max' => number_format($maxAmount, 2, '.', ''),
            'currency' => $currencies[0] // Assume all programs use the same currency
        ];
    }

    private function getUserFromToken(Request $request): ?\App\Entity\User
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7); // Remove 'Bearer ' prefix

        try {
            $payload = $this->jwtManager->parse($token);
            $username = $payload['username'] ?? null;

            if (!$username) {
                return null;
            }

            return $this->userRepository->findOneBy(['email' => $username]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
