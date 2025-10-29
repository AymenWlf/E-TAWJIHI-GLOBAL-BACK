<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Entity\Program;
use App\Repository\EstablishmentRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/admin', name: 'api_admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EstablishmentRepository $establishmentRepository,
        private ProgramRepository $programRepository,
        private SluggerInterface $slugger,
        private ValidatorInterface $validator
    ) {}

    // ===== ESTABLISHMENTS MANAGEMENT =====

    #[Route('/establishments', name: 'establishments_list', methods: ['GET'])]
    public function getEstablishments(Request $request): JsonResponse
    {
        try {
            $page = max(1, (int) $request->query->get('page', 1));
            $limit = min(100, max(1, (int) $request->query->get('limit', 20)));
            $search = $request->query->get('search', '');
            $country = $request->query->get('country', '');
            $type = $request->query->get('type', '');
            $universityType = $request->query->get('universityType', '');
            $isActive = $request->query->get('isActive', '');

            $offset = ($page - 1) * $limit;

            $filters = [];
            if ($search) $filters['search'] = $search;
            if ($country) $filters['country'] = $country;
            if ($type) $filters['type'] = $type;
            if ($universityType) $filters['universityType'] = $universityType;
            if ($isActive !== '') $filters['isActive'] = $isActive === 'true';

            $establishments = $this->establishmentRepository->findByFilters($filters, $limit, $offset);
            $total = $this->establishmentRepository->countByFilters($filters);

            $data = array_map(function (Establishment $establishment) {
                return $this->serializeEstablishmentForAdmin($establishment);
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

    #[Route('/establishments/{id}', name: 'establishments_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getEstablishment(int $id): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->find($id);

            if (!$establishment) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->serializeEstablishmentForAdmin($establishment, true)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/establishments', name: 'establishments_create', methods: ['POST'])]
    public function createEstablishment(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Données JSON invalides'
                ], 400);
            }

            $establishment = new Establishment();
            $this->updateEstablishmentFromData($establishment, $data);

            // Generate slug if not provided
            if (!$establishment->getSlug() && $establishment->getName()) {
                $slug = $this->slugger->slug($establishment->getName())->lower()->toString();
                $establishment->setSlug($slug);
            }

            $errors = $this->validator->validate($establishment);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $errorMessages
                ], 400);
            }

            $this->entityManager->persist($establishment);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Établissement créé avec succès',
                'data' => $this->serializeEstablishmentForAdmin($establishment)
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/establishments/{id}', name: 'establishments_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function updateEstablishment(int $id, Request $request): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->find($id);

            if (!$establishment) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Données JSON invalides'
                ], 400);
            }

            $this->updateEstablishmentFromData($establishment, $data);
            $establishment->setUpdatedAt(new \DateTime());

            $errors = $this->validator->validate($establishment);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $errorMessages
                ], 400);
            }

            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Établissement mis à jour avec succès',
                'data' => $this->serializeEstablishmentForAdmin($establishment)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/establishments/{id}', name: 'establishments_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteEstablishment(int $id): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->find($id);

            if (!$establishment) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            $this->entityManager->remove($establishment);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Établissement supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'établissement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/establishments/{id}/toggle-status', name: 'establishments_toggle_status', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function toggleEstablishmentStatus(int $id): JsonResponse
    {
        try {
            $establishment = $this->establishmentRepository->find($id);

            if (!$establishment) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Établissement non trouvé'
                ], 404);
            }

            $establishment->setIsActive(!$establishment->isActive());
            $establishment->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Statut de l\'établissement mis à jour',
                'data' => [
                    'id' => $establishment->getId(),
                    'isActive' => $establishment->isActive()
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ===== PROGRAMS MANAGEMENT =====

    #[Route('/programs', name: 'programs_list', methods: ['GET'])]
    public function getPrograms(Request $request): JsonResponse
    {
        try {
            $page = max(1, (int) $request->query->get('page', 1));
            $limit = min(100, max(1, (int) $request->query->get('limit', 20)));
            $search = $request->query->get('search', '');
            $establishmentId = $request->query->get('establishmentId');
            $degree = $request->query->get('degree');
            $studyLevel = $request->query->get('studyLevel');
            $isActive = $request->query->get('isActive', '');

            $offset = ($page - 1) * $limit;

            $qb = $this->programRepository->createQueryBuilder('p')
                ->leftJoin('p.establishment', 'e')
                ->addSelect('e');

            if ($search) {
                $qb->andWhere('LOWER(p.name) LIKE :search OR LOWER(p.nameFr) LIKE :search OR LOWER(e.name) LIKE :search')
                    ->setParameter('search', '%' . strtolower($search) . '%');
            }
            if ($establishmentId) {
                $qb->andWhere('e.id = :eid')->setParameter('eid', (int) $establishmentId);
            }
            if ($degree) {
                $qb->andWhere('p.degree = :degree')->setParameter('degree', $degree);
            }
            if ($studyLevel) {
                $qb->andWhere('p.studyLevel = :studyLevel')->setParameter('studyLevel', $studyLevel);
            }
            if ($isActive !== '') {
                $qb->andWhere('p.isActive = :active')->setParameter('active', $isActive === 'true');
            }

            $total = (clone $qb)->select('COUNT(DISTINCT p.id)')->getQuery()->getSingleScalarResult();

            $programs = $qb->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy('p.updatedAt', 'DESC')
                ->getQuery()
                ->getResult();

            $data = array_map(fn(Program $program) => $this->serializeProgramForAdmin($program), $programs);

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => (int) $total,
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

    #[Route('/programs/{id}', name: 'programs_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function getProgram(int $id): JsonResponse
    {
        try {
            $program = $this->programRepository->find($id);
            if (!$program) {
                return new JsonResponse(['success' => false, 'message' => 'Programme non trouvé'], 404);
            }
            return new JsonResponse(['success' => true, 'data' => $this->serializeProgramForAdmin($program, true)]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la récupération du programme', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/programs', name: 'programs_create', methods: ['POST'])]
    public function createProgram(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return new JsonResponse(['success' => false, 'message' => 'Données JSON invalides'], 400);
            }

            $program = new Program();
            $this->updateProgramFromData($program, $data);

            // Generate slug if not provided
            if (!$program->getSlug() && $program->getName()) {
                $slug = $this->slugger->slug($program->getName())->lower()->toString();
                $program->setSlug($slug);
            }

            $this->entityManager->persist($program);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Programme créé avec succès', 'data' => $this->serializeProgramForAdmin($program)], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la création du programme', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/programs/{id}', name: 'programs_update', methods: ['PUT'], requirements: ['id' => '\\d+'])]
    public function updateProgram(int $id, Request $request): JsonResponse
    {
        try {
            $program = $this->programRepository->find($id);
            if (!$program) {
                return new JsonResponse(['success' => false, 'message' => 'Programme non trouvé'], 404);
            }

            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return new JsonResponse(['success' => false, 'message' => 'Données JSON invalides'], 400);
            }

            $this->updateProgramFromData($program, $data);
            $program->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Programme mis à jour avec succès', 'data' => $this->serializeProgramForAdmin($program)]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la mise à jour du programme', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/programs/{id}', name: 'programs_delete', methods: ['DELETE'], requirements: ['id' => '\\d+'])]
    public function deleteProgram(int $id): JsonResponse
    {
        try {
            $program = $this->programRepository->find($id);
            if (!$program) {
                return new JsonResponse(['success' => false, 'message' => 'Programme non trouvé'], 404);
            }

            $this->entityManager->remove($program);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Programme supprimé avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression du programme', 'error' => $e->getMessage()], 500);
        }
    }

    // ===== STATISTICS =====

    #[Route('/stats', name: 'admin_stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        try {
            $totalEstablishments = $this->establishmentRepository->count([]);
            $activeEstablishments = $this->establishmentRepository->count(['isActive' => true]);
            $totalPrograms = $this->programRepository->count([]);
            $activePrograms = $this->programRepository->count(['isActive' => true]);

            // Count by country
            $establishmentsByCountry = $this->establishmentRepository->createQueryBuilder('e')
                ->select('e.country, COUNT(e.id) as count')
                ->where('e.isActive = :active')
                ->setParameter('active', true)
                ->groupBy('e.country')
                ->orderBy('count', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            // Count by university type
            $establishmentsByType = $this->establishmentRepository->createQueryBuilder('e')
                ->select('e.universityType, COUNT(e.id) as count')
                ->where('e.isActive = :active')
                ->andWhere('e.universityType IS NOT NULL')
                ->setParameter('active', true)
                ->groupBy('e.universityType')
                ->orderBy('count', 'DESC')
                ->getQuery()
                ->getResult();

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'establishments' => [
                        'total' => $totalEstablishments,
                        'active' => $activeEstablishments,
                        'inactive' => $totalEstablishments - $activeEstablishments
                    ],
                    'programs' => [
                        'total' => $totalPrograms,
                        'active' => $activePrograms,
                        'inactive' => $totalPrograms - $activePrograms
                    ],
                    'byCountry' => $establishmentsByCountry,
                    'byUniversityType' => $establishmentsByType
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ===== HELPER METHODS =====

    private function updateEstablishmentFromData(Establishment $establishment, array $data): void
    {
        if (isset($data['name'])) $establishment->setName($data['name']);
        if (isset($data['nameFr'])) $establishment->setNameFr($data['nameFr']);
        if (isset($data['slug'])) $establishment->setSlug($data['slug']);
        if (isset($data['country'])) $establishment->setCountry($data['country']);
        if (isset($data['city'])) $establishment->setCity($data['city']);
        if (isset($data['type'])) $establishment->setType($data['type']);
        if (isset($data['rating'])) {
            $rating = $data['rating'] === '' || $data['rating'] === null ? null : (float)$data['rating'];
            $establishment->setRating($rating);
        }
        if (isset($data['students'])) $establishment->setStudents($data['students'] === '' ? null : (int)$data['students']);
        if (isset($data['programs'])) $establishment->setPrograms($data['programs'] === '' ? null : (int)$data['programs']);
        if (isset($data['logo'])) $establishment->setLogo($data['logo']);
        if (isset($data['description'])) $establishment->setDescription($data['description']);
        if (isset($data['descriptionFr'])) $establishment->setDescriptionFr($data['descriptionFr']);
        if (isset($data['mission'])) $establishment->setMission($data['mission']);
        if (isset($data['missionFr'])) $establishment->setMissionFr($data['missionFr']);
        if (isset($data['foundedYear'])) $establishment->setFoundedYear($data['foundedYear'] === '' ? null : (int)$data['foundedYear']);
        if (isset($data['featured'])) $establishment->setFeatured($data['featured']);
        if (isset($data['sponsored'])) $establishment->setSponsored($data['sponsored']);
        if (isset($data['tuition'])) {
            $tuition = $data['tuition'] === '' || $data['tuition'] === null ? null : $data['tuition'];
            $establishment->setTuition($tuition);
        }
        if (isset($data['tuitionMin'])) {
            $tuitionMin = $data['tuitionMin'] === '' || $data['tuitionMin'] === null ? null : $data['tuitionMin'];
            $establishment->setTuitionMin($tuitionMin);
        }
        if (isset($data['tuitionMax'])) {
            $tuitionMax = $data['tuitionMax'] === '' || $data['tuitionMax'] === null ? null : $data['tuitionMax'];
            $establishment->setTuitionMax($tuitionMax);
        }
        if (isset($data['tuitionCurrency'])) $establishment->setTuitionCurrency($data['tuitionCurrency']);
        if (isset($data['acceptanceRate'])) {
            $acceptanceRate = $data['acceptanceRate'] === '' || $data['acceptanceRate'] === null ? null : (float)$data['acceptanceRate'];
            $establishment->setAcceptanceRate($acceptanceRate);
        }
        if (isset($data['worldRanking'])) $establishment->setWorldRanking($data['worldRanking'] === '' ? null : (int)$data['worldRanking']);
        if (isset($data['qsRanking'])) $establishment->setQsRanking($data['qsRanking'] === '' ? null : (int)$data['qsRanking']);
        if (isset($data['timesRanking'])) $establishment->setTimesRanking($data['timesRanking'] === '' ? null : (int)$data['timesRanking']);
        if (isset($data['arwuRanking'])) $establishment->setArwuRanking($data['arwuRanking'] === '' ? null : (int)$data['arwuRanking']);
        if (isset($data['usNewsRanking'])) $establishment->setUsNewsRanking($data['usNewsRanking'] === '' ? null : (int)$data['usNewsRanking']);
        if (isset($data['popularPrograms'])) $establishment->setPopularPrograms($data['popularPrograms']);
        if (isset($data['applicationDeadline'])) {
            $deadline = $data['applicationDeadline'] ? new \DateTime($data['applicationDeadline']) : null;
            $establishment->setApplicationDeadline($deadline);
        }
        if (isset($data['scholarships'])) $establishment->setScholarships($data['scholarships']);
        if (isset($data['scholarshipTypes'])) $establishment->setScholarshipTypes($data['scholarshipTypes']);
        if (isset($data['scholarshipDescription'])) $establishment->setScholarshipDescription($data['scholarshipDescription']);
        if (isset($data['housing'])) $establishment->setHousing($data['housing']);
        if (isset($data['language'])) $establishment->setLanguage($data['language']);
        if (isset($data['aidvisorRecommended'])) $establishment->setAidvisorRecommended($data['aidvisorRecommended']);
        if (isset($data['easyApply'])) $establishment->setEasyApply($data['easyApply']);
        if (isset($data['universityType'])) $establishment->setUniversityType($data['universityType']);
        if (isset($data['commissionRate'])) {
            $commissionRate = $data['commissionRate'] === '' || $data['commissionRate'] === null ? null : (float)$data['commissionRate'];
            $establishment->setCommissionRate($commissionRate);
        }
        if (isset($data['freeApplications'])) $establishment->setFreeApplications($data['freeApplications'] === '' ? null : (int)$data['freeApplications']);
        if (isset($data['visaSupport'])) $establishment->setVisaSupport($data['visaSupport']);
        if (isset($data['countrySpecific'])) $establishment->setCountrySpecific($data['countrySpecific']);
        if (isset($data['website'])) $establishment->setWebsite($data['website']);
        if (isset($data['email'])) $establishment->setEmail($data['email']);
        if (isset($data['phone'])) $establishment->setPhone($data['phone']);
        if (isset($data['address'])) $establishment->setAddress($data['address']);
        if (isset($data['accreditations'])) $establishment->setAccreditations($data['accreditations']);
        if (isset($data['accommodation'])) $establishment->setAccommodation($data['accommodation']);
        if (isset($data['careerServices'])) $establishment->setCareerServices($data['careerServices']);
        if (isset($data['languageSupport'])) $establishment->setLanguageSupport($data['languageSupport']);
        if (isset($data['isActive'])) $establishment->setIsActive($data['isActive']);
        if (isset($data['admissionRequirements'])) $establishment->setAdmissionRequirements($data['admissionRequirements']);
        if (isset($data['admissionRequirementsFr'])) $establishment->setAdmissionRequirementsFr($data['admissionRequirementsFr']);
        if (isset($data['englishTestRequirements'])) $establishment->setEnglishTestRequirements($data['englishTestRequirements']);
        if (isset($data['academicRequirements'])) $establishment->setAcademicRequirements($data['academicRequirements']);
        if (isset($data['documentRequirements'])) $establishment->setDocumentRequirements($data['documentRequirements']);
        if (isset($data['visaRequirements'])) $establishment->setVisaRequirements($data['visaRequirements']);
        if (isset($data['applicationFee'])) {
            $applicationFee = $data['applicationFee'] === '' ? null : $data['applicationFee'];
            $establishment->setApplicationFee($applicationFee);
        }
        if (isset($data['applicationFeeCurrency'])) $establishment->setApplicationFeeCurrency($data['applicationFeeCurrency']);
        if (isset($data['livingCosts'])) {
            $livingCosts = $data['livingCosts'] === '' ? null : $data['livingCosts'];
            $establishment->setLivingCosts($livingCosts);
        }
        if (isset($data['livingCostsCurrency'])) $establishment->setLivingCostsCurrency($data['livingCostsCurrency']);

        // Nouveaux champs
        if (isset($data['languages'])) $establishment->setLanguages($data['languages']);
        if (isset($data['youtubeVideos'])) $establishment->setYoutubeVideos($data['youtubeVideos']);
        if (isset($data['brochures'])) $establishment->setBrochures($data['brochures']);
        if (isset($data['campusLocations'])) $establishment->setCampusLocations($data['campusLocations']);
        if (isset($data['campusPhotos'])) $establishment->setCampusPhotos($data['campusPhotos']);
        if (isset($data['status'])) $establishment->setStatus($data['status']);

        // Champs SEO
        if (isset($data['seoTitle'])) $establishment->setSeoTitle($data['seoTitle']);
        if (isset($data['seoDescription'])) $establishment->setSeoDescription($data['seoDescription']);
        if (isset($data['seoKeywords'])) $establishment->setSeoKeywords($data['seoKeywords']);
        if (isset($data['seoImageAlt'])) $establishment->setSeoImageAlt($data['seoImageAlt']);

        // Configuration des prix
        if (isset($data['servicePricing'])) $establishment->setServicePricing($data['servicePricing']);

        // Multi-intakes
        if (isset($data['multiIntakes'])) $establishment->setMultiIntakes($data['multiIntakes']);
    }

    private function serializeEstablishmentForAdmin(Establishment $establishment, bool $detailed = false): array
    {
        $data = [
            'id' => $establishment->getId(),
            'name' => $establishment->getName(),
            'nameFr' => $establishment->getNameFr(),
            'slug' => $establishment->getSlug(),
            'country' => $establishment->getCountry(),
            'city' => $establishment->getCity(),
            'type' => $establishment->getType(),
            'rating' => $establishment->getRating(),
            'students' => $establishment->getStudents(),
            'programs' => $establishment->getPrograms(),
            'logo' => $establishment->getLogo(),
            'featured' => $establishment->isFeatured(),
            'sponsored' => $establishment->isSponsored(),
            'universityType' => $establishment->getUniversityType(),
            'isActive' => $establishment->isActive(),
            'createdAt' => $establishment->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $establishment->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'description' => $establishment->getDescription(),
                'descriptionFr' => $establishment->getDescriptionFr(),
                'mission' => $establishment->getMission(),
                'missionFr' => $establishment->getMissionFr(),
                'foundedYear' => $establishment->getFoundedYear(),
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
                'housing' => $establishment->isHousing(),
                'language' => $establishment->getLanguage(),
                'aidvisorRecommended' => $establishment->isAidvisorRecommended(),
                'easyApply' => $establishment->isEasyApply(),
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
                'admissionRequirements' => $establishment->getAdmissionRequirements(),
                'admissionRequirementsFr' => $establishment->getAdmissionRequirementsFr(),
                'englishTestRequirements' => $establishment->getEnglishTestRequirements(),
                'academicRequirements' => $establishment->getAcademicRequirements(),
                'documentRequirements' => $establishment->getDocumentRequirements(),
                'visaRequirements' => $establishment->getVisaRequirements(),
                'applicationFee' => $establishment->getApplicationFee(),
                'applicationFeeCurrency' => $establishment->getApplicationFeeCurrency(),
                'livingCosts' => $establishment->getLivingCosts(),
                'livingCostsCurrency' => $establishment->getLivingCostsCurrency(),
                'programsCount' => $establishment->getProgramsList()->count(),
                // Nouveaux champs
                'languages' => $establishment->getLanguages(),
                'youtubeVideos' => $establishment->getYoutubeVideos(),
                'brochures' => $establishment->getBrochures(),
                'campusLocations' => $establishment->getCampusLocations(),
                'campusPhotos' => $establishment->getCampusPhotos(),
                'status' => $establishment->getStatus(),
                // Champs SEO
                'seoTitle' => $establishment->getSeoTitle(),
                'seoDescription' => $establishment->getSeoDescription(),
                'seoKeywords' => $establishment->getSeoKeywords(),
                'seoImageAlt' => $establishment->getSeoImageAlt(),
                'servicePricing' => $establishment->getServicePricing(),
                'multiIntakes' => $establishment->getMultiIntakes(),
            ]);
        }

        return $data;
    }

    private function serializeProgramForAdmin(Program $program, bool $detailed = false): array
    {
        $data = [
            'id' => $program->getId(),
            'name' => $program->getName(),
            'nameFr' => $program->getNameFr(),
            'slug' => $program->getSlug(),
            'status' => $program->getStatus(),
            'establishment' => $program->getEstablishment() ? [
                'id' => $program->getEstablishment()->getId(),
                'name' => $program->getEstablishment()->getName(),
                'slug' => $program->getEstablishment()->getSlug(),
                'logo' => $program->getEstablishment()->getLogo(),
                'universityType' => $program->getEstablishment()->getUniversityType(),
            ] : null,
            'degree' => $program->getDegree(),
            'studyLevel' => $program->getStudyLevel(),
            'tuitionAmount' => $program->getTuitionAmount(),
            'tuitionCurrency' => $program->getTuitionCurrency(),
            'language' => $program->getLanguage(),
            'scholarships' => $program->isScholarships(),
            'featured' => $program->isFeatured(),
            'aidvisorRecommended' => $program->isAidvisorRecommended(),
            'easyApply' => $program->isEasyApply(),
            'housing' => $program->isHousing(),
            'oralExam' => $program->isOralExam(),
            'writtenExam' => $program->isWrittenExam(),
            'isActive' => $program->isActive(),
            'createdAt' => $program->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $program->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'description' => $program->getDescription(),
                'descriptionFr' => $program->getDescriptionFr(),
                'country' => $program->getCountry(),
                'city' => $program->getCity(),
                'duration' => $program->getDuration(),
                'durationUnit' => $program->getDurationUnit(),
                'tuition' => $program->getTuition(),
                'startDate' => $program->getStartDate()?->format('Y-m-d'),
                'startYear' => $program->getStartYear(),
                'intake' => $program->getIntake(),
                'applicationDeadline' => $program->getApplicationDeadline()?->format('Y-m-d'),
                'logo' => $program->getLogo(),
                'ranking' => $program->getRanking(),
                'studyType' => $program->getStudyType(),
                'universityType' => $program->getUniversityType(),
                'subject' => $program->getSubject(),
                'field' => $program->getField(),
                'languages' => $program->getLanguages(),
                'intakes' => $program->getIntakes(),
                'subjects' => $program->getSubjects(),
                'studyLevels' => $program->getStudyLevels(),
                'curriculum' => $program->getCurriculum(),
                'curriculumFr' => $program->getCurriculumFr(),
                'careerProspects' => $program->getCareerProspects(),
                'faculty' => $program->getFaculty(),
                'facilities' => $program->getFacilities(),
                'accreditations' => $program->getAccreditations(),
                'rating' => $program->getRating(),
                'reviews' => $program->getReviews(),
                // Qualifications / GPA
                'academicQualifications' => $program->getAcademicQualifications(),
                'gradeRequirements' => $program->getGradeRequirements(),
                'minimumGrade' => $program->getMinimumGrade(),
                'gradeSystem' => $program->getGradeSystem(),
                'requiresAcademicQualification' => $program->isRequiresAcademicQualification(),
                'gpaScale' => $program->getGpaScale(),
                'gpaScore' => $program->getGpaScore(),
                'requiresGPA' => $program->isRequiresGPA(),
                'structuredRequirements' => $program->getStructuredRequirements(),
                'campusPhotos' => $program->getCampusPhotos(),
                'campusLocations' => $program->getCampusLocations(),
                'youtubeVideos' => $program->getYoutubeVideos(),
                'brochures' => $program->getBrochures(),
                'seoTitle' => $program->getSeoTitle(),
                'seoDescription' => $program->getSeoDescription(),
                'seoKeywords' => $program->getSeoKeywords(),
                'multiIntakes' => $program->getMultiIntakes(),
                'servicePricing' => $program->getServicePricing(),
                'programType' => $program->getProgramType(),
            ]);
        }

        return $data;
    }

    private function updateProgramFromData(Program $program, array $data): void
    {
        if (isset($data['name'])) $program->setName($data['name']);
        if (isset($data['nameFr'])) $program->setNameFr($data['nameFr']);
        if (isset($data['slug'])) $program->setSlug($data['slug']);

        // Establishment association
        if (isset($data['establishmentId'])) {
            $establishment = $this->establishmentRepository->find((int) $data['establishmentId']);
            if ($establishment) {
                $program->setEstablishment($establishment);
            }
        }

        if (isset($data['country'])) $program->setCountry($data['country']);
        if (isset($data['city'])) $program->setCity($data['city']);
        if (isset($data['degree'])) $program->setDegree($data['degree']);
        if (isset($data['duration'])) $program->setDuration($data['duration']);
        if (isset($data['durationUnit'])) $program->setDurationUnit($data['durationUnit']);
        if (isset($data['language'])) $program->setLanguage($data['language']);
        if (isset($data['tuition'])) $program->setTuition($data['tuition']);
        if (isset($data['tuitionAmount'])) {
            $tuitionAmount = $data['tuitionAmount'] === '' || $data['tuitionAmount'] === null ? null : (float)$data['tuitionAmount'];
            $program->setTuitionAmount($tuitionAmount);
        }
        if (isset($data['tuitionCurrency'])) $program->setTuitionCurrency($data['tuitionCurrency']);
        if (isset($data['startDate'])) $program->setStartDate($data['startDate'] ? new \DateTime($data['startDate']) : null);
        if (isset($data['startYear'])) $program->setStartYear($data['startYear']);
        if (isset($data['intake'])) $program->setIntake($data['intake']);
        if (isset($data['applicationDeadline'])) $program->setApplicationDeadline($data['applicationDeadline'] ? new \DateTime($data['applicationDeadline']) : null);
        if (isset($data['scholarships'])) $program->setScholarships((bool) $data['scholarships']);
        if (isset($data['logo'])) $program->setLogo($data['logo']);
        if (isset($data['featured'])) $program->setFeatured((bool) $data['featured']);
        if (isset($data['aidvisorRecommended'])) $program->setAidvisorRecommended((bool) $data['aidvisorRecommended']);
        if (isset($data['easyApply'])) $program->setEasyApply((bool) $data['easyApply']);
        if (isset($data['housing'])) $program->setHousing((bool) $data['housing']);
        if (isset($data['oralExam'])) $program->setOralExam((bool) $data['oralExam']);
        if (isset($data['writtenExam'])) $program->setWrittenExam((bool) $data['writtenExam']);
        if (isset($data['ranking'])) $program->setRanking($data['ranking']);
        if (isset($data['studyType'])) $program->setStudyType($data['studyType']);
        if (isset($data['universityType'])) $program->setUniversityType($data['universityType']);
        if (isset($data['subject'])) $program->setSubject($data['subject']);
        if (isset($data['field'])) $program->setField($data['field']);
        if (isset($data['studyLevel'])) $program->setStudyLevel($data['studyLevel']);
        if (isset($data['status'])) $program->setStatus($data['status']);
        if (isset($data['languages'])) $program->setLanguages($data['languages']);
        if (isset($data['intakes'])) $program->setIntakes($data['intakes']);
        if (isset($data['subjects'])) $program->setSubjects($data['subjects']);
        if (isset($data['studyLevels'])) $program->setStudyLevels($data['studyLevels']);
        if (isset($data['description'])) $program->setDescription($data['description']);
        if (isset($data['descriptionFr'])) $program->setDescriptionFr($data['descriptionFr']);
        if (isset($data['curriculum'])) $program->setCurriculum($data['curriculum']);
        if (isset($data['curriculumFr'])) $program->setCurriculumFr($data['curriculumFr']);
        if (isset($data['careerProspects'])) $program->setCareerProspects($data['careerProspects']);
        if (isset($data['faculty'])) $program->setFaculty($data['faculty']);
        if (isset($data['facilities'])) $program->setFacilities($data['facilities']);
        if (isset($data['accreditations'])) $program->setAccreditations($data['accreditations']);
        if (isset($data['rating'])) $program->setRating($data['rating']);
        if (isset($data['reviews'])) $program->setReviews($data['reviews']);
        if (isset($data['isActive'])) $program->setIsActive((bool) $data['isActive']);

        // Qualifications / GPA
        if (isset($data['academicQualifications'])) $program->setAcademicQualifications($data['academicQualifications']);
        if (isset($data['gradeRequirements'])) $program->setGradeRequirements($data['gradeRequirements']);
        if (isset($data['minimumGrade'])) {
            $minimumGrade = $data['minimumGrade'] === '' || $data['minimumGrade'] === null ? null : (float)$data['minimumGrade'];
            $program->setMinimumGrade($minimumGrade);
        }
        if (isset($data['gradeSystem'])) $program->setGradeSystem($data['gradeSystem']);
        if (isset($data['requiresAcademicQualification'])) $program->setRequiresAcademicQualification((bool) $data['requiresAcademicQualification']);
        if (isset($data['gpaScale'])) {
            $gpaScale = $data['gpaScale'] === '' || $data['gpaScale'] === null ? null : (float)$data['gpaScale'];
            $program->setGpaScale($gpaScale);
        }
        if (isset($data['gpaScore'])) {
            $gpaScore = $data['gpaScore'] === '' || $data['gpaScore'] === null ? null : (float)$data['gpaScore'];
            $program->setGpaScore($gpaScore);
        }
        if (isset($data['requiresGPA'])) $program->setRequiresGPA((bool) $data['requiresGPA']);
        if (isset($data['structuredRequirements'])) $program->setStructuredRequirements($data['structuredRequirements']);

        // Media fields
        if (isset($data['campusPhotos'])) $program->setCampusPhotos($data['campusPhotos']);
        if (isset($data['campusLocations'])) $program->setCampusLocations($data['campusLocations']);
        if (isset($data['youtubeVideos'])) $program->setYoutubeVideos($data['youtubeVideos']);
        if (isset($data['brochures'])) $program->setBrochures($data['brochures']);

        // SEO fields
        if (isset($data['seoTitle'])) $program->setSeoTitle($data['seoTitle']);
        if (isset($data['seoDescription'])) $program->setSeoDescription($data['seoDescription']);
        if (isset($data['seoKeywords'])) $program->setSeoKeywords($data['seoKeywords']);
        if (isset($data['multiIntakes'])) $program->setMultiIntakes($data['multiIntakes']);

        // Configuration des prix
        if (isset($data['servicePricing'])) $program->setServicePricing($data['servicePricing']);

        // Type de programme
        if (isset($data['programType'])) $program->setProgramType($data['programType']);
    }

    #[Route('/upload-campus-photo', name: 'upload_campus_photo', methods: ['POST'])]
    public function uploadCampusPhoto(Request $request): JsonResponse
    {
        try {
            $uploadedFile = $request->files->get('file');
            if (!$uploadedFile) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Aucun fichier fourni'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérifier que le fichier a été uploadé correctement
            if (!$uploadedFile->isValid()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l\'upload du fichier: ' . $uploadedFile->getErrorMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérification de la taille (5MB max pour les images)
            $maxSize = 5 * 1024 * 1024; // 5MB en bytes
            $fileSize = $uploadedFile->getSize();
            if ($fileSize === false || $fileSize > $maxSize) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le fichier est trop volumineux. Taille maximum autorisée: 5MB'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérification du type de fichier (images seulement)
            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/webp'
            ];
            $mimeType = $uploadedFile->getMimeType();
            if (!$mimeType || !in_array($mimeType, $allowedTypes)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Type de fichier non autorisé. Types acceptés: JPG, PNG, WEBP'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Créer le répertoire de stockage s'il n'existe pas
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/campus-photos/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new \Exception('Impossible de créer le répertoire de stockage: ' . $uploadDir);
                }
            }

            // Vérifier que le répertoire est accessible en écriture
            if (!is_writable($uploadDir)) {
                throw new \Exception('Le répertoire de stockage n\'est pas accessible en écriture: ' . $uploadDir);
            }

            // Générer un nom de fichier unique
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->guessExtension();
            $fileName = uniqid('campus_', true) . '.' . $extension;
            $filePath = $uploadDir . $fileName;

            // Déplacer le fichier
            try {
                $uploadedFile->move($uploadDir, $fileName);
            } catch (\Exception $e) {
                throw new \Exception('Erreur lors du déplacement du fichier: ' . $e->getMessage());
            }

            // Vérifier que le fichier a été déplacé correctement
            if (!file_exists($filePath)) {
                throw new \Exception('Le fichier n\'a pas été déplacé correctement');
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Photo de campus uploadée avec succès',
                'data' => [
                    'fileName' => $fileName,
                    'originalName' => $originalName,
                    'filePath' => '/uploads/campus-photos/' . $fileName,
                    'mimeType' => $mimeType,
                    'fileSize' => $fileSize,
                    'url' => '/uploads/campus-photos/' . $fileName
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/upload-brochure', name: 'upload_brochure', methods: ['POST'])]
    public function uploadBrochure(Request $request): JsonResponse
    {
        try {
            $uploadedFile = $request->files->get('file');
            if (!$uploadedFile) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Aucun fichier fourni'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérifier que le fichier a été uploadé correctement
            if (!$uploadedFile->isValid()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l\'upload du fichier: ' . $uploadedFile->getErrorMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérification de la taille (10MB max pour les brochures)
            $maxSize = 10 * 1024 * 1024; // 10MB en bytes
            $fileSize = $uploadedFile->getSize();
            if ($fileSize === false || $fileSize > $maxSize) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le fichier est trop volumineux. Taille maximum autorisée: 10MB'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Vérification du type de fichier
            $allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $mimeType = $uploadedFile->getMimeType();
            if (!$mimeType || !in_array($mimeType, $allowedTypes)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Type de fichier non autorisé. Types acceptés: PDF, DOC, DOCX'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Créer le répertoire de stockage s'il n'existe pas
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/brochures/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new \Exception('Impossible de créer le répertoire de stockage: ' . $uploadDir);
                }
            }

            // Vérifier que le répertoire est accessible en écriture
            if (!is_writable($uploadDir)) {
                throw new \Exception('Le répertoire de stockage n\'est pas accessible en écriture: ' . $uploadDir);
            }

            // Générer un nom de fichier unique
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->guessExtension();
            $fileName = uniqid('brochure_', true) . '.' . $extension;
            $filePath = $uploadDir . $fileName;

            // Déplacer le fichier
            try {
                $uploadedFile->move($uploadDir, $fileName);
            } catch (\Exception $e) {
                throw new \Exception('Erreur lors du déplacement du fichier: ' . $e->getMessage());
            }

            // Vérifier que le fichier a été déplacé correctement
            if (!file_exists($filePath)) {
                throw new \Exception('Le fichier n\'a pas été déplacé correctement');
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Brochure uploadée avec succès',
                'data' => [
                    'fileName' => $fileName,
                    'originalName' => $originalName,
                    'filePath' => '/uploads/brochures/' . $fileName,
                    'mimeType' => $mimeType,
                    'fileSize' => $fileSize,
                    'url' => '/uploads/brochures/' . $fileName
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
