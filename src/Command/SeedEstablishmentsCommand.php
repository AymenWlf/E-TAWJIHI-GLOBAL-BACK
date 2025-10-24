<?php

namespace App\Command;

use App\Entity\Establishment;
use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-establishments',
    description: 'Seed establishments and programs data',
)]
class SeedEstablishmentsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding Establishments and Programs');

        // Sample establishments data
        $establishmentsData = [
            [
                'name' => 'University of Toronto',
                'country' => 'Canada',
                'city' => 'Toronto',
                'type' => 'Public',
                'rating' => '4.8',
                'students' => 97000,
                'programs' => 700,
                'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/04/Utoronto_coa.svg/1200px-Utoronto_coa.svg.png',
                'description' => 'One of Canada\'s leading research universities, offering world-class education in diverse fields.',
                'featured' => true,
                'sponsored' => false,
                'tuition' => '$6,100 - $58,160',
                'tuitionMin' => '6100',
                'tuitionMax' => '58160',
                'tuitionCurrency' => 'USD',
                'acceptanceRate' => '43%',
                'worldRanking' => 18,
                'qsRanking' => 18,
                'timesRanking' => 22,
                'arwuRanking' => 25,
                'usNewsRanking' => 20,
                'popularPrograms' => ['Computer Science', 'Business', 'Engineering'],
                'applicationDeadline' => new \DateTime('2025-01-15'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'universityType' => 'A',
                'commissionRate' => '5-15%',
                'freeApplications' => 3,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.utoronto.ca',
                'email' => 'info@utoronto.ca',
                'phone' => '+1-416-978-2011',
                'address' => '27 King\'s College Cir, Toronto, ON M5S 1A1, Canada',
                'accreditations' => ['AACSB', 'EQUIS'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'Sorbonne University',
                'country' => 'France',
                'city' => 'Paris',
                'type' => 'Public',
                'rating' => '4.6',
                'students' => 55000,
                'programs' => 400,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Sorbonne_University_logo.svg/1200px-Sorbonne_University_logo.svg.png',
                'description' => 'A prestigious French university with a rich history and excellent academic programs.',
                'featured' => false,
                'sponsored' => false,
                'tuition' => '€3,770',
                'tuitionMin' => '3770',
                'tuitionMax' => '3770',
                'tuitionCurrency' => 'EUR',
                'acceptanceRate' => '35%',
                'worldRanking' => 83,
                'qsRanking' => 83,
                'timesRanking' => 90,
                'arwuRanking' => 85,
                'usNewsRanking' => 88,
                'popularPrograms' => ['Medicine', 'Law', 'Literature'],
                'applicationDeadline' => new \DateTime('2025-03-01'),
                'scholarships' => true,
                'housing' => false,
                'language' => 'French',
                'aidvisorRecommended' => false,
                'easyApply' => true,
                'universityType' => 'C',
                'commissionRate' => '0%',
                'freeApplications' => null,
                'visaSupport' => 'paid',
                'countrySpecific' => ['type' => 'france', 'requirements' => ['Campus France']],
                'website' => 'https://www.sorbonne-universite.fr',
                'email' => 'contact@sorbonne-universite.fr',
                'phone' => '+33-1-44-27-30-00',
                'address' => '21 Rue de l\'École de Médecine, 75006 Paris, France',
                'accreditations' => ['AERES'],
                'accommodation' => false,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'University of Oxford',
                'country' => 'United Kingdom',
                'city' => 'Oxford',
                'type' => 'Public',
                'rating' => '4.9',
                'students' => 24000,
                'programs' => 350,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/University_of_Oxford.svg/1200px-University_of_Oxford.svg.png',
                'description' => 'One of the oldest and most prestigious universities in the world.',
                'featured' => true,
                'sponsored' => true,
                'tuition' => '£9,250 - £39,010',
                'tuitionMin' => '9250',
                'tuitionMax' => '39010',
                'tuitionCurrency' => 'GBP',
                'acceptanceRate' => '17%',
                'worldRanking' => 1,
                'qsRanking' => 1,
                'timesRanking' => 1,
                'arwuRanking' => 2,
                'usNewsRanking' => 1,
                'popularPrograms' => ['Philosophy', 'Politics', 'Economics'],
                'applicationDeadline' => new \DateTime('2025-01-15'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'universityType' => 'A',
                'commissionRate' => '10-20%',
                'freeApplications' => 2,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.ox.ac.uk',
                'email' => 'admissions@ox.ac.uk',
                'phone' => '+44-1865-270000',
                'address' => 'Wellington Square, Oxford OX1 2JD, UK',
                'accreditations' => ['QAA'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'Harvard University',
                'country' => 'United States',
                'city' => 'Cambridge',
                'type' => 'Private',
                'rating' => '4.9',
                'students' => 23000,
                'programs' => 200,
                'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/2/29/Harvard_shield_wreath.svg/1200px-Harvard_shield_wreath.svg.png',
                'description' => 'America\'s oldest institution of higher learning, founded in 1636.',
                'featured' => true,
                'sponsored' => true,
                'tuition' => '$54,269',
                'tuitionMin' => '54269',
                'tuitionMax' => '54269',
                'tuitionCurrency' => 'USD',
                'acceptanceRate' => '3%',
                'worldRanking' => 3,
                'qsRanking' => 3,
                'timesRanking' => 2,
                'arwuRanking' => 1,
                'usNewsRanking' => 2,
                'popularPrograms' => ['Business', 'Law', 'Medicine'],
                'applicationDeadline' => new \DateTime('2025-01-01'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'universityType' => 'A',
                'commissionRate' => '15-25%',
                'freeApplications' => 1,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.harvard.edu',
                'email' => 'admissions@harvard.edu',
                'phone' => '+1-617-495-1000',
                'address' => 'Massachusetts Hall, Cambridge, MA 02138, USA',
                'accreditations' => ['AACSB', 'ABA'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'ETH Zurich',
                'country' => 'Switzerland',
                'city' => 'Zurich',
                'type' => 'Public',
                'rating' => '4.7',
                'students' => 22000,
                'programs' => 100,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/ETH_Zurich_logo.svg/1200px-ETH_Zurich_logo.svg.png',
                'description' => 'Swiss Federal Institute of Technology, renowned for engineering and natural sciences.',
                'featured' => true,
                'sponsored' => false,
                'tuition' => 'CHF 1,290',
                'tuitionMin' => '1290',
                'tuitionMax' => '1290',
                'tuitionCurrency' => 'CHF',
                'acceptanceRate' => '27%',
                'worldRanking' => 6,
                'qsRanking' => 6,
                'timesRanking' => 8,
                'arwuRanking' => 20,
                'usNewsRanking' => 25,
                'popularPrograms' => ['Engineering', 'Computer Science', 'Physics'],
                'applicationDeadline' => new \DateTime('2025-04-30'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'universityType' => 'A',
                'commissionRate' => '5-10%',
                'freeApplications' => 2,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.ethz.ch',
                'email' => 'info@ethz.ch',
                'phone' => '+41-44-632-1111',
                'address' => 'Rämistrasse 101, 8092 Zürich, Switzerland',
                'accreditations' => ['ABET'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'University of Melbourne',
                'country' => 'Australia',
                'city' => 'Melbourne',
                'type' => 'Public',
                'rating' => '4.5',
                'students' => 50000,
                'programs' => 600,
                'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/8c/University_of_Melbourne_coat_of_arms.svg/1200px-University_of_Melbourne_coat_of_arms.svg.png',
                'description' => 'Australia\'s leading research university with a strong international reputation.',
                'featured' => false,
                'sponsored' => false,
                'tuition' => 'A$30,000 - A$45,000',
                'tuitionMin' => '30000',
                'tuitionMax' => '45000',
                'tuitionCurrency' => 'AUD',
                'acceptanceRate' => '70%',
                'worldRanking' => 33,
                'qsRanking' => 33,
                'timesRanking' => 37,
                'arwuRanking' => 35,
                'usNewsRanking' => 30,
                'popularPrograms' => ['Business', 'Medicine', 'Engineering'],
                'applicationDeadline' => new \DateTime('2025-01-31'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'universityType' => 'B',
                'commissionRate' => '8-12%',
                'freeApplications' => 2,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.unimelb.edu.au',
                'email' => 'admissions@unimelb.edu.au',
                'phone' => '+61-3-9035-5511',
                'address' => 'Parkville VIC 3010, Australia',
                'accreditations' => ['AACSB', 'EQUIS'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'Tsinghua University',
                'country' => 'China',
                'city' => 'Beijing',
                'type' => 'Public',
                'rating' => '4.6',
                'students' => 46000,
                'programs' => 300,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Tsinghua_University_Logo.svg/1200px-Tsinghua_University_Logo.svg.png',
                'description' => 'One of China\'s most prestigious universities, known for engineering and technology.',
                'featured' => false,
                'sponsored' => false,
                'tuition' => '¥26,000 - ¥40,000',
                'tuitionMin' => '26000',
                'tuitionMax' => '40000',
                'tuitionCurrency' => 'CNY',
                'acceptanceRate' => '15%',
                'worldRanking' => 14,
                'qsRanking' => 14,
                'timesRanking' => 16,
                'arwuRanking' => 29,
                'usNewsRanking' => 28,
                'popularPrograms' => ['Engineering', 'Computer Science', 'Business'],
                'applicationDeadline' => new \DateTime('2025-03-15'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'Chinese',
                'aidvisorRecommended' => false,
                'easyApply' => false,
                'universityType' => 'B',
                'commissionRate' => '10-15%',
                'freeApplications' => 1,
                'visaSupport' => 'paid',
                'countrySpecific' => ['type' => 'china', 'requirements' => ['HSK Test']],
                'website' => 'https://www.tsinghua.edu.cn',
                'email' => 'admissions@tsinghua.edu.cn',
                'phone' => '+86-10-6278-3000',
                'address' => 'Tsinghua University, Beijing 100084, China',
                'accreditations' => ['MOE'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => false
            ],
            [
                'name' => 'University of Tokyo',
                'country' => 'Japan',
                'city' => 'Tokyo',
                'type' => 'Public',
                'rating' => '4.7',
                'students' => 28000,
                'programs' => 200,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/University_of_Tokyo_logo.svg/1200px-University_of_Tokyo_logo.svg.png',
                'description' => 'Japan\'s most prestigious university, known for research excellence.',
                'featured' => true,
                'sponsored' => false,
                'tuition' => '¥535,800',
                'tuitionMin' => '535800',
                'tuitionMax' => '535800',
                'tuitionCurrency' => 'JPY',
                'acceptanceRate' => '20%',
                'worldRanking' => 23,
                'qsRanking' => 23,
                'timesRanking' => 35,
                'arwuRanking' => 24,
                'usNewsRanking' => 73,
                'popularPrograms' => ['Engineering', 'Medicine', 'Economics'],
                'applicationDeadline' => new \DateTime('2025-02-28'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'Japanese',
                'aidvisorRecommended' => false,
                'easyApply' => false,
                'universityType' => 'B',
                'commissionRate' => '8-12%',
                'freeApplications' => 1,
                'visaSupport' => 'paid',
                'countrySpecific' => ['type' => 'standard', 'requirements' => ['JLPT N1']],
                'website' => 'https://www.u-tokyo.ac.jp',
                'email' => 'admissions@u-tokyo.ac.jp',
                'phone' => '+81-3-3812-2111',
                'address' => '7-3-1 Hongo, Bunkyo, Tokyo 113-8654, Japan',
                'accreditations' => ['MEXT'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => false
            ],
            [
                'name' => 'McGill University',
                'country' => 'Canada',
                'city' => 'Montreal',
                'type' => 'Public',
                'rating' => '4.6',
                'students' => 40000,
                'programs' => 300,
                'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5a/McGill_University_CoA.svg/1200px-McGill_University_CoA.svg.png',
                'description' => 'One of Canada\'s best-known institutions of higher learning and one of the leading universities in the world.',
                'featured' => false,
                'sponsored' => false,
                'tuition' => '$2,500 - $8,500',
                'tuitionMin' => '2500',
                'tuitionMax' => '8500',
                'tuitionCurrency' => 'CAD',
                'acceptanceRate' => '46%',
                'worldRanking' => 31,
                'qsRanking' => 31,
                'timesRanking' => 44,
                'arwuRanking' => 67,
                'usNewsRanking' => 51,
                'popularPrograms' => ['Medicine', 'Law', 'Engineering'],
                'applicationDeadline' => new \DateTime('2025-01-15'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'universityType' => 'A',
                'commissionRate' => '5-10%',
                'freeApplications' => 2,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.mcgill.ca',
                'email' => 'admissions@mcgill.ca',
                'phone' => '+1-514-398-4455',
                'address' => '845 Sherbrooke St W, Montreal, QC H3A 0G4, Canada',
                'accreditations' => ['AACSB', 'EQUIS'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'École Polytechnique',
                'country' => 'France',
                'city' => 'Palaiseau',
                'type' => 'Public',
                'rating' => '4.8',
                'students' => 3000,
                'programs' => 50,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Logo_%C3%89cole_Polytechnique.svg/1200px-Logo_%C3%89cole_Polytechnique.svg.png',
                'description' => 'France\'s leading engineering school, known for excellence in science and technology.',
                'featured' => true,
                'sponsored' => false,
                'tuition' => '€3,770',
                'tuitionMin' => '3770',
                'tuitionMax' => '3770',
                'tuitionCurrency' => 'EUR',
                'acceptanceRate' => '12%',
                'worldRanking' => 38,
                'qsRanking' => 38,
                'timesRanking' => 40,
                'arwuRanking' => 301,
                'usNewsRanking' => 95,
                'popularPrograms' => ['Engineering', 'Mathematics', 'Physics'],
                'applicationDeadline' => new \DateTime('2025-03-01'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'French',
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'universityType' => 'A',
                'commissionRate' => '0%',
                'freeApplications' => 1,
                'visaSupport' => 'paid',
                'countrySpecific' => ['type' => 'france', 'requirements' => ['Campus France', 'Concours']],
                'website' => 'https://www.polytechnique.edu',
                'email' => 'admissions@polytechnique.edu',
                'phone' => '+33-1-69-33-33-33',
                'address' => 'Route de Saclay, 91128 Palaiseau, France',
                'accreditations' => ['CTI'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ],
            [
                'name' => 'Stanford University',
                'country' => 'United States',
                'city' => 'Stanford',
                'type' => 'Private',
                'rating' => '4.9',
                'students' => 17000,
                'programs' => 150,
                'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b7/Stanford_University_seal_2003.svg/1200px-Stanford_University_seal_2003.svg.png',
                'description' => 'One of the world\'s leading research universities, known for academic strength and proximity to Silicon Valley.',
                'featured' => true,
                'sponsored' => true,
                'tuition' => '$56,169',
                'tuitionMin' => '56169',
                'tuitionMax' => '56169',
                'tuitionCurrency' => 'USD',
                'acceptanceRate' => '4%',
                'worldRanking' => 2,
                'qsRanking' => 2,
                'timesRanking' => 3,
                'arwuRanking' => 2,
                'usNewsRanking' => 3,
                'popularPrograms' => ['Computer Science', 'Engineering', 'Business'],
                'applicationDeadline' => new \DateTime('2025-01-02'),
                'scholarships' => true,
                'housing' => true,
                'language' => 'English',
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'universityType' => 'A',
                'commissionRate' => '20-30%',
                'freeApplications' => 1,
                'visaSupport' => 'free',
                'countrySpecific' => ['type' => 'standard', 'requirements' => []],
                'website' => 'https://www.stanford.edu',
                'email' => 'admissions@stanford.edu',
                'phone' => '+1-650-723-2300',
                'address' => '450 Serra Mall, Stanford, CA 94305, USA',
                'accreditations' => ['AACSB', 'ABET'],
                'accommodation' => true,
                'careerServices' => true,
                'languageSupport' => true
            ]
        ];

        // Sample programs data
        $programsData = [
            [
                'name' => 'Master of Computer Science',
                'degree' => 'Master\'s',
                'duration' => '2 years',
                'language' => 'English',
                'tuition' => '$58,160',
                'tuitionAmount' => '58160',
                'tuitionCurrency' => 'USD',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-01-15'),
                'description' => 'Advanced program in computer science with focus on AI and machine learning.',
                'requirements' => ['Bachelor\'s in CS', 'IELTS 7.0', 'GRE 320+'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'ranking' => 18,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Computer Science',
                'studyLevel' => 'graduate',
                'rating' => '4.8',
                'reviews' => 150
            ],
            [
                'name' => 'Bachelor of Medicine',
                'degree' => 'Bachelor\'s',
                'duration' => '6 years',
                'language' => 'French',
                'tuition' => '€3,770',
                'tuitionAmount' => '3770',
                'tuitionCurrency' => 'EUR',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-03-01'),
                'description' => 'Comprehensive medical program with clinical rotations and research opportunities.',
                'requirements' => ['High School Diploma', 'DELF B2', 'Medical Entrance Exam'],
                'scholarships' => true,
                'featured' => false,
                'aidvisorRecommended' => false,
                'easyApply' => true,
                'ranking' => 83,
                'studyType' => 'on-campus',
                'universityType' => 'C',
                'subject' => 'Medicine',
                'studyLevel' => 'undergraduate',
                'rating' => '4.6',
                'reviews' => 89
            ],
            [
                'name' => 'Bachelor of Philosophy, Politics and Economics',
                'degree' => 'Bachelor\'s',
                'duration' => '3 years',
                'language' => 'English',
                'tuition' => '£39,010',
                'tuitionAmount' => '39010',
                'tuitionCurrency' => 'GBP',
                'startDate' => new \DateTime('2025-10-01'),
                'applicationDeadline' => new \DateTime('2025-01-15'),
                'description' => 'Interdisciplinary program combining philosophy, politics, and economics.',
                'requirements' => ['A-Levels AAA', 'IELTS 7.5', 'Personal Statement'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'ranking' => 1,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Philosophy',
                'studyLevel' => 'undergraduate',
                'rating' => '4.9',
                'reviews' => 203
            ],
            [
                'name' => 'Master of Business Administration',
                'degree' => 'Master\'s',
                'duration' => '2 years',
                'language' => 'English',
                'tuition' => '$54,269',
                'tuitionAmount' => '54269',
                'tuitionCurrency' => 'USD',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-01-01'),
                'description' => 'World-renowned MBA program with focus on leadership and innovation.',
                'requirements' => ['Bachelor\'s Degree', 'GMAT 730+', 'IELTS 7.5', 'Work Experience'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'ranking' => 3,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Business',
                'studyLevel' => 'graduate',
                'rating' => '4.9',
                'reviews' => 89
            ],
            [
                'name' => 'Master of Science in Computer Science',
                'degree' => 'Master\'s',
                'duration' => '2 years',
                'language' => 'English',
                'tuition' => 'CHF 1,290',
                'tuitionAmount' => '1290',
                'tuitionCurrency' => 'CHF',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-04-30'),
                'description' => 'Advanced computer science program with focus on AI and machine learning.',
                'requirements' => ['Bachelor\'s in CS', 'IELTS 7.0', 'GRE 320+'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'ranking' => 6,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Computer Science',
                'studyLevel' => 'graduate',
                'rating' => '4.7',
                'reviews' => 156
            ],
            [
                'name' => 'Bachelor of Medicine',
                'degree' => 'Bachelor\'s',
                'duration' => '6 years',
                'language' => 'English',
                'tuition' => 'A$45,000',
                'tuitionAmount' => '45000',
                'tuitionCurrency' => 'AUD',
                'startDate' => new \DateTime('2025-02-01'),
                'applicationDeadline' => new \DateTime('2025-01-31'),
                'description' => 'Comprehensive medical program with clinical training and research opportunities.',
                'requirements' => ['High School Diploma', 'IELTS 7.0', 'UMAT Test'],
                'scholarships' => true,
                'featured' => false,
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'ranking' => 33,
                'studyType' => 'on-campus',
                'universityType' => 'B',
                'subject' => 'Medicine',
                'studyLevel' => 'undergraduate',
                'rating' => '4.5',
                'reviews' => 134
            ],
            [
                'name' => 'Master of Engineering',
                'degree' => 'Master\'s',
                'duration' => '2 years',
                'language' => 'Chinese',
                'tuition' => '¥40,000',
                'tuitionAmount' => '40000',
                'tuitionCurrency' => 'CNY',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-03-15'),
                'description' => 'Advanced engineering program with focus on technology and innovation.',
                'requirements' => ['Bachelor\'s in Engineering', 'HSK 5', 'IELTS 6.5'],
                'scholarships' => true,
                'featured' => false,
                'aidvisorRecommended' => false,
                'easyApply' => false,
                'ranking' => 14,
                'studyType' => 'on-campus',
                'universityType' => 'B',
                'subject' => 'Engineering',
                'studyLevel' => 'graduate',
                'rating' => '4.6',
                'reviews' => 78
            ],
            [
                'name' => 'Bachelor of Economics',
                'degree' => 'Bachelor\'s',
                'duration' => '4 years',
                'language' => 'Japanese',
                'tuition' => '¥535,800',
                'tuitionAmount' => '535800',
                'tuitionCurrency' => 'JPY',
                'startDate' => new \DateTime('2025-04-01'),
                'applicationDeadline' => new \DateTime('2025-02-28'),
                'description' => 'Comprehensive economics program with focus on Japanese and global markets.',
                'requirements' => ['High School Diploma', 'JLPT N1', 'EJU Test'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => false,
                'easyApply' => false,
                'ranking' => 23,
                'studyType' => 'on-campus',
                'universityType' => 'B',
                'subject' => 'Economics',
                'studyLevel' => 'undergraduate',
                'rating' => '4.7',
                'reviews' => 92
            ],
            [
                'name' => 'Master of Law',
                'degree' => 'Master\'s',
                'duration' => '1 year',
                'language' => 'English',
                'tuition' => '$8,500',
                'tuitionAmount' => '8500',
                'tuitionCurrency' => 'CAD',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-01-15'),
                'description' => 'Advanced law program with focus on international and comparative law.',
                'requirements' => ['Bachelor\'s in Law', 'IELTS 7.0', 'LSAT 160+'],
                'scholarships' => true,
                'featured' => false,
                'aidvisorRecommended' => true,
                'easyApply' => true,
                'ranking' => 31,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Law',
                'studyLevel' => 'graduate',
                'rating' => '4.6',
                'reviews' => 67
            ],
            [
                'name' => 'Master of Science in Mathematics',
                'degree' => 'Master\'s',
                'duration' => '2 years',
                'language' => 'French',
                'tuition' => '€3,770',
                'tuitionAmount' => '3770',
                'tuitionCurrency' => 'EUR',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-03-01'),
                'description' => 'Advanced mathematics program with focus on pure and applied mathematics.',
                'requirements' => ['Bachelor\'s in Mathematics', 'DELF B2', 'GRE 320+'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'ranking' => 38,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Mathematics',
                'studyLevel' => 'graduate',
                'rating' => '4.8',
                'reviews' => 45
            ],
            [
                'name' => 'Master of Science in Computer Science',
                'degree' => 'Master\'s',
                'duration' => '2 years',
                'language' => 'English',
                'tuition' => '$56,169',
                'tuitionAmount' => '56169',
                'tuitionCurrency' => 'USD',
                'startDate' => new \DateTime('2025-09-01'),
                'applicationDeadline' => new \DateTime('2025-01-02'),
                'description' => 'Cutting-edge computer science program with focus on AI and machine learning.',
                'requirements' => ['Bachelor\'s in CS', 'IELTS 7.5', 'GRE 330+'],
                'scholarships' => true,
                'featured' => true,
                'aidvisorRecommended' => true,
                'easyApply' => false,
                'ranking' => 2,
                'studyType' => 'on-campus',
                'universityType' => 'A',
                'subject' => 'Computer Science',
                'studyLevel' => 'graduate',
                'rating' => '4.9',
                'reviews' => 234
            ]
        ];

        $io->progressStart(count($establishmentsData));

        $establishments = [];
        foreach ($establishmentsData as $index => $data) {
            $establishment = new Establishment();
            $establishment->setName($data['name']);
            $establishment->setCountry($data['country']);
            $establishment->setCity($data['city']);
            $establishment->setType($data['type']);
            $establishment->setRating($data['rating']);
            $establishment->setStudents($data['students']);
            $establishment->setPrograms($data['programs']);
            $establishment->setLogo($data['logo']);
            $establishment->setDescription($data['description']);
            $establishment->setFeatured($data['featured']);
            $establishment->setSponsored($data['sponsored']);
            $establishment->setTuition($data['tuition']);
            $establishment->setTuitionMin($data['tuitionMin']);
            $establishment->setTuitionMax($data['tuitionMax']);
            $establishment->setTuitionCurrency($data['tuitionCurrency']);
            $establishment->setAcceptanceRate($data['acceptanceRate']);
            $establishment->setWorldRanking($data['worldRanking']);
            $establishment->setQsRanking($data['qsRanking']);
            $establishment->setTimesRanking($data['timesRanking']);
            $establishment->setArwuRanking($data['arwuRanking']);
            $establishment->setUsNewsRanking($data['usNewsRanking']);
            $establishment->setPopularPrograms($data['popularPrograms']);
            $establishment->setApplicationDeadline($data['applicationDeadline']);
            $establishment->setScholarships($data['scholarships']);
            $establishment->setHousing($data['housing']);
            $establishment->setLanguage($data['language']);
            $establishment->setAidvisorRecommended($data['aidvisorRecommended']);
            $establishment->setEasyApply($data['easyApply']);
            $establishment->setUniversityType($data['universityType']);
            $establishment->setCommissionRate($data['commissionRate']);
            $establishment->setFreeApplications($data['freeApplications']);
            $establishment->setVisaSupport($data['visaSupport']);
            $establishment->setCountrySpecific($data['countrySpecific']);
            $establishment->setWebsite($data['website']);
            $establishment->setEmail($data['email']);
            $establishment->setPhone($data['phone']);
            $establishment->setAddress($data['address']);
            $establishment->setAccreditations($data['accreditations']);
            $establishment->setAccommodation($data['accommodation']);
            $establishment->setCareerServices($data['careerServices']);
            $establishment->setLanguageSupport($data['languageSupport']);

            $this->entityManager->persist($establishment);
            $establishments[] = $establishment;

            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();
        $io->success('Establishments created successfully!');

        $io->progressStart(count($programsData));

        foreach ($programsData as $index => $data) {
            $program = new Program();
            $program->setName($data['name']);
            $program->setEstablishment($establishments[$index]);
            $program->setCountry($establishments[$index]->getCountry());
            $program->setCity($establishments[$index]->getCity());
            $program->setDegree($data['degree']);
            $program->setDuration($data['duration']);
            $program->setLanguage($data['language']);
            $program->setTuition($data['tuition']);
            $program->setTuitionAmount($data['tuitionAmount']);
            $program->setTuitionCurrency($data['tuitionCurrency']);
            $program->setStartDate($data['startDate']);
            $program->setApplicationDeadline($data['applicationDeadline']);
            $program->setDescription($data['description']);
            $program->setRequirements($data['requirements']);
            $program->setScholarships($data['scholarships']);
            $program->setFeatured($data['featured']);
            $program->setAidvisorRecommended($data['aidvisorRecommended']);
            $program->setEasyApply($data['easyApply']);
            $program->setRanking($data['ranking']);
            $program->setStudyType($data['studyType']);
            $program->setUniversityType($data['universityType']);
            $program->setSubject($data['subject']);
            $program->setStudyLevel($data['studyLevel']);
            $program->setRating($data['rating']);
            $program->setReviews($data['reviews']);

            $this->entityManager->persist($program);

            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();
        $io->success('Programs created successfully!');

        $io->success('All data seeded successfully!');

        return Command::SUCCESS;
    }
}
