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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/programs', name: 'api_programs_')]
class ProgramDetailController extends AbstractController
{
    public function __construct(
        private ProgramRepository $programRepository,
        private ShortlistRepository $shortlistRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/slug/{slug}', name: 'detail_by_slug', methods: ['GET'], requirements: ['slug' => '.+'])]
    public function getProgramDetailBySlug(string $slug, Request $request): JsonResponse
    {
        // Handle composite slugs like "establishment-slug/program-slug"
        if (strpos($slug, '/') !== false) {
            $slugParts = explode('/', $slug, 2);
            $establishmentSlug = $slugParts[0];
            $programSlug = $slugParts[1];

            // Find program by establishment slug and program slug
            $program = $this->programRepository->createQueryBuilder('p')
                ->join('p.establishment', 'e')
                ->where('e.slug = :establishmentSlug')
                ->andWhere('p.slug = :programSlug')
                ->andWhere('p.isActive = :active')
                ->setParameter('establishmentSlug', $establishmentSlug)
                ->setParameter('programSlug', $programSlug)
                ->setParameter('active', true)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            // Handle simple program slug
            $program = $this->programRepository->findOneBy(['slug' => $slug]);
        }

        if (!$program || !$program->isActive()) {
            throw new NotFoundHttpException('Program not found');
        }

        return $this->getProgramDetail($program->getId(), $request);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function getProgramDetail(string $id, Request $request): JsonResponse
    {
        $programId = (int) $id;
        $program = $this->programRepository->find($programId);

        if (!$program || !$program->isActive()) {
            throw new NotFoundHttpException('Program not found');
        }

        $language = $request->query->get('lang', 'en');
        $establishment = $program->getEstablishment();

        // Get localized content
        $description = $language === 'fr' && $program->getDescriptionFr()
            ? $program->getDescriptionFr()
            : $program->getDescription();

        $name = $language === 'fr' && $program->getNameFr()
            ? $program->getNameFr()
            : $program->getName();

        $curriculum = $program->getCurriculum();

        // Use structured requirements instead of deprecated requirements collection
        $structuredRequirements = $program->getStructuredRequirements();


        // Check if program is shortlisted by current user
        $isShortlisted = false;
        $user = $this->getUser();
        if ($user) {
            $shortlist = $this->shortlistRepository->findByUserAndProgram($user, $program);
            $isShortlisted = $shortlist !== null;
        }

        $data = [
            'id' => $program->getId(),
            'name' => $name,
            'nameFr' => $program->getNameFr(),
            'slug' => $program->getSlug(),
            'isShortlisted' => $isShortlisted,
            'description' => $description,
            'descriptionFr' => $program->getDescriptionFr(),
            'curriculum' => $curriculum,
            'curriculumFr' => $program->getCurriculumFr(),
            'degree' => $program->getDegree(),
            'duration' => $program->getDuration(),
            'studyType' => $program->getStudyType(),
            'studyLevel' => $program->getStudyLevel(),
            'subject' => $program->getSubject(),
            'field' => $program->getField(),
            'language' => $program->getLanguage(),
            'tuition' => $program->getTuition(),
            'tuitionAmount' => $program->getTuitionAmount(),
            'tuitionCurrency' => $program->getTuitionCurrency(),
            'startDate' => $program->getStartDate(),
            'applicationDeadline' => $program->getApplicationDeadline(),
            'intake' => $program->getIntake(),
            'scholarships' => $program->isScholarships(),
            'featured' => $program->isFeatured(),
            'aidvisorRecommended' => $program->isAidvisorRecommended(),
            'easyApply' => $program->isEasyApply(),
            'ranking' => $program->getRanking(),
            'rating' => $program->getRating(),
            'reviews' => $program->getReviews(),
            'universityType' => $program->getUniversityType(),
            'isActive' => $program->isActive(),
            'structuredRequirements' => $program->getStructuredRequirementsByLanguage($language),
            'establishment' => $establishment ? [
                'id' => $establishment->getId(),
                'name' => $establishment->getName(),
                'slug' => $establishment->getSlug(),
                'country' => $establishment->getCountry(),
                'city' => $establishment->getCity(),
                'type' => $establishment->getType(),
                'rating' => $establishment->getRating(),
                'students' => $establishment->getStudents(),
                'programs' => $this->countActivePrograms($establishment),
                'logo' => $establishment->getLogo(),
                'description' => $establishment->getDescription(),
                'featured' => $establishment->isFeatured(),
                'sponsored' => $establishment->isSponsored(),
                'tuition' => $establishment->getTuition(),
                'tuitionRange' => $establishment->getTuitionRange(),
                'acceptanceRate' => $establishment->getAcceptanceRate(),
                'worldRanking' => $establishment->getWorldRanking(),
                'rankings' => [
                    'qs' => $establishment->getQsRanking(),
                    'times' => $establishment->getTimesRanking(),
                    'arwu' => $establishment->getArwuRanking(),
                    'usNews' => $establishment->getUsNewsRanking(),
                ],
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
                'applicationFee' => $establishment->getApplicationFee(),
                'applicationFeeCurrency' => $establishment->getApplicationFeeCurrency(),
                'livingCosts' => $establishment->getLivingCosts(),
                'livingCostsCurrency' => $establishment->getLivingCostsCurrency(),
            ] : null,
        ];

        return new JsonResponse([
            'success' => true,
            'data' => $data
        ]);
    }

    private function countActivePrograms($establishment): int
    {
        return $this->entityManager->getRepository(\App\Entity\Program::class)
            ->count(['establishment' => $establishment, 'isActive' => true]);
    }
}
