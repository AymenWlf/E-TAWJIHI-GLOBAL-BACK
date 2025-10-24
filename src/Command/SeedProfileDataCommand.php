<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\Qualification;
use App\Entity\Document;
use App\Entity\Application;
use App\Entity\Shortlist;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-profile-data',
    description: 'Seed profile data for aymenouallaf2000@gmail.com',
)]
class SeedProfileDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Find or create user
        $user = $this->userRepository->findOneBy(['email' => 'aymenouallaf2000@gmail.com']);

        if (!$user) {
            $io->error('User aymenouallaf2000@gmail.com not found. Please create the user first.');
            return Command::FAILURE;
        }

        // Check if profile already exists
        $profile = $user->getProfile();
        if ($profile) {
            $io->warning('Profile already exists for this user. Skipping...');
            return Command::SUCCESS;
        }

        // Create profile
        $profile = new UserProfile();
        $profile->setUser($user);
        $profile->setFirstName('Aymen');
        $profile->setLastName('Ouallaf');
        $profile->setCountry('Morocco');
        $profile->setCity('Casablanca');
        $profile->setNationality('Morocco');
        $profile->setPhone('+212 6 12 34 56 78');
        $profile->setDateOfBirth(new \DateTime('2000-05-15'));
        $profile->setStudyLevel('Postgraduate');
        $profile->setFieldOfStudy('Finance');
        $profile->setPreferredCountry('United Kingdom');
        $profile->setStartDate('September 2025');
        $profile->setPreferredCurrency('USD');
        $profile->setAnnualBudget(50000.00);
        $profile->setScholarshipRequired(true);
        $profile->setLanguagePreferences(['english', 'french']);
        $profile->setOnboardingProgress([
            'account_creation' => true,
            'email_verification' => true,
            'edvisor_test' => true,
            'search_shortlist' => true,
            'apply_choice' => false,
            'fill_information' => true,
            'degrees_qualifications' => true,
            'documents_preferences' => true
        ]);

        $this->entityManager->persist($profile);

        // Add qualifications
        $qualifications = [
            [
                'type' => 'academic',
                'title' => 'Bachelor\'s Degree in Finance',
                'institution' => 'University of Casablanca',
                'field' => 'Finance',
                'startDate' => '2018-09-01',
                'endDate' => '2022-06-30',
                'grade' => 'Distinction',
                'score' => '3.8',
                'scoreType' => 'GPA',
                'status' => 'valid'
            ],
            [
                'type' => 'academic',
                'title' => 'High School Diploma',
                'institution' => 'Lycée Hassan II',
                'field' => 'Science',
                'startDate' => '2017-09-01',
                'endDate' => '2018-06-30',
                'grade' => 'Excellent',
                'score' => '18.5',
                'scoreType' => 'Percentage',
                'status' => 'valid'
            ],
            [
                'type' => 'language',
                'title' => 'IELTS Academic',
                'institution' => 'British Council',
                'field' => 'English',
                'startDate' => '2024-03-15',
                'endDate' => '2024-03-15',
                'grade' => 'Band 7.5',
                'score' => '7.5',
                'scoreType' => 'Band',
                'expiryDate' => '2026-03-15',
                'status' => 'valid'
            ],
            [
                'type' => 'language',
                'title' => 'TOEFL iBT',
                'institution' => 'ETS',
                'field' => 'English',
                'startDate' => '2023-11-20',
                'endDate' => '2023-11-20',
                'grade' => 'Score 105',
                'score' => '105',
                'scoreType' => 'Score',
                'expiryDate' => '2025-11-20',
                'status' => 'expired'
            ],
            [
                'type' => 'professional',
                'title' => 'CFA Level I',
                'institution' => 'CFA Institute',
                'field' => 'Finance',
                'startDate' => '2023-06-01',
                'endDate' => '2023-06-01',
                'grade' => 'Passed',
                'score' => null,
                'scoreType' => 'Pass/Fail',
                'status' => 'valid'
            ]
        ];

        foreach ($qualifications as $qualData) {
            $qualification = new Qualification();
            $qualification->setUserProfile($profile);
            $qualification->setType($qualData['type']);
            $qualification->setTitle($qualData['title']);
            $qualification->setInstitution($qualData['institution']);
            $qualification->setField($qualData['field']);
            $qualification->setStartDate(new \DateTime($qualData['startDate']));
            $qualification->setEndDate(new \DateTime($qualData['endDate']));
            $qualification->setGrade($qualData['grade']);
            $qualification->setScore($qualData['score']);
            $qualification->setScoreType($qualData['scoreType']);
            $qualification->setStatus($qualData['status']);

            if (isset($qualData['expiryDate'])) {
                $qualification->setExpiryDate(new \DateTime($qualData['expiryDate']));
            }

            $this->entityManager->persist($qualification);
        }

        // Add documents
        $documents = [
            [
                'type' => 'academic',
                'title' => 'Bachelor\'s Degree Transcript',
                'filename' => 'bachelor_transcript_2022.pdf',
                'originalFilename' => 'Bachelor_Degree_Transcript.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 2048576,
                'status' => 'uploaded'
            ],
            [
                'type' => 'academic',
                'title' => 'High School Diploma',
                'filename' => 'high_school_diploma_2018.pdf',
                'originalFilename' => 'High_School_Diploma.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 1536000,
                'status' => 'uploaded'
            ],
            [
                'type' => 'academic',
                'title' => 'Master\'s Degree Transcript',
                'filename' => 'master_transcript_2024.pdf',
                'originalFilename' => 'Master_Degree_Transcript.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 1800000,
                'status' => 'not_required'
            ],
            [
                'type' => 'language',
                'title' => 'IELTS Certificate',
                'filename' => 'ielts_certificate_2024.pdf',
                'originalFilename' => 'IELTS_Certificate.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 1024000,
                'status' => 'uploaded'
            ],
            [
                'type' => 'language',
                'title' => 'TOEFL Certificate',
                'filename' => 'toefl_certificate_2023.pdf',
                'originalFilename' => 'TOEFL_Certificate.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 1200000,
                'status' => 'expired'
            ],
            [
                'type' => 'personal',
                'title' => 'Passport Copy',
                'filename' => 'passport_copy_2024.pdf',
                'originalFilename' => 'Passport_Copy.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 800000,
                'status' => 'uploaded'
            ],
            [
                'type' => 'personal',
                'title' => 'Personal Statement',
                'filename' => 'personal_statement_draft.docx',
                'originalFilename' => 'Personal_Statement_Draft.docx',
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'fileSize' => 45000,
                'status' => 'draft'
            ]
        ];

        foreach ($documents as $docData) {
            $document = new Document();
            $document->setUserProfile($profile);
            $document->setType($docData['type']);
            $document->setTitle($docData['title']);
            $document->setFilename($docData['filename']);
            $document->setOriginalFilename($docData['originalFilename']);
            $document->setMimeType($docData['mimeType']);
            $document->setFileSize($docData['fileSize']);
            $document->setStatus($docData['status']);

            $this->entityManager->persist($document);
        }

        // Add applications
        $applications = [
            [
                'universityName' => 'London School of Economics',
                'programName' => 'MSc Finance',
                'country' => 'United Kingdom',
                'status' => 'submitted',
                'applicationFee' => '100',
                'tuitionFee' => '35000',
                'applicationDeadline' => '2025-01-15',
                'startDate' => '2025-09-01',
                'notes' => 'Applied for scholarship. Waiting for response.'
            ],
            [
                'universityName' => 'University of Manchester',
                'programName' => 'MSc Financial Economics',
                'country' => 'United Kingdom',
                'status' => 'under_review',
                'applicationFee' => '80',
                'tuitionFee' => '28000',
                'applicationDeadline' => '2025-02-01',
                'startDate' => '2025-09-01',
                'notes' => 'Documents submitted. Under review.'
            ]
        ];

        foreach ($applications as $appData) {
            $application = new Application();
            $application->setUserProfile($profile);
            $application->setUniversityName($appData['universityName']);
            $application->setProgramName($appData['programName']);
            $application->setCountry($appData['country']);
            $application->setStatus($appData['status']);
            $application->setApplicationFee($appData['applicationFee']);
            $application->setTuitionFee($appData['tuitionFee']);
            $application->setApplicationDeadline(new \DateTime($appData['applicationDeadline']));
            $application->setStartDate(new \DateTime($appData['startDate']));
            $application->setNotes($appData['notes']);

            $this->entityManager->persist($application);
        }

        // Add shortlist
        $shortlist = [
            [
                'universityName' => 'Imperial College London',
                'programName' => 'MSc Finance and Accounting',
                'country' => 'United Kingdom',
                'field' => 'Finance',
                'level' => 'Postgraduate',
                'tuitionFee' => '40000',
                'currency' => 'GBP',
                'applicationDeadline' => '2025-03-01',
                'startDate' => '2025-09-01',
                'notes' => 'Top choice. Excellent reputation.',
                'priority' => 1
            ],
            [
                'universityName' => 'University of Edinburgh',
                'programName' => 'MSc Banking and Risk',
                'country' => 'United Kingdom',
                'field' => 'Finance',
                'level' => 'Postgraduate',
                'tuitionFee' => '32000',
                'currency' => 'GBP',
                'applicationDeadline' => '2025-02-15',
                'startDate' => '2025-09-01',
                'notes' => 'Good program. Lower cost.',
                'priority' => 2
            ],
            [
                'universityName' => 'University of Toronto',
                'programName' => 'Master of Finance',
                'country' => 'Canada',
                'field' => 'Finance',
                'level' => 'Postgraduate',
                'tuitionFee' => '45000',
                'currency' => 'CAD',
                'applicationDeadline' => '2025-01-31',
                'startDate' => '2025-09-01',
                'notes' => 'International option. Good for immigration.',
                'priority' => 3
            ]
        ];

        foreach ($shortlist as $itemData) {
            $shortlistItem = new Shortlist();
            $shortlistItem->setUserProfile($profile);
            $shortlistItem->setUniversityName($itemData['universityName']);
            $shortlistItem->setProgramName($itemData['programName']);
            $shortlistItem->setCountry($itemData['country']);
            $shortlistItem->setField($itemData['field']);
            $shortlistItem->setLevel($itemData['level']);
            $shortlistItem->setTuitionFee($itemData['tuitionFee']);
            $shortlistItem->setCurrency($itemData['currency']);
            $shortlistItem->setApplicationDeadline(new \DateTime($itemData['applicationDeadline']));
            $shortlistItem->setStartDate(new \DateTime($itemData['startDate']));
            $shortlistItem->setNotes($itemData['notes']);
            $shortlistItem->setPriority($itemData['priority']);

            $this->entityManager->persist($shortlistItem);
        }

        $this->entityManager->flush();

        $io->success('Profile data seeded successfully for aymenouallaf2000@gmail.com');
        $io->note('Created:');
        $io->listing([
            '1 User Profile',
            '5 Qualifications',
            '7 Documents',
            '2 Applications',
            '3 Shortlist items'
        ]);

        return Command::SUCCESS;
    }
}
