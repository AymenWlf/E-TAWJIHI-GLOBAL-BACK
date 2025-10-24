<?php

namespace App\Repository;

use App\Entity\Establishment;
use App\Service\CurrencyConversionService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Establishment>
 */
class EstablishmentRepository extends ServiceEntityRepository
{
    private CurrencyConversionService $currencyService;

    public function __construct(ManagerRegistry $registry, CurrencyConversionService $currencyService)
    {
        parent::__construct($registry, Establishment::class);
        $this->currencyService = $currencyService;
    }

    private function getCurrencyConversionCase(): string
    {
        return "
            CASE pr2.tuitionCurrency
                WHEN 'USD' THEN pr2.tuitionAmount
                WHEN 'EUR' THEN pr2.tuitionAmount / 0.85
                WHEN 'GBP' THEN pr2.tuitionAmount / 0.73
                WHEN 'CAD' THEN pr2.tuitionAmount / 1.25
                WHEN 'AUD' THEN pr2.tuitionAmount / 1.35
                WHEN 'CHF' THEN pr2.tuitionAmount / 0.92
                WHEN 'JPY' THEN pr2.tuitionAmount / 110.0
                WHEN 'CNY' THEN pr2.tuitionAmount / 6.45
                WHEN 'INR' THEN pr2.tuitionAmount / 75.0
                WHEN 'BRL' THEN pr2.tuitionAmount / 5.2
                WHEN 'MAD' THEN pr2.tuitionAmount / 9.0
                WHEN 'AED' THEN pr2.tuitionAmount / 3.67
                WHEN 'SAR' THEN pr2.tuitionAmount / 3.75
                WHEN 'EGP' THEN pr2.tuitionAmount / 15.7
                WHEN 'ZAR' THEN pr2.tuitionAmount / 14.5
                WHEN 'KRW' THEN pr2.tuitionAmount / 1180.0
                WHEN 'SGD' THEN pr2.tuitionAmount / 1.35
                WHEN 'HKD' THEN pr2.tuitionAmount / 7.8
                WHEN 'NZD' THEN pr2.tuitionAmount / 1.4
                WHEN 'SEK' THEN pr2.tuitionAmount / 8.5
                WHEN 'NOK' THEN pr2.tuitionAmount / 8.7
                WHEN 'DKK' THEN pr2.tuitionAmount / 6.3
                WHEN 'PLN' THEN pr2.tuitionAmount / 3.9
                WHEN 'CZK' THEN pr2.tuitionAmount / 21.5
                WHEN 'HUF' THEN pr2.tuitionAmount / 300.0
                WHEN 'RON' THEN pr2.tuitionAmount / 4.2
                WHEN 'BGN' THEN pr2.tuitionAmount / 1.66
                WHEN 'HRK' THEN pr2.tuitionAmount / 6.4
                WHEN 'RSD' THEN pr2.tuitionAmount / 100.0
                WHEN 'TRY' THEN pr2.tuitionAmount / 8.5
                WHEN 'RUB' THEN pr2.tuitionAmount / 75.0
                WHEN 'UAH' THEN pr2.tuitionAmount / 27.0
                WHEN 'ILS' THEN pr2.tuitionAmount / 3.2
                WHEN 'QAR' THEN pr2.tuitionAmount / 3.64
                WHEN 'KWD' THEN pr2.tuitionAmount / 0.30
                WHEN 'BHD' THEN pr2.tuitionAmount / 0.38
                WHEN 'OMR' THEN pr2.tuitionAmount / 0.38
                WHEN 'JOD' THEN pr2.tuitionAmount / 0.71
                WHEN 'LBP' THEN pr2.tuitionAmount / 1500.0
                WHEN 'PKR' THEN pr2.tuitionAmount / 160.0
                WHEN 'BDT' THEN pr2.tuitionAmount / 85.0
                WHEN 'LKR' THEN pr2.tuitionAmount / 200.0
                WHEN 'NPR' THEN pr2.tuitionAmount / 120.0
                WHEN 'AFN' THEN pr2.tuitionAmount / 80.0
                WHEN 'THB' THEN pr2.tuitionAmount / 33.0
                WHEN 'VND' THEN pr2.tuitionAmount / 23000.0
                WHEN 'IDR' THEN pr2.tuitionAmount / 14500.0
                WHEN 'MYR' THEN pr2.tuitionAmount / 4.2
                WHEN 'PHP' THEN pr2.tuitionAmount / 50.0
                WHEN 'TWD' THEN pr2.tuitionAmount / 28.0
                WHEN 'MXN' THEN pr2.tuitionAmount / 20.0
                WHEN 'ARS' THEN pr2.tuitionAmount / 100.0
                WHEN 'CLP' THEN pr2.tuitionAmount / 800.0
                WHEN 'COP' THEN pr2.tuitionAmount / 3800.0
                WHEN 'PEN' THEN pr2.tuitionAmount / 3.7
                WHEN 'UYU' THEN pr2.tuitionAmount / 44.0
                WHEN 'PYG' THEN pr2.tuitionAmount / 7000.0
                WHEN 'BOB' THEN pr2.tuitionAmount / 6.9
                WHEN 'VES' THEN pr2.tuitionAmount / 4000000.0
                WHEN 'GYD' THEN pr2.tuitionAmount / 210.0
                WHEN 'SRD' THEN pr2.tuitionAmount / 21.0
                WHEN 'DZD' THEN pr2.tuitionAmount / 135.0
                WHEN 'TND' THEN pr2.tuitionAmount / 2.8
                WHEN 'LYD' THEN pr2.tuitionAmount / 4.5
                WHEN 'SDG' THEN pr2.tuitionAmount / 55.0
                WHEN 'ETB' THEN pr2.tuitionAmount / 45.0
                WHEN 'KES' THEN pr2.tuitionAmount / 110.0
                WHEN 'UGX' THEN pr2.tuitionAmount / 3500.0
                WHEN 'TZS' THEN pr2.tuitionAmount / 2300.0
                WHEN 'GHS' THEN pr2.tuitionAmount / 6.0
                WHEN 'NGN' THEN pr2.tuitionAmount / 410.0
                WHEN 'BWP' THEN pr2.tuitionAmount / 11.0
                WHEN 'NAD' THEN pr2.tuitionAmount / 14.5
                WHEN 'ZWL' THEN pr2.tuitionAmount / 360.0
                WHEN 'ZMW' THEN pr2.tuitionAmount / 18.0
                WHEN 'MWK' THEN pr2.tuitionAmount / 820.0
                WHEN 'MZN' THEN pr2.tuitionAmount / 64.0
                WHEN 'AOA' THEN pr2.tuitionAmount / 650.0
                WHEN 'XAF' THEN pr2.tuitionAmount / 550.0
                WHEN 'XOF' THEN pr2.tuitionAmount / 550.0
                WHEN 'CDF' THEN pr2.tuitionAmount / 2000.0
                WHEN 'STN' THEN pr2.tuitionAmount / 20.0
                WHEN 'GNF' THEN pr2.tuitionAmount / 10000.0
                WHEN 'SLE' THEN pr2.tuitionAmount / 11.0
                WHEN 'LRD' THEN pr2.tuitionAmount / 160.0
                WHEN 'GMD' THEN pr2.tuitionAmount / 52.0
                WHEN 'MRU' THEN pr2.tuitionAmount / 36.0
                WHEN 'CVE' THEN pr2.tuitionAmount / 100.0
                WHEN 'KMF' THEN pr2.tuitionAmount / 450.0
                WHEN 'SCR' THEN pr2.tuitionAmount / 13.5
                WHEN 'MUR' THEN pr2.tuitionAmount / 40.0
                WHEN 'MGA' THEN pr2.tuitionAmount / 4000.0
                WHEN 'LSL' THEN pr2.tuitionAmount / 14.5
                WHEN 'SZL' THEN pr2.tuitionAmount / 14.5
                WHEN 'BYN' THEN pr2.tuitionAmount / 2.5
                WHEN 'MDL' THEN pr2.tuitionAmount / 17.5
                WHEN 'GEL' THEN pr2.tuitionAmount / 3.1
                WHEN 'AMD' THEN pr2.tuitionAmount / 520.0
                WHEN 'AZN' THEN pr2.tuitionAmount / 1.7
                WHEN 'KZT' THEN pr2.tuitionAmount / 425.0
                WHEN 'UZS' THEN pr2.tuitionAmount / 10750.0
                WHEN 'KGS' THEN pr2.tuitionAmount / 84.0
                WHEN 'TJS' THEN pr2.tuitionAmount / 11.3
                WHEN 'TMT' THEN pr2.tuitionAmount / 3.5
                WHEN 'MVR' THEN pr2.tuitionAmount / 15.4
                WHEN 'MMK' THEN pr2.tuitionAmount / 1800.0
                WHEN 'LAK' THEN pr2.tuitionAmount / 9500.0
                WHEN 'KHR' THEN pr2.tuitionAmount / 4100.0
                WHEN 'BND' THEN pr2.tuitionAmount / 1.35
                WHEN 'MOP' THEN pr2.tuitionAmount / 8.0
                WHEN 'MNT' THEN pr2.tuitionAmount / 2850.0
                WHEN 'KPW' THEN pr2.tuitionAmount / 900.0
                WHEN 'YER' THEN pr2.tuitionAmount / 250.0
                WHEN 'IQD' THEN pr2.tuitionAmount / 1460.0
                WHEN 'IRR' THEN pr2.tuitionAmount / 42000.0
                WHEN 'SYP' THEN pr2.tuitionAmount / 2500.0
                WHEN 'ISK' THEN pr2.tuitionAmount / 130.0
                WHEN 'ALL' THEN pr2.tuitionAmount / 105.0
                WHEN 'MKD' THEN pr2.tuitionAmount / 52.0
                WHEN 'BAM' THEN pr2.tuitionAmount / 1.66
                ELSE pr2.tuitionAmount
            END
        ";
    }


