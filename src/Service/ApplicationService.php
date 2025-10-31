<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\ApplicationStep;
use App\Entity\ApplicationDocument;
use App\Entity\AgentAssignment;
use App\Entity\User;
use App\Entity\Program;
use App\Repository\ApplicationRepository;
use App\Repository\ApplicationStepRepository;
use App\Repository\ApplicationDocumentRepository;
use App\Repository\AgentAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ApplicationService
{
    private const APPLICATION_STEPS = [
        1 => [
            'name' => 'personal_info',
            'title' => 'Personal Information',
            'titleFr' => 'Informations Personnelles',
            'description' => 'Complete your personal details',
            'descriptionFr' => 'Complétez vos informations personnelles',
            'required_documents' => []
        ],
        2 => [
            'name' => 'agent_selection',
            'title' => 'Agent Selection',
            'titleFr' => 'Sélection d\'Agent',
            'description' => 'Choose your education agent or get assigned one',
            'descriptionFr' => 'Choisissez votre agent éducatif ou obtenez-en un',
            'required_documents' => []
        ],
        3 => [
            'name' => 'academic_documents',
            'title' => 'Academic Documents',
            'titleFr' => 'Documents Académiques',
            'description' => 'Upload your academic certificates and transcripts',
            'descriptionFr' => 'Téléchargez vos certificats et relevés académiques',
            'required_documents' => ['diploma', 'transcript']
        ],
        4 => [
            'name' => 'personal_documents',
            'title' => 'Personal Documents',
            'titleFr' => 'Documents Personnels',
            'description' => 'Upload passport, photos, and other personal documents',
            'descriptionFr' => 'Téléchargez passeport, photos et autres documents personnels',
            'required_documents' => ['passport', 'photo']
        ],
        5 => [
            'name' => 'motivation_letter',
            'title' => 'Motivation Letter',
            'titleFr' => 'Lettre de Motivation',
            'description' => 'Write your motivation letter',
            'descriptionFr' => 'Rédigez votre lettre de motivation',
            'required_documents' => ['motivation_letter']
        ],
        6 => [
            'name' => 'references',
            'title' => 'References',
            'titleFr' => 'Références',
            'description' => 'Provide academic and professional references',
            'descriptionFr' => 'Fournissez des références académiques et professionnelles',
            'required_documents' => ['recommendation_letter']
        ],
        7 => [
            'name' => 'review_submission',
            'title' => 'Review & Submit',
            'titleFr' => 'Révision et Soumission',
            'description' => 'Review your application and submit',
            'descriptionFr' => 'Révisez votre candidature et soumettez',
            'required_documents' => []
        ]
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ApplicationRepository $applicationRepository,
        private ApplicationStepRepository $applicationStepRepository,
        private ApplicationDocumentRepository $applicationDocumentRepository,
        private AgentAssignmentRepository $agentAssignmentRepository,
        private SluggerInterface $slugger
    ) {}

    public function createApplication(User $user, Program $program): Application
    {
        // Check if user already has an application for this program
        $existingApplication = $this->applicationRepository->findUserApplicationForProgram($user, $program);
        if ($existingApplication) {
            throw new \Exception('You already have an application for this program');
        }

        $application = new Application();
        $application->setUser($user);
        $application->setProgram($program);
        $application->setStatus('draft');
        $application->setCurrentStep(1);
        $application->setProgressPercentage('0.00');

        // Set is_china based on program's establishment country
        $isChina = $this->isChinaApplication($program);
        $application->setIsChina($isChina);

        // Set is_france based on program's establishment country
        $isFrance = $this->isFranceApplication($program);
        $application->setIsFrance($isFrance);

        $this->entityManager->persist($application);

        // Create application steps
        $this->createApplicationSteps($application);

        $this->entityManager->flush();

        return $application;
    }

    private function createApplicationSteps(Application $application): void
    {
        foreach (self::APPLICATION_STEPS as $stepNumber => $stepConfig) {
            $step = new ApplicationStep();
            $step->setApplication($application);
            $step->setStepNumber($stepNumber);
            $step->setStepName($stepConfig['name']);
            $step->setStepTitle($stepConfig['title']);
            $step->setDescription($stepConfig['description']);
            $step->setRequiredDocuments($stepConfig['required_documents']);
            $step->setIsCompleted(false);

            $this->entityManager->persist($step);
        }
    }

    public function updateApplicationStep(Application $application, int $stepNumber, array $stepData): ApplicationStep
    {
        $step = $this->applicationStepRepository->findStepByNumber($application, $stepNumber);
        if (!$step) {
            throw new \Exception('Application step not found');
        }

        $step->setStepData($stepData);
        $step->setUpdatedAt(new \DateTimeImmutable());

        // Validate step completion
        $this->validateStepCompletion($step);

        $this->entityManager->flush();

        // Update application progress
        $this->updateApplicationProgress($application);

        return $step;
    }

    private function validateStepCompletion(ApplicationStep $step): void
    {
        $stepData = $step->getStepData();
        $requiredDocuments = $step->getRequiredDocuments();
        $isCompleted = true;
        $validationErrors = [];

        // Check if required documents are uploaded
        foreach ($requiredDocuments as $documentType) {
            $document = $this->applicationDocumentRepository->findByApplicationAndType(
                $step->getApplication(),
                $documentType
            );

            if (!$document || !$document->isApproved()) {
                $isCompleted = false;
                $validationErrors[] = "Required document {$documentType} is missing or not approved";
            }
        }

        // Step-specific validation
        switch ($step->getStepName()) {
            case 'personal_info':
                $requiredFields = ['firstName', 'lastName', 'email', 'phone', 'dateOfBirth'];
                foreach ($requiredFields as $field) {
                    if (empty($stepData[$field] ?? null)) {
                        $isCompleted = false;
                        $validationErrors[] = "Required field {$field} is missing";
                    }
                }
                break;

            case 'agent_selection':
                if (empty($stepData['agentId'] ?? null) && empty($stepData['agentCode'] ?? null)) {
                    $isCompleted = false;
                    $validationErrors[] = "Agent selection is required";
                }
                break;

            case 'motivation_letter':
                if (empty($stepData['content'] ?? null) || strlen($stepData['content']) < 100) {
                    $isCompleted = false;
                    $validationErrors[] = "Motivation letter must be at least 100 characters";
                }
                break;

            case 'references':
                if (empty($stepData['references'] ?? null) || count($stepData['references']) < 2) {
                    $isCompleted = false;
                    $validationErrors[] = "At least 2 references are required";
                }
                break;
        }

        $step->setIsCompleted($isCompleted);
        $step->setValidationErrors($validationErrors);

        if ($isCompleted) {
            $step->markAsCompleted();
        }
    }

    private function updateApplicationProgress(Application $application): void
    {
        $progress = $this->applicationStepRepository->calculateProgress($application);
        $application->setProgressPercentage((string) $progress);

        // Update current step
        $nextStep = $this->applicationStepRepository->getNextStep($application);
        if ($nextStep) {
            $application->setCurrentStep($nextStep->getStepNumber());
        } else {
            // All steps completed
            $application->setCurrentStep(count(self::APPLICATION_STEPS));
        }

        $application->setUpdatedAt(new \DateTimeImmutable());
    }

    public function assignAgent(Application $application, ?User $agent = null, ?string $agentCode = null): AgentAssignment
    {
        if ($agentCode) {
            $existingAssignment = $this->agentAssignmentRepository->findByAgentCode($agentCode);
            if ($existingAssignment) {
                $agent = $existingAssignment->getAgent();
            }
        }

        if (!$agent) {
            $agent = $this->agentAssignmentRepository->findBestAvailableAgent();
            if (!$agent) {
                throw new \Exception('No available agents found');
            }
        }

        $assignment = new AgentAssignment();
        $assignment->setStudent($application->getUser());
        $assignment->setAgent($agent);
        $assignment->setApplication($application);
        $assignment->setAgentCode($agentCode);
        $assignment->setStatus(AgentAssignment::STATUS_ACTIVE);

        $this->entityManager->persist($assignment);

        // Update application with agent
        $application->setAgentId($agent->getId());
        $application->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $assignment;
    }

    public function uploadDocument(Application $application, string $documentType, array $fileData): ApplicationDocument
    {
        $existingDocument = $this->applicationDocumentRepository->findByApplicationAndType($application, $documentType);

        if ($existingDocument) {
            // Update existing document
            $document = $existingDocument;
        } else {
            // Create new document
            $document = new ApplicationDocument();
            $document->setApplication($application);
            $document->setDocumentType($documentType);
        }

        $document->setFileName($fileData['fileName']);
        $document->setFilePath($fileData['filePath']);
        $document->setMimeType($fileData['mimeType']);
        $document->setFileSize($fileData['fileSize']);
        $document->setStatus(ApplicationDocument::STATUS_PENDING);
        $document->setUploadedAt(new \DateTime());
        $document->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($document);
        $this->entityManager->flush();

        // Update step validation
        $this->updateStepValidationForDocument($application, $documentType);

        return $document;
    }

    private function updateStepValidationForDocument(Application $application, string $documentType): void
    {
        $steps = $this->applicationStepRepository->findByApplication($application);

        foreach ($steps as $step) {
            $requiredDocuments = $step->getRequiredDocuments();
            if (in_array($documentType, $requiredDocuments)) {
                $this->validateStepCompletion($step);
            }
        }

        $this->entityManager->flush();
        $this->updateApplicationProgress($application);
    }

    public function submitApplication(Application $application): Application
    {
        if (!$application->canBeSubmitted()) {
            throw new \Exception('Application cannot be submitted. Please complete all required steps.');
        }

        $application->setStatus('submitted');
        $application->setSubmittedAt(new \DateTimeImmutable());
        $application->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $application;
    }

    public function getApplicationProgress(Application $application): array
    {
        $steps = $this->applicationStepRepository->findByApplication($application);
        $documents = $this->applicationDocumentRepository->findByApplication($application);
        $assignment = $this->agentAssignmentRepository->findByApplication($application);

        return [
            'application' => $application,
            'steps' => $steps,
            'documents' => $documents,
            'agent_assignment' => $assignment,
            'progress_percentage' => $application->getProgressPercentage(),
            'current_step' => $application->getCurrentStep(),
            'can_submit' => $application->canBeSubmitted()
        ];
    }

    public function getStepConfiguration(int $stepNumber): ?array
    {
        return self::APPLICATION_STEPS[$stepNumber] ?? null;
    }

    public function getAllStepConfigurations(): array
    {
        return self::APPLICATION_STEPS;
    }

    /**
     * Check if the application is for China based on program's establishment country
     */
    private function isChinaApplication(Program $program): bool
    {
        $establishment = $program->getEstablishment();
        if (!$establishment) {
            return false;
        }

        $country = $establishment->getCountry();
        if (!$country) {
            return false;
        }

        // Check if country is China (case insensitive)
        return strtolower(trim($country)) === 'china' ||
            strtolower(trim($country)) === 'chinese' ||
            strtolower(trim($country)) === 'cn';
    }

    /**
     * Check if the application is for France based on program's establishment country
     */
    private function isFranceApplication(Program $program): bool
    {
        $establishment = $program->getEstablishment();
        if (!$establishment) {
            return false;
        }

        $country = $establishment->getCountry();
        if (!$country) {
            return false;
        }

        // Check if country is France (case insensitive)
        return strtolower(trim($country)) === 'france' ||
            strtolower(trim($country)) === 'french' ||
            strtolower(trim($country)) === 'fr';
    }

    /**
     * Update China-specific fields for an application
     */
    public function updateChinaFields(Application $application, array $chinaData): Application
    {
        if (!$application->getIsChina()) {
            throw new \Exception('This application is not for China');
        }

        if (isset($chinaData['passportNumber'])) {
            $application->setPassportNumber($chinaData['passportNumber']);
        }

        if (isset($chinaData['passportIssueDate'])) {
            $application->setPassportIssueDate($chinaData['passportIssueDate']);
        }

        if (isset($chinaData['passportExpirationDate'])) {
            $application->setPassportExpirationDate($chinaData['passportExpirationDate']);
        }

        if (isset($chinaData['religion'])) {
            $application->setReligion($chinaData['religion']);
        }

        if (isset($chinaData['familyMembers'])) {
            $application->setFamilyMembers($chinaData['familyMembers']);
        }


        $application->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $application;
    }
}
