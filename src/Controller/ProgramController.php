<?php

namespace App\Controller;

use App\Entity\Program;
use App\Repository\ProgramRepository;
use App\Repository\ShortlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/programs', name: 'api_programs_')]
class ProgramController extends AbstractController
{
    public function __construct(
        private ProgramRepository $programRepository,
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

            $programs = $this->programRepository->findByFilters($filters, $limit, $offset);
            $total = $this->programRepository->countByFilters($filters);

            $data = array_map(function (Program $program) {
                return $this->serializeProgram($program);
            }, $programs);

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
                'message' => 'Erreur lors de la récupération des programmes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $program = $this->programRepository->find($id);

            if (!$program || !$program->isActive()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Programme non trouvé'
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->serializeProgram($program, true)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération du programme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/slug/{slug}', name: 'show_by_slug', methods: ['GET'])]
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $program = $this->programRepository->findOneBy(['slug' => $slug]);

            if (!$program || !$program->isActive()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Programme non trouvé'
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->serializeProgram($program, true)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération du programme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/featured', name: 'featured', methods: ['GET'])]
    public function featured(): JsonResponse
    {
        try {
            $programs = $this->programRepository->findFeatured();

            $data = array_map(function (Program $program) {
                return $this->serializeProgram($program);
            }, $programs);

            return new JsonResponse([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des programmes en vedette',
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

            $programs = $this->programRepository->searchByName($query);

            $data = array_map(function (Program $program) {
                return $this->serializeProgram($program);
            }, $programs);

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
                'countries' => $this->programRepository->getCountries(),
                'cities' => $this->programRepository->getCities(),
                'degrees' => $this->programRepository->getDegrees(),
                'studyLevels' => $this->programRepository->getStudyLevels(),
                'subjects' => $this->programRepository->getSubjects(),
                'languages' => $this->programRepository->getLanguages(),
                'studyTypes' => $this->programRepository->getStudyTypes(),
                'universityTypes' => $this->programRepository->getUniversityTypes()
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

    #[Route('/establishment/{establishmentId}', name: 'by_establishment', methods: ['GET'])]
    public function byEstablishment(int $establishmentId): JsonResponse
    {
        try {
            $programs = $this->programRepository->findByEstablishment($establishmentId);

            $data = array_map(function (Program $program) {
                return $this->serializeProgram($program);
            }, $programs);

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des programmes de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function serializeProgram(Program $program, bool $detailed = false): array
    {
        // Check if program is shortlisted by current user
        $isShortlisted = false;
        $user = $this->getUser();
        if ($user) {
            $shortlist = $this->shortlistRepository->findByUserAndProgram($user, $program);
            $isShortlisted = $shortlist !== null;
        }

        $data = [
            'id' => $program->getId(),
            'name' => $program->getName(),
            'isShortlisted' => $isShortlisted,
            'establishment' => [
                'id' => $program->getEstablishment()?->getId(),
                'name' => $program->getEstablishment()?->getName(),
                'country' => $program->getEstablishment()?->getCountry(),
                'city' => $program->getEstablishment()?->getCity(),
                'logo' => $program->getEstablishment()?->getLogo(),
                'type' => $program->getEstablishment()?->getType(),
                'rating' => $program->getEstablishment()?->getRating(),
                'worldRanking' => $program->getEstablishment()?->getWorldRanking()
            ],
            'country' => $program->getCountry(),
            'city' => $program->getCity(),
            'degree' => $program->getDegree(),
            'duration' => $program->getDuration(),
            'durationUnit' => $program->getDurationUnit(),
            'language' => $program->getLanguage(),
            'tuition' => $program->getTuition(),
            'tuitionAmount' => $program->getTuitionAmount(),
            'tuitionCurrency' => $program->getTuitionCurrency(),
            'startDate' => $program->getStartDate()?->format('Y-m-d'),
            'applicationDeadline' => $program->getApplicationDeadline()?->format('Y-m-d'),
            'description' => $program->getDescription(),
            'scholarships' => $program->isScholarships(),
            'logo' => $program->getLogo(),
            'featured' => $program->isFeatured(),
            'aidvisorRecommended' => $program->isAidvisorRecommended(),
            'easyApply' => $program->isEasyApply(),
            'ranking' => $program->getRanking(),
            'studyType' => $program->getStudyType(),
            'universityType' => $program->getUniversityType(),
            'programType' => $program->getProgramType(),
            'servicePricing' => $program->getServicePricing(),
            'subject' => $program->getSubject(),
            'field' => $program->getField(),
            'studyLevel' => $program->getStudyLevel(),
            'intake' => $program->getIntake(),
            'rating' => $program->getRating(),
            'reviews' => $program->getReviews(),
            'structuredRequirements' => $program->getStructuredRequirements(),
            'housing' => $program->isHousing(),
            'establishmentRankings' => $program->getEstablishment()?->getRankings(),
            'multiIntakes' => $program->getMultiIntakes(),
            'campusPhotos' => $program->getCampusPhotos(),
            'campusLocations' => $program->getCampusLocations(),
            'youtubeVideos' => $program->getYoutubeVideos(),
            'brochures' => $program->getBrochures()
        ];

        if ($detailed) {
            $data['languages'] = $program->getLanguages();
            $data['intakes'] = $program->getIntakes();
            $data['subjects'] = $program->getSubjects();
            $data['studyLevels'] = $program->getStudyLevels();
            $data['curriculum'] = $program->getCurriculum();
            $data['careerProspects'] = $program->getCareerProspects();
            $data['faculty'] = $program->getFaculty();
            $data['facilities'] = $program->getFacilities();
            $data['accreditations'] = $program->getAccreditations();
            $data['createdAt'] = $program->getCreatedAt()?->format('Y-m-d H:i:s');
            $data['updatedAt'] = $program->getUpdatedAt()?->format('Y-m-d H:i:s');
        }

        return $data;
    }
}
