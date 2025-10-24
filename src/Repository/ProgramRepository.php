<?php

namespace App\Repository;

use App\Entity\Program;
use App\Service\CurrencyConversionService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 */
class ProgramRepository extends ServiceEntityRepository
{
    private CurrencyConversionService $currencyService;

    public function __construct(ManagerRegistry $registry, CurrencyConversionService $currencyService)
    {
        parent::__construct($registry, Program::class);
        $this->currencyService = $currencyService;
    }

    private function getCurrencyConversionCase(): string
    {
        return "
            CASE p.tuitionCurrency
                WHEN 'USD' THEN p.tuitionAmount
                WHEN 'EUR' THEN p.tuitionAmount / 0.85
                WHEN 'GBP' THEN p.tuitionAmount / 0.73
                WHEN 'CAD' THEN p.tuitionAmount / 1.25
                WHEN 'AUD' THEN p.tuitionAmount / 1.35
                WHEN 'CHF' THEN p.tuitionAmount / 0.92
                WHEN 'JPY' THEN p.tuitionAmount / 110.0
                WHEN 'CNY' THEN p.tuitionAmount / 6.45
                WHEN 'INR' THEN p.tuitionAmount / 75.0
                WHEN 'BRL' THEN p.tuitionAmount / 5.2
                WHEN 'MAD' THEN p.tuitionAmount / 9.0
                WHEN 'AED' THEN p.tuitionAmount / 3.67
                WHEN 'SAR' THEN p.tuitionAmount / 3.75
                WHEN 'EGP' THEN p.tuitionAmount / 15.7
                WHEN 'ZAR' THEN p.tuitionAmount / 14.5
                WHEN 'KRW' THEN p.tuitionAmount / 1180.0
                WHEN 'SGD' THEN p.tuitionAmount / 1.35
                WHEN 'HKD' THEN p.tuitionAmount / 7.8
                WHEN 'NZD' THEN p.tuitionAmount / 1.4
                WHEN 'SEK' THEN p.tuitionAmount / 8.5
                WHEN 'NOK' THEN p.tuitionAmount / 8.7
                WHEN 'DKK' THEN p.tuitionAmount / 6.3
                WHEN 'PLN' THEN p.tuitionAmount / 3.9
                WHEN 'CZK' THEN p.tuitionAmount / 21.5
                WHEN 'HUF' THEN p.tuitionAmount / 300.0
                WHEN 'RON' THEN p.tuitionAmount / 4.2
                WHEN 'BGN' THEN p.tuitionAmount / 1.66
                WHEN 'HRK' THEN p.tuitionAmount / 6.4
                WHEN 'RSD' THEN p.tuitionAmount / 100.0
                WHEN 'TRY' THEN p.tuitionAmount / 8.5
                WHEN 'RUB' THEN p.tuitionAmount / 75.0
                WHEN 'UAH' THEN p.tuitionAmount / 27.0
                WHEN 'ILS' THEN p.tuitionAmount / 3.2
                WHEN 'QAR' THEN p.tuitionAmount / 3.64
                WHEN 'KWD' THEN p.tuitionAmount / 0.30
                WHEN 'BHD' THEN p.tuitionAmount / 0.38
                WHEN 'OMR' THEN p.tuitionAmount / 0.38
                WHEN 'JOD' THEN p.tuitionAmount / 0.71
                WHEN 'LBP' THEN p.tuitionAmount / 1500.0
                WHEN 'PKR' THEN p.tuitionAmount / 160.0
                WHEN 'BDT' THEN p.tuitionAmount / 85.0
                WHEN 'LKR' THEN p.tuitionAmount / 200.0
                WHEN 'NPR' THEN p.tuitionAmount / 120.0
                WHEN 'AFN' THEN p.tuitionAmount / 80.0
                WHEN 'THB' THEN p.tuitionAmount / 33.0
                WHEN 'VND' THEN p.tuitionAmount / 23000.0
                WHEN 'IDR' THEN p.tuitionAmount / 14500.0
                WHEN 'MYR' THEN p.tuitionAmount / 4.2
                WHEN 'PHP' THEN p.tuitionAmount / 50.0
                WHEN 'TWD' THEN p.tuitionAmount / 28.0
                WHEN 'MXN' THEN p.tuitionAmount / 20.0
                WHEN 'ARS' THEN p.tuitionAmount / 100.0
                WHEN 'CLP' THEN p.tuitionAmount / 800.0
                WHEN 'COP' THEN p.tuitionAmount / 3800.0
                WHEN 'PEN' THEN p.tuitionAmount / 3.7
                WHEN 'UYU' THEN p.tuitionAmount / 44.0
                WHEN 'PYG' THEN p.tuitionAmount / 7000.0
                WHEN 'BOB' THEN p.tuitionAmount / 6.9
                WHEN 'VES' THEN p.tuitionAmount / 4000000.0
                WHEN 'GYD' THEN p.tuitionAmount / 210.0
                WHEN 'SRD' THEN p.tuitionAmount / 21.0
                WHEN 'DZD' THEN p.tuitionAmount / 135.0
                WHEN 'TND' THEN p.tuitionAmount / 2.8
                WHEN 'LYD' THEN p.tuitionAmount / 4.5
                WHEN 'SDG' THEN p.tuitionAmount / 55.0
                WHEN 'ETB' THEN p.tuitionAmount / 45.0
                WHEN 'KES' THEN p.tuitionAmount / 110.0
                WHEN 'UGX' THEN p.tuitionAmount / 3500.0
                WHEN 'TZS' THEN p.tuitionAmount / 2300.0
                WHEN 'GHS' THEN p.tuitionAmount / 6.0
                WHEN 'NGN' THEN p.tuitionAmount / 410.0
                WHEN 'BWP' THEN p.tuitionAmount / 11.0
                WHEN 'NAD' THEN p.tuitionAmount / 14.5
                WHEN 'ZWL' THEN p.tuitionAmount / 360.0
                WHEN 'ZMW' THEN p.tuitionAmount / 18.0
                WHEN 'MWK' THEN p.tuitionAmount / 820.0
                WHEN 'MZN' THEN p.tuitionAmount / 64.0
                WHEN 'AOA' THEN p.tuitionAmount / 650.0
                WHEN 'XAF' THEN p.tuitionAmount / 550.0
                WHEN 'XOF' THEN p.tuitionAmount / 550.0
                WHEN 'CDF' THEN p.tuitionAmount / 2000.0
                WHEN 'STN' THEN p.tuitionAmount / 20.0
                WHEN 'GNF' THEN p.tuitionAmount / 10000.0
                WHEN 'SLE' THEN p.tuitionAmount / 11.0
                WHEN 'LRD' THEN p.tuitionAmount / 160.0
                WHEN 'GMD' THEN p.tuitionAmount / 52.0
                WHEN 'MRU' THEN p.tuitionAmount / 36.0
                WHEN 'CVE' THEN p.tuitionAmount / 100.0
                WHEN 'KMF' THEN p.tuitionAmount / 450.0
                WHEN 'SCR' THEN p.tuitionAmount / 13.5
                WHEN 'MUR' THEN p.tuitionAmount / 40.0
                WHEN 'MGA' THEN p.tuitionAmount / 4000.0
                WHEN 'LSL' THEN p.tuitionAmount / 14.5
                WHEN 'SZL' THEN p.tuitionAmount / 14.5
                WHEN 'BYN' THEN p.tuitionAmount / 2.5
                WHEN 'MDL' THEN p.tuitionAmount / 17.5
                WHEN 'GEL' THEN p.tuitionAmount / 3.1
                WHEN 'AMD' THEN p.tuitionAmount / 520.0
                WHEN 'AZN' THEN p.tuitionAmount / 1.7
                WHEN 'KZT' THEN p.tuitionAmount / 425.0
                WHEN 'UZS' THEN p.tuitionAmount / 10750.0
                WHEN 'KGS' THEN p.tuitionAmount / 84.0
                WHEN 'TJS' THEN p.tuitionAmount / 11.3
                WHEN 'TMT' THEN p.tuitionAmount / 3.5
                WHEN 'MVR' THEN p.tuitionAmount / 15.4
                WHEN 'MMK' THEN p.tuitionAmount / 1800.0
                WHEN 'LAK' THEN p.tuitionAmount / 9500.0
                WHEN 'KHR' THEN p.tuitionAmount / 4100.0
                WHEN 'BND' THEN p.tuitionAmount / 1.35
                WHEN 'MOP' THEN p.tuitionAmount / 8.0
                WHEN 'MNT' THEN p.tuitionAmount / 2850.0
                WHEN 'KPW' THEN p.tuitionAmount / 900.0
                WHEN 'YER' THEN p.tuitionAmount / 250.0
                WHEN 'IQD' THEN p.tuitionAmount / 1460.0
                WHEN 'IRR' THEN p.tuitionAmount / 42000.0
                WHEN 'SYP' THEN p.tuitionAmount / 2500.0
                WHEN 'ISK' THEN p.tuitionAmount / 130.0
                WHEN 'ALL' THEN p.tuitionAmount / 105.0
                WHEN 'MKD' THEN p.tuitionAmount / 52.0
                WHEN 'BAM' THEN p.tuitionAmount / 1.66
                ELSE p.tuitionAmount
            END
        ";
    }