    public function save(Establishment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Establishment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFeatured(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.featured = :featured')
            ->setParameter('active', true)
            ->setParameter('featured', true)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCountry(string $country): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.country = :country')
            ->setParameter('active', true)
            ->setParameter('country', $country)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.city = :city')
            ->setParameter('active', true)
            ->setParameter('city', $city)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.type = :type')
            ->setParameter('active', true)
            ->setParameter('type', $type)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByUniversityType(string $universityType): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.universityType = :universityType')
            ->setParameter('active', true)
            ->setParameter('universityType', $universityType)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.name LIKE :name')
            ->setParameter('active', true)
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(array $filters, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('DISTINCT e')
            ->leftJoin('e.programsList', 'p')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true);

        if (!empty($filters['country'])) {
            if (is_array($filters['country'])) {
                $qb->andWhere('e.country IN (:countries)')
                    ->setParameter('countries', $filters['country']);
            } else {
                $qb->andWhere('e.country = :country')
                    ->setParameter('country', $filters['country']);
            }
        }

        if (!empty($filters['city'])) {
            $qb->andWhere('e.city = :city')
                ->setParameter('city', $filters['city']);
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('e.type = :type')
                ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['universityType'])) {
            if (is_array($filters['universityType'])) {
                $qb->andWhere('e.universityType IN (:universityTypes)')
                    ->setParameter('universityTypes', $filters['universityType']);
            } else {
                $qb->andWhere('e.universityType = :universityType')
                    ->setParameter('universityType', $filters['universityType']);
            }
        }

        if (!empty($filters['language'])) {
            $qb->andWhere('e.language = :language')
                ->setParameter('language', $filters['language']);
        }

        if (!empty($filters['scholarships'])) {
            $qb->andWhere('e.scholarships = :scholarships')
                ->setParameter('scholarships', $filters['scholarships']);
        }

        if (!empty($filters['housing'])) {
            $qb->andWhere('e.housing = :housing')
                ->setParameter('housing', $filters['housing']);
        }

        if (!empty($filters['featured'])) {
            $qb->andWhere('e.featured = :featured')
                ->setParameter('featured', $filters['featured']);
        }

        if (!empty($filters['aidvisorRecommended'])) {
            $qb->andWhere('e.aidvisorRecommended = :aidvisorRecommended')
                ->setParameter('aidvisorRecommended', $filters['aidvisorRecommended']);
        }

        if (!empty($filters['easyApply'])) {
            $qb->andWhere('e.easyApply = :easyApply')
                ->setParameter('easyApply', $filters['easyApply']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('e.name LIKE :search OR e.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        // Filter by study level of programs
        if (!empty($filters['studyLevel'])) {
            $qb->andWhere('(p.studyLevel IN (:studyLevels) OR p.studyLevel IS NULL)')
                ->andWhere('(p.isActive = :programActive OR p.isActive IS NULL)')
                ->setParameter('studyLevels', $filters['studyLevel'])
                ->setParameter('programActive', true);
        }

        // Filter by study type of programs
        if (!empty($filters['studyType'])) {
            $qb->andWhere('(p.studyType IN (:studyTypes) OR p.studyType IS NULL)')
                ->andWhere('(p.isActive = :programActive OR p.isActive IS NULL)')
                ->setParameter('studyTypes', $filters['studyType'])
                ->setParameter('programActive', true);
        }

        // Academic qualification filters for establishments (based on their programs)
        if (isset($filters['requiresAcademicQualification'])) {
            if ($filters['requiresAcademicQualification'] === false) {
                // No academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('requiresAcademicQualification', false)
                    ->setParameter('programActive', true);
            } elseif (is_string($filters['requiresAcademicQualification']) && !empty($filters['requiresAcademicQualification'])) {
                // Specific academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.academicQualifications LIKE :specificQualification')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('requiresAcademicQualification', true)
                    ->setParameter('specificQualification', '%' . $filters['requiresAcademicQualification'] . '%')
                    ->setParameter('programActive', true);
            } elseif ($filters['requiresAcademicQualification'] === true) {
                // Any academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('requiresAcademicQualification', true)
                    ->setParameter('programActive', true);
            }
        }

        if (!empty($filters['academicQualifications'])) {
            $qb->andWhere('JSON_OVERLAPS(p.academicQualifications, :academicQualifications) = 1')
                ->andWhere('p.isActive = :programActive')
                ->setParameter('academicQualifications', json_encode($filters['academicQualifications']))
                ->setParameter('programActive', true);
        }

        // Grade requirement filters for establishments (based on their programs)
        if (!empty($filters['minimumGrade']) && !empty($filters['gradeSystem'])) {
            $qb->andWhere('p.minimumGrade <= :minimumGrade')
                ->andWhere('p.gradeSystem = :gradeSystem')
                ->andWhere('p.isActive = :programActive')
                ->setParameter('minimumGrade', $filters['minimumGrade'])
                ->setParameter('gradeSystem', $filters['gradeSystem'])
                ->setParameter('programActive', true);
        }

        // Detailed grade filters with conversion for establishments using dynamic requirements
        if (!empty($filters['detailedGrade']) && !empty($filters['detailedGradeType'])) {
            $userGrade = floatval($filters['detailedGrade']);
            $userGradeSystem = $filters['detailedGradeType'];

            // Convert user grade to percentage for comparison
            $userGradePercentage = $this->convertGradeToPercentage($userGrade, $userGradeSystem);

            if ($userGradePercentage !== null) {
                // Use dynamic requirements for grade filtering
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr 
                    WHERE pr.program = p 
                    AND pr.type IN (\'grade\', \'gpa\') 
                    AND pr.isActive = 1 
                    AND pr.isRequired = 1
                    AND (
                        CASE pr.unit
                            WHEN \'4.0\' THEN (pr.minimumValue / 4.0) * 100
                            WHEN \'5.0\' THEN (pr.minimumValue / 5.0) * 100
                            WHEN \'7.0\' THEN (pr.minimumValue / 7.0) * 100
                            WHEN \'10.0\' THEN (pr.minimumValue / 10.0) * 100
                            WHEN \'20.0\' THEN (pr.minimumValue / 20.0) * 100
                            WHEN \'100.0\' THEN pr.minimumValue
                            WHEN \'percentage\' THEN pr.minimumValue
                            ELSE 0
                        END
                    ) <= :userGradePercentage
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('userGradePercentage', $userGradePercentage)
                    ->setParameter('programActive', true);
            }
        }

        // English language test filters using dynamic requirements for establishments
        if (!empty($filters['englishTest'])) {
            $scoreCondition = '';
            $parameters = [];

            if (!empty($filters['englishScore'])) {
                $userScore = floatval($filters['englishScore']);
                $scoreCondition = ' AND pr2.minimumValue <= :userEnglishScore';
                $parameters['userEnglishScore'] = $userScore;
            }

            if (is_array($filters['englishTest'])) {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr2 
                    WHERE pr2.program = p 
                    AND pr2.type = \'language_test\' 
                    AND pr2.subtype IN (:englishTests)
                    AND pr2.isActive = 1 
                    AND pr2.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('englishTests', $filters['englishTest'])
                    ->setParameter('programActive', true);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr2 
                    WHERE pr2.program = p 
                    AND pr2.type = \'language_test\' 
                    AND pr2.subtype = :englishTest
                    AND pr2.isActive = 1 
                    AND pr2.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('englishTest', $filters['englishTest'])
                    ->setParameter('programActive', true);
            }

            // Add score parameter if provided
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
        }

        // Standardized test filters using dynamic requirements for establishments
        if (!empty($filters['standardizedTest'])) {
            $scoreCondition = '';
            $parameters = [];

            if (!empty($filters['standardizedScore'])) {
                $userScore = floatval($filters['standardizedScore']);
                $scoreCondition = ' AND pr3.minimumValue <= :userStandardizedScore';
                $parameters['userStandardizedScore'] = $userScore;
            }

            if (is_array($filters['standardizedTest'])) {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr3 
                    WHERE pr3.program = p 
                    AND pr3.type = \'standardized_test\' 
                    AND pr3.subtype IN (:standardizedTests)
                    AND pr3.isActive = 1 
                    AND pr3.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('standardizedTests', $filters['standardizedTest'])
                    ->setParameter('programActive', true);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr3 
                    WHERE pr3.program = p 
                    AND pr3.type = \'standardized_test\' 
                    AND pr3.subtype = :standardizedTest
                    AND pr3.isActive = 1 
                    AND pr3.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('standardizedTest', $filters['standardizedTest'])
                    ->setParameter('programActive', true);
            }

            // Add score parameter if provided
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
        }

        $qb->orderBy('e.name', 'ASC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        // Fee range filters with currency conversion
        if (isset($filters['minFees']) || isset($filters['maxFees'])) {
            $feeCurrency = $filters['feeCurrency'] ?? 'USD';

            // Join with programs to filter by their tuition fees
            $qb->leftJoin('e.programsList', 'pr2')
                ->andWhere('(pr2.isActive = :programActive OR pr2.isActive IS NULL)')
                ->setParameter('programActive', true);

            if (isset($filters['minFees']) && $filters['minFees'] !== null) {
                // Convert user's min fee to USD for comparison
                $minFeesUSD = $this->currencyService->convertToUSD($filters['minFees'], $feeCurrency);

                // Filter establishments that have programs with tuition >= minFees OR establishments without programs
                $qb->andWhere('((' . $this->getCurrencyConversionCase() . ') >= :minFeesUSD OR pr2.id IS NULL)')
                    ->setParameter('minFeesUSD', $minFeesUSD);
            }

            if (isset($filters['maxFees']) && $filters['maxFees'] !== null) {
                // Convert user's max fee to USD for comparison
                $maxFeesUSD = $this->currencyService->convertToUSD($filters['maxFees'], $feeCurrency);

                // Filter establishments that have programs with tuition <= maxFees OR establishments without programs
                $qb->andWhere('((' . $this->getCurrencyConversionCase() . ') <= :maxFeesUSD OR pr2.id IS NULL)')
                    ->setParameter('maxFeesUSD', $maxFeesUSD);
            }

            // DISTINCT already applied at the beginning of the query
        }

        // New choice field filters
        // Language Test Filter
        if (!empty($filters['languageTestFilter'])) {
            if ($filters['languageTestFilter'] === 'without') {
                // Establishments with programs without language test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            } elseif ($filters['languageTestFilter'] === 'with') {
                // Establishments with programs that have language test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            }
        }

        // Standardized Test Filter
        if (!empty($filters['standardizedTestFilter'])) {
            if ($filters['standardizedTestFilter'] === 'without') {
                // Establishments with programs without standardized test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            } elseif ($filters['standardizedTestFilter'] === 'with') {
                // Establishments with programs that have standardized test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            }
        }

        // Scholarship Filter
        if (!empty($filters['scholarshipFilter']) && $filters['scholarshipFilter'] === 'with') {
            $qb->andWhere('e.scholarships = :scholarships')
                ->setParameter('scholarships', true);
        }

        // Housing Filter
        if (!empty($filters['housingFilter']) && $filters['housingFilter'] === 'with') {
            $qb->andWhere('e.housing = :housing')
                ->setParameter('housing', true);
        }

        // Ranking Filter
        if (!empty($filters['rankingFilter']) && $filters['rankingFilter'] === 'top') {
            $qb->andWhere('e.ranking IS NOT NULL AND e.ranking <= 100')
                ->orderBy('e.ranking', 'ASC');
        }

        // Featured Filter
        if (!empty($filters['featuredFilter']) && $filters['featuredFilter'] === 'featured') {
            $qb->andWhere('e.featured = :featured')
                ->setParameter('featured', true);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByFilters(array $filters): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(DISTINCT e.id)')
            ->leftJoin('e.programsList', 'p')
            ->andWhere('e.isActive = :active')
            ->setParameter('active', true);

        if (!empty($filters['country'])) {
            if (is_array($filters['country'])) {
                $qb->andWhere('e.country IN (:countries)')
                    ->setParameter('countries', $filters['country']);
            } else {
                $qb->andWhere('e.country = :country')
                    ->setParameter('country', $filters['country']);
            }
        }

        if (!empty($filters['city'])) {
            $qb->andWhere('e.city = :city')
                ->setParameter('city', $filters['city']);
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('e.type = :type')
                ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['universityType'])) {
            if (is_array($filters['universityType'])) {
                $qb->andWhere('e.universityType IN (:universityTypes)')
                    ->setParameter('universityTypes', $filters['universityType']);
            } else {
                $qb->andWhere('e.universityType = :universityType')
                    ->setParameter('universityType', $filters['universityType']);
            }
        }

        if (!empty($filters['language'])) {
            $qb->andWhere('e.language = :language')
                ->setParameter('language', $filters['language']);
        }

        if (!empty($filters['scholarships'])) {
            $qb->andWhere('e.scholarships = :scholarships')
                ->setParameter('scholarships', $filters['scholarships']);
        }

        if (!empty($filters['housing'])) {
            $qb->andWhere('e.housing = :housing')
                ->setParameter('housing', $filters['housing']);
        }

        if (!empty($filters['featured'])) {
            $qb->andWhere('e.featured = :featured')
                ->setParameter('featured', $filters['featured']);
        }

        if (!empty($filters['aidvisorRecommended'])) {
            $qb->andWhere('e.aidvisorRecommended = :aidvisorRecommended')
                ->setParameter('aidvisorRecommended', $filters['aidvisorRecommended']);
        }

        if (!empty($filters['easyApply'])) {
            $qb->andWhere('e.easyApply = :easyApply')
                ->setParameter('easyApply', $filters['easyApply']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('e.name LIKE :search OR e.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        // Filter by study level of programs
        if (!empty($filters['studyLevel'])) {
            $qb->andWhere('(p.studyLevel IN (:studyLevels) OR p.studyLevel IS NULL)')
                ->andWhere('(p.isActive = :programActive OR p.isActive IS NULL)')
                ->setParameter('studyLevels', $filters['studyLevel'])
                ->setParameter('programActive', true);
        }

        // Filter by study type of programs
        if (!empty($filters['studyType'])) {
            $qb->andWhere('(p.studyType IN (:studyTypes) OR p.studyType IS NULL)')
                ->andWhere('(p.isActive = :programActive OR p.isActive IS NULL)')
                ->setParameter('studyTypes', $filters['studyType'])
                ->setParameter('programActive', true);
        }

        // Academic qualification filters for establishments (based on their programs)
        if (isset($filters['requiresAcademicQualification'])) {
            if ($filters['requiresAcademicQualification'] === false) {
                // No academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('requiresAcademicQualification', false)
                    ->setParameter('programActive', true);
            } elseif (is_string($filters['requiresAcademicQualification']) && !empty($filters['requiresAcademicQualification'])) {
                // Specific academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.academicQualifications LIKE :specificQualification')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('requiresAcademicQualification', true)
                    ->setParameter('specificQualification', '%' . $filters['requiresAcademicQualification'] . '%')
                    ->setParameter('programActive', true);
            } elseif ($filters['requiresAcademicQualification'] === true) {
                // Any academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('requiresAcademicQualification', true)
                    ->setParameter('programActive', true);
            }
        }

        if (!empty($filters['academicQualifications'])) {
            $qb->andWhere('JSON_OVERLAPS(p.academicQualifications, :academicQualifications) = 1')
                ->andWhere('p.isActive = :programActive')
                ->setParameter('academicQualifications', json_encode($filters['academicQualifications']))
                ->setParameter('programActive', true);
        }

        // Grade requirement filters for establishments (based on their programs)
        if (!empty($filters['minimumGrade']) && !empty($filters['gradeSystem'])) {
            $qb->andWhere('p.minimumGrade <= :minimumGrade')
                ->andWhere('p.gradeSystem = :gradeSystem')
                ->andWhere('p.isActive = :programActive')
                ->setParameter('minimumGrade', $filters['minimumGrade'])
                ->setParameter('gradeSystem', $filters['gradeSystem'])
                ->setParameter('programActive', true);
        }

        // Detailed grade filters with conversion for establishments using dynamic requirements
        if (!empty($filters['detailedGrade']) && !empty($filters['detailedGradeType'])) {
            $userGrade = floatval($filters['detailedGrade']);
            $userGradeSystem = $filters['detailedGradeType'];

            // Convert user grade to percentage for comparison
            $userGradePercentage = $this->convertGradeToPercentage($userGrade, $userGradeSystem);

            if ($userGradePercentage !== null) {
                // Use dynamic requirements for grade filtering
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr 
                    WHERE pr.program = p 
                    AND pr.type IN (\'grade\', \'gpa\') 
                    AND pr.isActive = 1 
                    AND pr.isRequired = 1
                    AND (
                        CASE pr.unit
                            WHEN \'4.0\' THEN (pr.minimumValue / 4.0) * 100
                            WHEN \'5.0\' THEN (pr.minimumValue / 5.0) * 100
                            WHEN \'7.0\' THEN (pr.minimumValue / 7.0) * 100
                            WHEN \'10.0\' THEN (pr.minimumValue / 10.0) * 100
                            WHEN \'20.0\' THEN (pr.minimumValue / 20.0) * 100
                            WHEN \'100.0\' THEN pr.minimumValue
                            WHEN \'percentage\' THEN pr.minimumValue
                            ELSE 0
                        END
                    ) <= :userGradePercentage
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('userGradePercentage', $userGradePercentage)
                    ->setParameter('programActive', true);
            }
        }

        // English language test filters using dynamic requirements for establishments
        if (!empty($filters['englishTest'])) {
            $scoreCondition = '';
            $parameters = [];

            if (!empty($filters['englishScore'])) {
                $userScore = floatval($filters['englishScore']);
                $scoreCondition = ' AND pr2.minimumValue <= :userEnglishScore';
                $parameters['userEnglishScore'] = $userScore;
            }

            if (is_array($filters['englishTest'])) {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr2 
                    WHERE pr2.program = p 
                    AND pr2.type = \'language_test\' 
                    AND pr2.subtype IN (:englishTests)
                    AND pr2.isActive = 1 
                    AND pr2.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('englishTests', $filters['englishTest'])
                    ->setParameter('programActive', true);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr2 
                    WHERE pr2.program = p 
                    AND pr2.type = \'language_test\' 
                    AND pr2.subtype = :englishTest
                    AND pr2.isActive = 1 
                    AND pr2.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('englishTest', $filters['englishTest'])
                    ->setParameter('programActive', true);
            }

            // Add score parameter if provided
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
        }

        // Standardized test filters using dynamic requirements for establishments
        if (!empty($filters['standardizedTest'])) {
            $scoreCondition = '';
            $parameters = [];

            if (!empty($filters['standardizedScore'])) {
                $userScore = floatval($filters['standardizedScore']);
                $scoreCondition = ' AND pr3.minimumValue <= :userStandardizedScore';
                $parameters['userStandardizedScore'] = $userScore;
            }

            if (is_array($filters['standardizedTest'])) {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr3 
                    WHERE pr3.program = p 
                    AND pr3.type = \'standardized_test\' 
                    AND pr3.subtype IN (:standardizedTests)
                    AND pr3.isActive = 1 
                    AND pr3.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('standardizedTests', $filters['standardizedTest'])
                    ->setParameter('programActive', true);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr3 
                    WHERE pr3.program = p 
                    AND pr3.type = \'standardized_test\' 
                    AND pr3.subtype = :standardizedTest
                    AND pr3.isActive = 1 
                    AND pr3.isRequired = 1' . $scoreCondition . '
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('standardizedTest', $filters['standardizedTest'])
                    ->setParameter('programActive', true);
            }

            // Add score parameter if provided
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
        }

        // Fee range filters with currency conversion
        if (isset($filters['minFees']) || isset($filters['maxFees'])) {
            $feeCurrency = $filters['feeCurrency'] ?? 'USD';

            // Join with programs to filter by their tuition fees
            $qb->leftJoin('e.programsList', 'pr2')
                ->andWhere('(pr2.isActive = :programActive OR pr2.isActive IS NULL)')
                ->setParameter('programActive', true);

            if (isset($filters['minFees']) && $filters['minFees'] !== null) {
                // Convert user's min fee to USD for comparison
                $minFeesUSD = $this->currencyService->convertToUSD($filters['minFees'], $feeCurrency);

                // Filter establishments that have programs with tuition >= minFees OR establishments without programs
                $qb->andWhere('((' . $this->getCurrencyConversionCase() . ') >= :minFeesUSD OR pr2.id IS NULL)')
                    ->setParameter('minFeesUSD', $minFeesUSD);
            }

            if (isset($filters['maxFees']) && $filters['maxFees'] !== null) {
                // Convert user's max fee to USD for comparison
                $maxFeesUSD = $this->currencyService->convertToUSD($filters['maxFees'], $feeCurrency);

                // Filter establishments that have programs with tuition <= maxFees OR establishments without programs
                $qb->andWhere('((' . $this->getCurrencyConversionCase() . ') <= :maxFeesUSD OR pr2.id IS NULL)')
                    ->setParameter('maxFeesUSD', $maxFeesUSD);
            }

            // DISTINCT already applied at the beginning of the query
        }

        // New choice field filters
        // Language Test Filter
        if (!empty($filters['languageTestFilter'])) {
            if ($filters['languageTestFilter'] === 'without') {
                // Establishments with programs without language test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            } elseif ($filters['languageTestFilter'] === 'with') {
                // Establishments with programs that have language test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            }
        }

        // Standardized Test Filter
        if (!empty($filters['standardizedTestFilter'])) {
            if ($filters['standardizedTestFilter'] === 'without') {
                // Establishments with programs without standardized test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            } elseif ($filters['standardizedTestFilter'] === 'with') {
                // Establishments with programs that have standardized test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )')
                    ->andWhere('p.isActive = :programActive')
                    ->setParameter('programActive', true);
            }
        }

        // Scholarship Filter
        if (!empty($filters['scholarshipFilter']) && $filters['scholarshipFilter'] === 'with') {
            $qb->andWhere('e.scholarships = :scholarships')
                ->setParameter('scholarships', true);
        }

        // Housing Filter
        if (!empty($filters['housingFilter']) && $filters['housingFilter'] === 'with') {
            $qb->andWhere('e.housing = :housing')
                ->setParameter('housing', true);
        }

        // Ranking Filter
        if (!empty($filters['rankingFilter']) && $filters['rankingFilter'] === 'top') {
            $qb->andWhere('e.ranking IS NOT NULL AND e.ranking <= 100');
        }

        // Featured Filter
        if (!empty($filters['featuredFilter']) && $filters['featuredFilter'] === 'featured') {
            $qb->andWhere('e.featured = :featured')
                ->setParameter('featured', true);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getCountries(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.country')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.country IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('e.country', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getCities(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.city')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.city IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('e.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getTypes(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.type')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.type IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('e.type', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getUniversityTypes(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.universityType')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.universityType IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('e.universityType', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getLanguages(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.language')
            ->andWhere('e.isActive = :active')
            ->andWhere('e.language IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('e.language', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Convert grade to percentage for comparison
     */
    private function convertGradeToPercentage(float $grade, string $gradeSystem): ?float
    {
        switch ($gradeSystem) {
            case 'cgpa4':
                // CGPA 4.0 scale: convert to percentage (0-100%)
                return ($grade / 4.0) * 100;
            case 'cgpa7':
                // CGPA 7.0 scale: convert to percentage (0-100%)
                return ($grade / 7.0) * 100;
            case 'cgpa20':
                // CGPA 20 scale: convert to percentage (0-100%)
                return ($grade / 20.0) * 100;
            case 'percentage':
                // Already in percentage
                return $grade;
            case 'gpa5':
                // GPA 5.0 scale: convert to percentage (0-100%)
                return ($grade / 5.0) * 100;
            case 'gpa10':
                // GPA 10.0 scale: convert to percentage (0-100%)
                return ($grade / 10.0) * 100;
            default:
                return null;
        }
    }
}
