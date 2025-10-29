<?php

namespace App\Command;

use App\Entity\FinalStep;
use App\Entity\FinalStepDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-final-steps',
    description: 'Initialize default final steps data',
)]
class InitFinalStepsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Clear existing data
        $this->entityManager->createQuery('DELETE FROM App\Entity\FinalStepDocument')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\UserFinalStepStatus')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\FinalStep')->execute();

        // Create final steps
        $steps = [
            [
                'nameEn' => 'Pre-admission',
                'nameFr' => 'Préadmission',
                'descriptionEn' => 'Initial review of your application and documents.',
                'descriptionFr' => 'Examen initial de votre candidature et de vos documents.',
                'order' => 1,
                'documents' => [
                    [
                        'titleEn' => 'Application Checklist',
                        'titleFr' => 'Liste de Vérification de Candidature',
                        'filePath' => '/uploads/final-steps/pre-admission-checklist.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 1024000
                    ]
                ]
            ],
            [
                'nameEn' => 'Enrollment Confirmation',
                'nameFr' => 'Confirmation d\'Inscription',
                'descriptionEn' => 'Confirmation of your enrollment in the program.',
                'descriptionFr' => 'Confirmation de votre inscription au programme.',
                'order' => 2,
                'documents' => [
                    [
                        'titleEn' => 'Enrollment Letter',
                        'titleFr' => 'Lettre d\'Inscription',
                        'filePath' => '/uploads/final-steps/enrollment-letter.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 512000
                    ]
                ]
            ],
            [
                'nameEn' => 'Final Admission',
                'nameFr' => 'Admission Finale',
                'descriptionEn' => 'Final admission decision and acceptance letter.',
                'descriptionFr' => 'Décision d\'admission finale et lettre d\'acceptation.',
                'order' => 3,
                'documents' => [
                    [
                        'titleEn' => 'Admission Letter',
                        'titleFr' => 'Lettre d\'Admission',
                        'filePath' => '/uploads/final-steps/admission-letter.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 768000
                    ],
                    [
                        'titleEn' => 'Program Details',
                        'titleFr' => 'Détails du Programme',
                        'filePath' => '/uploads/final-steps/program-details.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 1536000
                    ]
                ]
            ],
            [
                'nameEn' => 'Visa Process',
                'nameFr' => 'Processus de Visa',
                'descriptionEn' => 'Visa application and processing guidance.',
                'descriptionFr' => 'Guidance pour la demande et le traitement du visa.',
                'order' => 4,
                'documents' => [
                    [
                        'titleEn' => 'Visa Application Guide',
                        'titleFr' => 'Guide de Demande de Visa',
                        'filePath' => '/uploads/final-steps/visa-guide.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 2048000
                    ],
                    [
                        'titleEn' => 'Required Documents List',
                        'titleFr' => 'Liste des Documents Requis',
                        'filePath' => '/uploads/final-steps/visa-documents.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 512000
                    ]
                ]
            ],
            [
                'nameEn' => 'Travel & Accommodation',
                'nameFr' => 'Voyage et Logement',
                'descriptionEn' => 'Travel arrangements and accommodation information.',
                'descriptionFr' => 'Arrangements de voyage et informations sur le logement.',
                'order' => 5,
                'documents' => [
                    [
                        'titleEn' => 'Travel Guide',
                        'titleFr' => 'Guide de Voyage',
                        'filePath' => '/uploads/final-steps/travel-guide.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 2560000
                    ],
                    [
                        'titleEn' => 'Accommodation Options',
                        'titleFr' => 'Options de Logement',
                        'filePath' => '/uploads/final-steps/accommodation.pdf',
                        'fileType' => 'application/pdf',
                        'fileSize' => 1280000
                    ]
                ]
            ]
        ];

        foreach ($steps as $stepData) {
            $step = new FinalStep();
            $step->setName($stepData['nameEn']);
            $step->setNameEn($stepData['nameEn']);
            $step->setNameFr($stepData['nameFr']);
            $step->setDescription($stepData['descriptionEn']);
            $step->setDescriptionEn($stepData['descriptionEn']);
            $step->setDescriptionFr($stepData['descriptionFr']);
            $step->setStepOrder($stepData['order']);
            $step->setIsActive(true);

            $this->entityManager->persist($step);

            // Add documents for this step
            foreach ($stepData['documents'] as $docData) {
                $document = new FinalStepDocument();
                $document->setFinalStep($step);
                $document->setTitle($docData['titleEn']);
                $document->setTitleEn($docData['titleEn']);
                $document->setTitleFr($docData['titleFr']);
                $document->setFilePath($docData['filePath']);
                $document->setFileType($docData['fileType']);
                $document->setFileSize($docData['fileSize']);
                $document->setIsActive(true);

                $this->entityManager->persist($document);
            }
        }

        $this->entityManager->flush();

        $io->success('Final steps initialized successfully!');
        return Command::SUCCESS;
    }
}