    public function save(Program $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Program $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFeatured(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.featured = :featured')
            ->setParameter('active', true)
            ->setParameter('featured', true)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByEstablishment(int $establishmentId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.establishment = :establishmentId')
            ->setParameter('active', true)
            ->setParameter('establishmentId', $establishmentId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCountry(string $country): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.country = :country')
            ->setParameter('active', true)
            ->setParameter('country', $country)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.city = :city')
            ->setParameter('active', true)
            ->setParameter('city', $city)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByDegree(string $degree): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.degree = :degree')
            ->setParameter('active', true)
            ->setParameter('degree', $degree)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByStudyLevel(string $studyLevel): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.studyLevel = :studyLevel')
            ->setParameter('active', true)
            ->setParameter('studyLevel', $studyLevel)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySubject(string $subject): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.subject = :subject')
            ->setParameter('active', true)
            ->setParameter('subject', $subject)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByLanguage(string $language): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.language = :language')
            ->setParameter('active', true)
            ->setParameter('language', $language)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByStudyType(string $studyType): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.studyType = :studyType')
            ->setParameter('active', true)
            ->setParameter('studyType', $studyType)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByUniversityType(string $universityType): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.universityType = :universityType')
            ->setParameter('active', true)
            ->setParameter('universityType', $universityType)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.name LIKE :name')
            ->setParameter('active', true)
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(array $filters, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true);

        if (!empty($filters['establishmentId'])) {
            $qb->andWhere('p.establishment = :establishmentId')
                ->setParameter('establishmentId', $filters['establishmentId']);
        }

        if (!empty($filters['country'])) {
            if (is_array($filters['country'])) {
                $qb->andWhere('p.country IN (:countries)')
                    ->setParameter('countries', $filters['country']);
            } else {
                $qb->andWhere('p.country = :country')
                    ->setParameter('country', $filters['country']);
            }
        }

        if (!empty($filters['city'])) {
            $qb->andWhere('p.city = :city')
                ->setParameter('city', $filters['city']);
        }

        if (!empty($filters['degree'])) {
            $qb->andWhere('p.degree = :degree')
                ->setParameter('degree', $filters['degree']);
        }

        if (!empty($filters['studyLevel'])) {
            if (is_array($filters['studyLevel'])) {
                $qb->andWhere('p.studyLevel IN (:studyLevels)')
                    ->setParameter('studyLevels', $filters['studyLevel']);
            } else {
                $qb->andWhere('p.studyLevel = :studyLevel')
                    ->setParameter('studyLevel', $filters['studyLevel']);
            }
        }

        if (!empty($filters['subject'])) {
            if (is_array($filters['subject'])) {
                $qb->andWhere('p.subject IN (:subjects)')
                    ->setParameter('subjects', $filters['subject']);
            } else {
                $qb->andWhere('p.subject = :subject')
                    ->setParameter('subject', $filters['subject']);
            }
        }

        if (!empty($filters['language'])) {
            $qb->andWhere('p.language = :language')
                ->setParameter('language', $filters['language']);
        }

        if (!empty($filters['studyType'])) {
            if (is_array($filters['studyType'])) {
                $qb->andWhere('p.studyType IN (:studyTypes)')
                    ->setParameter('studyTypes', $filters['studyType']);
            } else {
                $qb->andWhere('p.studyType = :studyType')
                    ->setParameter('studyType', $filters['studyType']);
            }
        }

        if (!empty($filters['universityType'])) {
            if (is_array($filters['universityType'])) {
                $qb->andWhere('p.universityType IN (:universityTypes)')
                    ->setParameter('universityTypes', $filters['universityType']);
            } else {
                $qb->andWhere('p.universityType = :universityType')
                    ->setParameter('universityType', $filters['universityType']);
            }
        }

        if (!empty($filters['scholarships'])) {
            $qb->andWhere('p.scholarships = :scholarships')
                ->setParameter('scholarships', $filters['scholarships']);
        }

        if (!empty($filters['featured'])) {
            $qb->andWhere('p.featured = :featured')
                ->setParameter('featured', $filters['featured']);
        }

        if (!empty($filters['aidvisorRecommended'])) {
            $qb->andWhere('p.aidvisorRecommended = :aidvisorRecommended')
                ->setParameter('aidvisorRecommended', $filters['aidvisorRecommended']);
        }

        if (!empty($filters['easyApply'])) {
            $qb->andWhere('p.easyApply = :easyApply')
                ->setParameter('easyApply', $filters['easyApply']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        $qb->orderBy('p.name', 'ASC');

        if (!empty($filters['startYear'])) {
            if (is_array($filters['startYear'])) {
                $qb->andWhere('p.startYear IN (:startYears)')
                    ->setParameter('startYears', $filters['startYear']);
            } else {
                $qb->andWhere('p.startYear = :startYear')
                    ->setParameter('startYear', $filters['startYear']);
            }
        }

        if (!empty($filters['intake'])) {
            if (is_array($filters['intake'])) {
                $qb->andWhere('p.intake IN (:intakes)')
                    ->setParameter('intakes', $filters['intake']);
            } else {
                $qb->andWhere('p.intake = :intake')
                    ->setParameter('intake', $filters['intake']);
            }
        }

        // Academic qualification filters
        if (isset($filters['requiresAcademicQualification'])) {
            if ($filters['requiresAcademicQualification'] === false) {
                // No academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->setParameter('requiresAcademicQualification', false);
            } elseif (is_string($filters['requiresAcademicQualification']) && !empty($filters['requiresAcademicQualification'])) {
                // Specific academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.academicQualifications LIKE :specificQualification')
                    ->setParameter('requiresAcademicQualification', true)
                    ->setParameter('specificQualification', '%' . $filters['requiresAcademicQualification'] . '%');
            } elseif ($filters['requiresAcademicQualification'] === true) {
                // Any academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->setParameter('requiresAcademicQualification', true);
            }
        }

        if (!empty($filters['academicQualifications'])) {
            if (is_array($filters['academicQualifications'])) {
                $qb->andWhere('JSON_OVERLAPS(p.academicQualifications, :academicQualifications) = 1')
                    ->setParameter('academicQualifications', json_encode($filters['academicQualifications']));
            }
        }

        // Grade requirement filters
        if (!empty($filters['minimumGrade']) && !empty($filters['gradeSystem'])) {
            $qb->andWhere('p.minimumGrade <= :minimumGrade')
                ->andWhere('p.gradeSystem = :gradeSystem')
                ->setParameter('minimumGrade', $filters['minimumGrade'])
                ->setParameter('gradeSystem', $filters['gradeSystem']);
        }

        // Detailed grade filters with conversion using dynamic requirements
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
                    ->setParameter('userGradePercentage', $userGradePercentage);
            }
        }

        // English language test filters using dynamic requirements
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
                    ->setParameter('englishTests', $filters['englishTest']);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr2 
                    WHERE pr2.program = p 
                    AND pr2.type = \'language_test\' 
                    AND pr2.subtype = :englishTest
                    AND pr2.isActive = 1 
                    AND pr2.isRequired = 1' . $scoreCondition . '
                )')
                    ->setParameter('englishTest', $filters['englishTest']);
            }

            // Add score parameter if provided
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
        }

        // Standardized test filters using dynamic requirements
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
                    ->setParameter('standardizedTests', $filters['standardizedTest']);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr3 
                    WHERE pr3.program = p 
                    AND pr3.type = \'standardized_test\' 
                    AND pr3.subtype = :standardizedTest
                    AND pr3.isActive = 1 
                    AND pr3.isRequired = 1' . $scoreCondition . '
                )')
                    ->setParameter('standardizedTest', $filters['standardizedTest']);
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

