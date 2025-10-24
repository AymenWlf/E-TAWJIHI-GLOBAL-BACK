<?php

namespace App\Command;

use App\Repository\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-admission-requirements',
    description: 'Populates admission requirements for École Polytechnique and other establishments.',
)]
class PopulateAdmissionRequirementsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EstablishmentRepository $establishmentRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Populating Admission Requirements');

        // École Polytechnique admission requirements
        $polytechniqueData = [
            'name' => 'École Polytechnique',
            'admissionRequirements' => [
                'academic' => [
                    'High school diploma with excellent grades',
                    'Strong background in mathematics and sciences',
                    'Minimum GPA of 3.5/4.0 or equivalent',
                    'Advanced mathematics and physics courses'
                ],
                'english' => [
                    'IELTS 6.5 or higher (all sections)',
                    'TOEFL iBT 90 or higher',
                    'Cambridge English: Advanced (CAE) Grade C or higher',
                    'Duolingo English Test 120 or higher'
                ],
                'documents' => [
                    'Official high school transcripts',
                    'Personal statement (motivation letter)',
                    'Two letters of recommendation',
                    'CV/Resume',
                    'Passport copy',
                    'Application fee payment proof'
                ],
                'visa' => [
                    'Valid passport (minimum 6 months validity)',
                    'Study permit application',
                    'Financial proof (bank statements)',
                    'Medical examination certificate',
                    'Criminal background check',
                    'Proof of accommodation in France'
                ]
            ],
            'admissionRequirementsFr' => [
                'academic' => [
                    'Diplôme de fin d\'études secondaires avec d\'excellentes notes',
                    'Solide formation en mathématiques et sciences',
                    'GPA minimum de 3,5/4,0 ou équivalent',
                    'Cours avancés de mathématiques et physique'
                ],
                'english' => [
                    'IELTS 6,5 ou plus (toutes les sections)',
                    'TOEFL iBT 90 ou plus',
                    'Cambridge English: Advanced (CAE) Grade C ou plus',
                    'Test d\'anglais Duolingo 120 ou plus'
                ],
                'documents' => [
                    'Relevés de notes officiels du lycée',
                    'Lettre de motivation',
                    'Deux lettres de recommandation',
                    'CV/Curriculum vitae',
                    'Copie du passeport',
                    'Preuve de paiement des frais de candidature'
                ],
                'visa' => [
                    'Passeport valide (validité minimum 6 mois)',
                    'Demande de permis d\'études',
                    'Preuve financière (relevés bancaires)',
                    'Certificat d\'examen médical',
                    'Vérification des antécédents criminels',
                    'Preuve d\'hébergement en France'
                ]
            ],
            'englishTestRequirements' => [
                'IELTS' => [
                    'minimum_score' => 6.5,
                    'description' => 'International English Language Testing System',
                    'validity' => '2 years'
                ],
                'TOEFL' => [
                    'minimum_score' => 90,
                    'description' => 'Test of English as a Foreign Language',
                    'validity' => '2 years'
                ],
                'Cambridge' => [
                    'minimum_score' => 'Grade C',
                    'description' => 'Cambridge English: Advanced (CAE)',
                    'validity' => 'No expiry'
                ],
                'Duolingo' => [
                    'minimum_score' => 120,
                    'description' => 'Duolingo English Test',
                    'validity' => '2 years'
                ]
            ],
            'academicRequirements' => [
                'minimum_gpa' => 3.5,
                'required_subjects' => [
                    'Mathematics (Advanced)',
                    'Physics (Advanced)',
                    'Chemistry (Recommended)',
                    'English (Proficiency)'
                ],
                'additional_requirements' => [
                    'Strong analytical and problem-solving skills',
                    'Research experience (preferred)',
                    'Extracurricular activities in STEM fields'
                ]
            ],
            'documentRequirements' => [
                'mandatory' => [
                    'Official transcripts',
                    'Personal statement',
                    'Letters of recommendation',
                    'Passport copy',
                    'Application fee proof'
                ],
                'optional' => [
                    'Portfolio (for design programs)',
                    'Research papers',
                    'Certificates of achievement',
                    'Work experience letters'
                ]
            ],
            'visaRequirements' => [
                'documents' => [
                    'Valid passport',
                    'Study permit application',
                    'Financial proof',
                    'Medical certificate',
                    'Criminal background check'
                ],
                'financial_requirements' => [
                    'minimum_amount' => 12000,
                    'currency' => 'EUR',
                    'duration' => 'per year',
                    'description' => 'Proof of sufficient funds for living expenses'
                ],
                'processing_time' => '4-8 weeks',
                'additional_info' => 'Visa support available through university'
            ]
        ];

        $establishment = $this->establishmentRepository->findOneBy(['name' => 'École Polytechnique']);
        if ($establishment) {
            $establishment->setAdmissionRequirements($polytechniqueData['admissionRequirements']);
            $establishment->setAdmissionRequirementsFr($polytechniqueData['admissionRequirementsFr']);
            $establishment->setEnglishTestRequirements($polytechniqueData['englishTestRequirements']);
            $establishment->setAcademicRequirements($polytechniqueData['academicRequirements']);
            $establishment->setDocumentRequirements($polytechniqueData['documentRequirements']);
            $establishment->setVisaRequirements($polytechniqueData['visaRequirements']);

            $this->entityManager->persist($establishment);
            $io->success('Updated École Polytechnique admission requirements');
        } else {
            $io->warning('École Polytechnique not found');
        }

        // Add some data for other major universities
        $otherUniversities = [
            'Harvard University' => [
                'admissionRequirements' => [
                    'academic' => [
                        'High school diploma with outstanding grades',
                        'SAT score 1500+ or ACT 34+',
                        'Strong extracurricular involvement',
                        'Leadership experience'
                    ],
                    'english' => [
                        'IELTS 7.0 or higher',
                        'TOEFL iBT 100 or higher',
                        'Cambridge English: Proficiency (CPE)',
                        'Duolingo English Test 130 or higher'
                    ],
                    'documents' => [
                        'Official transcripts',
                        'Personal essay',
                        'Letters of recommendation',
                        'SAT/ACT scores',
                        'Application fee'
                    ]
                ],
                'englishTestRequirements' => [
                    'IELTS' => ['minimum_score' => 7.0, 'description' => 'International English Language Testing System'],
                    'TOEFL' => ['minimum_score' => 100, 'description' => 'Test of English as a Foreign Language'],
                    'Cambridge' => ['minimum_score' => 'Grade B', 'description' => 'Cambridge English: Proficiency (CPE)'],
                    'Duolingo' => ['minimum_score' => 130, 'description' => 'Duolingo English Test']
                ]
            ],
            'Stanford University' => [
                'admissionRequirements' => [
                    'academic' => [
                        'High school diploma with excellent grades',
                        'SAT score 1450+ or ACT 32+',
                        'Strong academic rigor',
                        'Innovation and creativity'
                    ],
                    'english' => [
                        'IELTS 7.0 or higher',
                        'TOEFL iBT 100 or higher',
                        'Cambridge English: Advanced (CAE)',
                        'Duolingo English Test 130 or higher'
                    ],
                    'documents' => [
                        'Official transcripts',
                        'Personal statement',
                        'Letters of recommendation',
                        'SAT/ACT scores',
                        'Application fee'
                    ]
                ],
                'englishTestRequirements' => [
                    'IELTS' => ['minimum_score' => 7.0, 'description' => 'International English Language Testing System'],
                    'TOEFL' => ['minimum_score' => 100, 'description' => 'Test of English as a Foreign Language'],
                    'Cambridge' => ['minimum_score' => 'Grade B', 'description' => 'Cambridge English: Advanced (CAE)'],
                    'Duolingo' => ['minimum_score' => 130, 'description' => 'Duolingo English Test']
                ]
            ]
        ];

        foreach ($otherUniversities as $universityName => $data) {
            $establishment = $this->establishmentRepository->findOneBy(['name' => $universityName]);
            if ($establishment) {
                $establishment->setAdmissionRequirements($data['admissionRequirements']);
                $establishment->setEnglishTestRequirements($data['englishTestRequirements']);
                $this->entityManager->persist($establishment);
                $io->success("Updated {$universityName} admission requirements");
            } else {
                $io->warning("{$universityName} not found");
            }
        }

        $this->entityManager->flush();

        $io->success('Successfully populated admission requirements for all establishments');

        return Command::SUCCESS;
    }
}
