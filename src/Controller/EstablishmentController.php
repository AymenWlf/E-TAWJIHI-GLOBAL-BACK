<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Repository\EstablishmentRepository;
use App\Repository\ShortlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/establishments', name: 'api_establishments_')]
class EstablishmentController extends AbstractController
{
    public function __construct(
        private EstablishmentRepository $establishmentRepository,
        private ShortlistRepository $shortlistRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            $filters = $request->query->all();

            // Pagination parameters
            $page = max(1, (int) ($filters['page'] ?? 1));
            $limit = min(50, max(1, (int) ($filters['limit'] ?? 12))); // Default 12, max 50
            $offset = ($page - 1) * $limit;

            // Remove pagination parameters from filters
            unset($filters['page'], $filters['limit'], $filters['offset']);

            $establishments = $this->establishmentRepository->findByFilters($filters, $limit, $offset);
            $total = $this->establishmentRepository->countByFilters($filters);

            $data = array_map(function (Establishment $establishment) {
                return $this->serializeEstablishment($establishment);
            }, $establishments);

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit),
                    'hasNext' => $page < ceil($total / $limit),
                    'hasPrev' => $page > 1
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des établissements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->find($id);

            if (!$establishment || !$establishment->isActive()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->serializeEstablishment($establishment, true)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/slug/{slug}', name: 'show_by_slug', methods: ['GET'])]
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->findOneBy(['slug' => $slug]);

            if (!$establishment || !$establishment->isActive()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->serializeEstablishment($establishment, true)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/featured', name: 'featured', methods: ['GET'])]
    public function featured(): JsonResponse
    {
        try {
            $establishments = $this->establishmentRepository->findFeatured();

            $data = array_map(function (Establishment $establishment) {
                return $this->serializeEstablishment($establishment);
            }, $establishments);

            return new JsonResponse([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des établissements en vedette',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->query->get('q', '');

            if (empty($query)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Terme de recherche requis'
                ], 400);
            }

            $establishments = $this->establishmentRepository->searchByName($query);

            $data = array_map(function (Establishment $establishment) {
                return $this->serializeEstablishment($establishment);
            }, $establishments);

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'query' => $query
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/filters', name: 'filters', methods: ['GET'])]
    public function filters(): JsonResponse
    {
        try {
            $filters = [
                'countries' => $this->establishmentRepository->getCountries(),
                'cities' => $this->establishmentRepository->getCities(),
                'types' => $this->establishmentRepository->getTypes(),
                'universityTypes' => $this->establishmentRepository->getUniversityTypes(),
                'languages' => $this->establishmentRepository->getLanguages()
            ];

            return new JsonResponse([
                'success' => true,
                'data' => $filters
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des filtres',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // #[Route('/{id}/programs', name: 'programs', methods: ['GET'])]
    public function programs(int $id): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->find($id);

            if (!$establishment || !$establishment->isActive()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            $programs = $establishment->getProgramsList()->filter(function ($program) {
                return $program->isActive();
            });

            $data = [];
            foreach ($programs as $program) {
                $data[] = [
                    'id' => $program->getId(),
                    'name' => $program->getName(),
                    'degree' => $program->getDegree(),
                    'duration' => $program->getDuration(),
                    'durationUnit' => $program->getDurationUnit(),
                    'language' => $program->getLanguage(),
                    'tuition' => $program->getTuition(),
                    'startDate' => $program->getStartDate()?->format('Y-m-d'),
                    'applicationDeadline' => $program->getApplicationDeadline()?->format('Y-m-d'),
                    'description' => $program->getDescription(),
                    'scholarships' => $program->isScholarships(),
                    'featured' => $program->isFeatured(),
                    'aidvisorRecommended' => $program->isAidvisorRecommended(),
                    'easyApply' => $program->isEasyApply(),
                    'ranking' => $program->getRanking(),
                    'studyType' => $program->getStudyType(),
                    'universityType' => $program->getUniversityType(),
                    'subject' => $program->getSubject(),
                    'studyLevel' => $program->getStudyLevel(),
                    'rating' => $program->getRating(),
                    'reviews' => $program->getReviews(),
                    'structuredRequirements' => $program->getStructuredRequirements(),
                    'housing' => $program->isHousing(),
                    'establishmentRankings' => $program->getEstablishment()?->getRankings(),
                    'multiIntakes' => $program->getMultiIntakes()
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des programmes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function serializeEstablishment(Establishment $establishment, bool $detailed = false): array
    {
        // Check if establishment is shortlisted by current user
        $isShortlisted = false;
        $user = $this->getUser();
        if ($user) {
            $shortlist = $this->shortlistRepository->findByUserAndEstablishment($user, $establishment);
            $isShortlisted = $shortlist !== null;
        }

        $data = [
            'id' => $establishment->getId(),
            'name' => $establishment->getName(),
            'isShortlisted' => $isShortlisted,
            'slug' => $establishment->getSlug(),
            'country' => $establishment->getCountry(),
            'city' => $establishment->getCity(),
            'type' => $establishment->getType(),
            'rating' => $establishment->getRating(),
            'students' => $establishment->getStudents(),
            'programs' => $this->countActivePrograms($establishment),
            'programsList' => $establishment->getProgramsList() ? $establishment->getProgramsList()->map(function ($program) {
                return [
                    'id' => $program->getId(),
                    'name' => $program->getName(),
                    'studyLevel' => $program->getStudyLevel(),
                    'degree' => $program->getDegree(),
                    'studyType' => $program->getStudyType(),
                    'subject' => $program->getSubject()
                ];
            })->toArray() : [],
            'logo' => $establishment->getLogo(),
            'description' => $establishment->getDescription(),
            'featured' => $establishment->isFeatured(),
            'sponsored' => $establishment->isSponsored(),
            'tuition' => $establishment->getTuition(),
            'tuitionRange' => $establishment->getTuitionRange(),
            'acceptanceRate' => $establishment->getAcceptanceRate(),
            'worldRanking' => $establishment->getWorldRanking(),
            'rankings' => $establishment->getRankings(),
            'popularPrograms' => $establishment->getPopularPrograms(),
            'applicationDeadline' => $establishment->getApplicationDeadline()?->format('Y-m-d'),
            'scholarships' => $establishment->isScholarships(),
            'scholarshipTypes' => $establishment->getScholarshipTypes(),
            'scholarshipDescription' => $establishment->getScholarshipDescription(),
            'housing' => $establishment->isHousing(),
            'language' => $establishment->getLanguage(),
            'aidvisorRecommended' => $establishment->isAidvisorRecommended(),
            'easyApply' => $establishment->isEasyApply(),
            'universityType' => $establishment->getUniversityType(),
            'servicePricing' => $establishment->getServicePricing(),
            'multiIntakes' => $establishment->getMultiIntakes(),
            'commissionRate' => $establishment->getCommissionRate(),
            'freeApplications' => $establishment->getFreeApplications(),
            'visaSupport' => $establishment->getVisaSupport(),
            'countrySpecific' => $establishment->getCountrySpecific(),
            'languages' => $establishment->getLanguages() ?? [],
            'campusPhotos' => $establishment->getCampusPhotos() ?? [],
            'youtubeVideos' => $establishment->getYoutubeVideos() ?? [],
            'brochures' => $establishment->getBrochures() ?? [],
            'campusLocations' => $establishment->getCampusLocations() ?? [],
            'seoTitle' => $establishment->getSeoTitle(),
            'seoDescription' => $establishment->getSeoDescription(),
            'seoKeywords' => $establishment->getSeoKeywords() ?? [],
            'seoImageAlt' => $establishment->getSeoImageAlt(),
            'status' => $establishment->getStatus(),
        ];

        // Récupérer les données JSON directement depuis la base de données
        $connection = $this->entityManager->getConnection();
        $sql = 'SELECT campus_photos, youtube_videos, brochures, campus_locations, seo_title, seo_description, seo_keywords, seo_image_alt FROM establishments WHERE id = ?';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery([$establishment->getId()]);
        $row = $result->fetchAssociative();
        $data['debug_sql_executed'] = 'SQL executed for ID: ' . $establishment->getId();

        if ($row) {
            $data['campusPhotos'] = $row['campus_photos'] ? json_decode($row['campus_photos'], true) : [];
            $data['youtubeVideos'] = $row['youtube_videos'] ? json_decode($row['youtube_videos'], true) : [];
            $data['brochures'] = $row['brochures'] ? json_decode($row['brochures'], true) : [];
            $data['campusLocations'] = $row['campus_locations'] ? json_decode($row['campus_locations'], true) : [];
            $data['seoTitle'] = $row['seo_title'] ?: $data['seoTitle'];
            $data['seoDescription'] = $row['seo_description'] ?: $data['seoDescription'];
            $data['seoKeywords'] = $row['seo_keywords'] ? json_decode($row['seo_keywords'], true) : [];
            $data['seoImageAlt'] = $row['seo_image_alt'] ?: $data['seoImageAlt'];
            $data['debug_campus_photos_raw'] = $row['campus_photos'];
        } else {
            $data['debug_no_row_found'] = 'No row found for establishment ID: ' . $establishment->getId();
        }

        if ($detailed) {
            $data['website'] = $establishment->getWebsite();
            $data['email'] = $establishment->getEmail();
            $data['phone'] = $establishment->getPhone();
            $data['address'] = $establishment->getAddress();
            $data['accreditations'] = $establishment->getAccreditations();
            $data['accommodation'] = $establishment->isAccommodation();
            $data['careerServices'] = $establishment->isCareerServices();
            $data['languageSupport'] = $establishment->isLanguageSupport();
            $data['createdAt'] = $establishment->getCreatedAt()?->format('Y-m-d H:i:s');
            $data['updatedAt'] = $establishment->getUpdatedAt()?->format('Y-m-d H:i:s');
        }

        return $data;
    }

    private function countActivePrograms(Establishment $establishment): int
    {
        return $this->entityManager->getRepository(\App\Entity\Program::class)
            ->count(['establishment' => $establishment, 'isActive' => true]);
    }
}