            if (isset($filters['minFees']) && $filters['minFees'] !== null) {
                // Convert user's min fee to USD for comparison
                $minFeesUSD = $this->currencyService->convertToUSD($filters['minFees'], $feeCurrency);

                // Convert program's tuition amount to USD for comparison
                $qb->andWhere('(' . $this->getCurrencyConversionCase() . ') >= :minFeesUSD')
                    ->setParameter('minFeesUSD', $minFeesUSD);
            }

            if (isset($filters['maxFees']) && $filters['maxFees'] !== null) {
                // Convert user's max fee to USD for comparison
                $maxFeesUSD = $this->currencyService->convertToUSD($filters['maxFees'], $feeCurrency);

                // Convert program's tuition amount to USD for comparison
                $qb->andWhere('(' . $this->getCurrencyConversionCase() . ') <= :maxFeesUSD')
                    ->setParameter('maxFeesUSD', $maxFeesUSD);
            }
        }

        // New choice field filters
        // Language Test Filter
        if (!empty($filters['languageTestFilter'])) {
            if ($filters['languageTestFilter'] === 'without') {
                // Programs without language test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )');
            } elseif ($filters['languageTestFilter'] === 'with') {
                // Programs with language test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )');
            }
        }

        // Standardized Test Filter
        if (!empty($filters['standardizedTestFilter'])) {
            if ($filters['standardizedTestFilter'] === 'without') {
                // Programs without standardized test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )');
            } elseif ($filters['standardizedTestFilter'] === 'with') {
                // Programs with standardized test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )');
            }
        }

        // Scholarship Filter
        if (!empty($filters['scholarshipFilter']) && $filters['scholarshipFilter'] === 'with') {
            $qb->andWhere('p.scholarships = :scholarships')
                ->setParameter('scholarships', true);
        }

        // Housing Filter
        if (!empty($filters['housingFilter']) && $filters['housingFilter'] === 'with') {
            $qb->andWhere('p.housing = :housing')
                ->setParameter('housing', true);
        }

        // Ranking Filter
        if (!empty($filters['rankingFilter']) && $filters['rankingFilter'] === 'top') {
            $qb->andWhere('p.ranking IS NOT NULL AND p.ranking <= 100')
                ->orderBy('p.ranking', 'ASC');
        }

        // Featured Filter
        if (!empty($filters['featuredFilter']) && $filters['featuredFilter'] === 'featured') {
            $qb->andWhere('p.featured = :featured')
                ->setParameter('featured', true);
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByFilters(array $filters): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true);

        if (!empty($filters['establishmentId'])) {
            $qb->andWhere('p.establishment = :establishmentId')
                ->setParameter('establishmentId', $filters['establishmentId']);
        }

        if (!empty($filters['country'])) {
            if (is_array($filters['country'])) {
                $qb->andWhere('p.country IN (:countries)')
                    ->setParameter('countries', $filters['country']);
            } else {
                $qb->andWhere('p.country = :country')
                    ->setParameter('country', $filters['country']);
            }
        }

        if (!empty($filters['city'])) {
            $qb->andWhere('p.city = :city')
                ->setParameter('city', $filters['city']);
        }

        if (!empty($filters['degree'])) {
            $qb->andWhere('p.degree = :degree')
                ->setParameter('degree', $filters['degree']);
        }

        if (!empty($filters['studyLevel'])) {
            if (is_array($filters['studyLevel'])) {
                $qb->andWhere('p.studyLevel IN (:studyLevels)')
                    ->setParameter('studyLevels', $filters['studyLevel']);
            } else {
                $qb->andWhere('p.studyLevel = :studyLevel')
                    ->setParameter('studyLevel', $filters['studyLevel']);
            }
        }

        if (!empty($filters['subject'])) {
            if (is_array($filters['subject'])) {
                $qb->andWhere('p.subject IN (:subjects)')
                    ->setParameter('subjects', $filters['subject']);
            } else {
                $qb->andWhere('p.subject = :subject')
                    ->setParameter('subject', $filters['subject']);
            }
        }

        if (!empty($filters['language'])) {
            $qb->andWhere('p.language = :language')
                ->setParameter('language', $filters['language']);
        }

        if (!empty($filters['studyType'])) {
            if (is_array($filters['studyType'])) {
                $qb->andWhere('p.studyType IN (:studyTypes)')
                    ->setParameter('studyTypes', $filters['studyType']);
            } else {
                $qb->andWhere('p.studyType = :studyType')
                    ->setParameter('studyType', $filters['studyType']);
            }
        }

        if (!empty($filters['universityType'])) {
            if (is_array($filters['universityType'])) {
                $qb->andWhere('p.universityType IN (:universityTypes)')
                    ->setParameter('universityTypes', $filters['universityType']);
            } else {
                $qb->andWhere('p.universityType = :universityType')
                    ->setParameter('universityType', $filters['universityType']);
            }
        }

        if (!empty($filters['featured'])) {
            $qb->andWhere('p.featured = :featured')
                ->setParameter('featured', $filters['featured']);
        }

        if (!empty($filters['aidvisorRecommended'])) {
            $qb->andWhere('p.aidvisorRecommended = :aidvisorRecommended')
                ->setParameter('aidvisorRecommended', $filters['aidvisorRecommended']);
        }

        if (!empty($filters['easyApply'])) {
            $qb->andWhere('p.easyApply = :easyApply')
                ->setParameter('easyApply', $filters['easyApply']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['startYear'])) {
            if (is_array($filters['startYear'])) {
                $qb->andWhere('p.startYear IN (:startYears)')
                    ->setParameter('startYears', $filters['startYear']);
            } else {
                $qb->andWhere('p.startYear = :startYear')
                    ->setParameter('startYear', $filters['startYear']);
            }
        }

        if (!empty($filters['intake'])) {
            if (is_array($filters['intake'])) {
                $qb->andWhere('p.intake IN (:intakes)')
                    ->setParameter('intakes', $filters['intake']);
            } else {
                $qb->andWhere('p.intake = :intake')
                    ->setParameter('intake', $filters['intake']);
            }
        }

        // Academic qualification filters
        if (isset($filters['requiresAcademicQualification'])) {
            if ($filters['requiresAcademicQualification'] === false) {
                // No academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->setParameter('requiresAcademicQualification', false);
            } elseif (is_string($filters['requiresAcademicQualification']) && !empty($filters['requiresAcademicQualification'])) {
                // Specific academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->andWhere('p.academicQualifications LIKE :specificQualification')
                    ->setParameter('requiresAcademicQualification', true)
                    ->setParameter('specificQualification', '%' . $filters['requiresAcademicQualification'] . '%');
            } elseif ($filters['requiresAcademicQualification'] === true) {
                // Any academic qualification required
                $qb->andWhere('p.requiresAcademicQualification = :requiresAcademicQualification')
                    ->setParameter('requiresAcademicQualification', true);
            }
        }

        if (!empty($filters['academicQualifications'])) {
            if (is_array($filters['academicQualifications'])) {
                $qb->andWhere('JSON_OVERLAPS(p.academicQualifications, :academicQualifications) = 1')
                    ->setParameter('academicQualifications', json_encode($filters['academicQualifications']));
            }
        }

        // Grade requirement filters
        if (!empty($filters['minimumGrade']) && !empty($filters['gradeSystem'])) {
            $qb->andWhere('p.minimumGrade <= :minimumGrade')
                ->andWhere('p.gradeSystem = :gradeSystem')
                ->setParameter('minimumGrade', $filters['minimumGrade'])
                ->setParameter('gradeSystem', $filters['gradeSystem']);
        }

        // Detailed grade filters with conversion using dynamic requirements
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
                    ->setParameter('userGradePercentage', $userGradePercentage);
            }
        }

        // English language test filters using dynamic requirements
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
                    ->setParameter('englishTests', $filters['englishTest']);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr2 
                    WHERE pr2.program = p 
                    AND pr2.type = \'language_test\' 
                    AND pr2.subtype = :englishTest
                    AND pr2.isActive = 1 
                    AND pr2.isRequired = 1' . $scoreCondition . '
                )')
                    ->setParameter('englishTest', $filters['englishTest']);
            }

            // Add score parameter if provided
            if (!empty($parameters)) {
                foreach ($parameters as $key => $value) {
                    $qb->setParameter($key, $value);
                }
            }
        }

        // Standardized test filters using dynamic requirements
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
                    ->setParameter('standardizedTests', $filters['standardizedTest']);
            } else {
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr3 
                    WHERE pr3.program = p 
                    AND pr3.type = \'standardized_test\' 
                    AND pr3.subtype = :standardizedTest
                    AND pr3.isActive = 1 
                    AND pr3.isRequired = 1' . $scoreCondition . '
                )')
                    ->setParameter('standardizedTest', $filters['standardizedTest']);
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

            if (isset($filters['minFees']) && $filters['minFees'] !== null) {
                // Convert user's min fee to USD for comparison
                $minFeesUSD = $this->currencyService->convertToUSD($filters['minFees'], $feeCurrency);

                // Convert program's tuition amount to USD for comparison
                $qb->andWhere('(' . $this->getCurrencyConversionCase() . ') >= :minFeesUSD')
                    ->setParameter('minFeesUSD', $minFeesUSD);
            }

            if (isset($filters['maxFees']) && $filters['maxFees'] !== null) {
                // Convert user's max fee to USD for comparison
                $maxFeesUSD = $this->currencyService->convertToUSD($filters['maxFees'], $feeCurrency);

                // Convert program's tuition amount to USD for comparison
                $qb->andWhere('(' . $this->getCurrencyConversionCase() . ') <= :maxFeesUSD')
                    ->setParameter('maxFeesUSD', $maxFeesUSD);
            }
        }

        // New choice field filters
        // Language Test Filter
        if (!empty($filters['languageTestFilter'])) {
            if ($filters['languageTestFilter'] === 'without') {
                // Programs without language test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )');
            } elseif ($filters['languageTestFilter'] === 'with') {
                // Programs with language test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr4 
                    WHERE pr4.program = p 
                    AND pr4.type = \'language_test\' 
                    AND pr4.isActive = 1 
                    AND pr4.isRequired = 1
                )');
            }
        }

        // Standardized Test Filter
        if (!empty($filters['standardizedTestFilter'])) {
            if ($filters['standardizedTestFilter'] === 'without') {
                // Programs without standardized test requirements
                $qb->andWhere('NOT EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )');
            } elseif ($filters['standardizedTestFilter'] === 'with') {
                // Programs with standardized test requirements
                $qb->andWhere('EXISTS (
                    SELECT 1 FROM App\Entity\ProgramRequirement pr5 
                    WHERE pr5.program = p 
                    AND pr5.type = \'standardized_test\' 
                    AND pr5.isActive = 1 
                    AND pr5.isRequired = 1
                )');
            }
        }

        // Scholarship Filter
        if (!empty($filters['scholarshipFilter']) && $filters['scholarshipFilter'] === 'with') {
            $qb->andWhere('p.scholarships = :scholarships')
                ->setParameter('scholarships', true);
        }

        // Housing Filter
        if (!empty($filters['housingFilter']) && $filters['housingFilter'] === 'with') {
            $qb->andWhere('p.housing = :housing')
                ->setParameter('housing', true);
        }

        // Ranking Filter
        if (!empty($filters['rankingFilter']) && $filters['rankingFilter'] === 'top') {
            $qb->andWhere('p.ranking IS NOT NULL AND p.ranking <= 100');
        }

        // Featured Filter
        if (!empty($filters['featuredFilter']) && $filters['featuredFilter'] === 'featured') {
            $qb->andWhere('p.featured = :featured')
                ->setParameter('featured', true);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getCountries(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.country')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.country IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.country', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getCities(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.city')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.city IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getDegrees(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.degree')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.degree IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.degree', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getStudyLevels(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.studyLevel')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.studyLevel IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.studyLevel', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getSubjects(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.subject')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.subject IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.subject', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getLanguages(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.language')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.language IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.language', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getStudyTypes(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.studyType')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.studyType IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.studyType', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getUniversityTypes(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.universityType')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.universityType IS NOT NULL')
            ->setParameter('active', true)
            ->orderBy('p.universityType', 'ASC')
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
            case 'cgpa20':
                // CGPA 20 scale: convert to percentage (0-100%)
                return ($grade / 20.0) * 100;
            case 'cgpa7':
                // CGPA 7.0 scale: convert to percentage (0-100%)
                return ($grade / 7.0) * 100;
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
