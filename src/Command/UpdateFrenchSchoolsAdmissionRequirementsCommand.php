<?php

namespace App\Command;

use App\Entity\Establishment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-french-schools-admission-requirements',
    description: 'Update admission requirements for French business schools',
)]
class UpdateFrenchSchoolsAdmissionRequirementsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Updating French Schools Admission Requirements');

        // Define admission requirements for each school
        $schoolsData = [
            'EM Lyon' => [
                'en' => [
                    'academic' => [
                        'High school diploma with excellent grades',
                        'Strong background in business and management',
                        'Minimum GPA of 3.0/4.0 or equivalent',
                        'Bachelor\'s degree or equivalent for Master programs'
                    ],
                    'english' => [
                        'IELTS 6.5 or higher (all sections)',
                        'TOEFL iBT 90 or higher',
                        'Cambridge English: Advanced (CAE) Grade C or higher',
                        'Duolingo English Test 120 or higher'
                    ],
                    'french' => [
                        'DELF B2 or higher',
                        'TCF B2 or higher',
                        'DALF C1 or higher (preferred)'
                    ],
                    'documents' => [
                        'Official academic transcripts',
                        'Personal statement (motivation letter)',
                        'Two letters of recommendation',
                        'CV/Resume',
                        'Passport copy',
                        'Language certificates',
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
                'fr' => [
                    'academic' => [
                        'Diplôme de fin d\'études secondaires avec d\'excellentes notes',
                        'Solide formation en commerce et gestion',
                        'GPA minimum de 3,0/4,0 ou équivalent',
                        'Licence ou équivalent pour les programmes Master'
                    ],
                    'english' => [
                        'IELTS 6,5 ou plus (toutes les sections)',
                        'TOEFL iBT 90 ou plus',
                        'Cambridge English: Advanced (CAE) Grade C ou plus',
                        'Test d\'anglais Duolingo 120 ou plus'
                    ],
                    'french' => [
                        'DELF B2 ou plus',
                        'TCF B2 ou plus',
                        'DALF C1 ou plus (préféré)'
                    ],
                    'documents' => [
                        'Relevés de notes officiels',
                        'Lettre de motivation',
                        'Deux lettres de recommandation',
                        'CV/Curriculum vitae',
                        'Copie du passeport',
                        'Certificats de langue',
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
                ]
            ],
            'SKEMA Business School' => [
                'en' => [
                    'academic' => [
                        'High school diploma with good grades',
                        'Background in business, economics, or related fields',
                        'Minimum GPA of 2.8/4.0 or equivalent',
                        'Bachelor\'s degree or equivalent for Master programs'
                    ],
                    'english' => [
                        'IELTS 6.0 or higher (all sections)',
                        'TOEFL iBT 85 or higher',
                        'Cambridge English: Advanced (CAE) Grade C or higher',
                        'Duolingo English Test 110 or higher'
                    ],
                    'french' => [
                        'DELF B1 or higher',
                        'TCF B1 or higher',
                        'DALF B2 or higher (preferred)'
                    ],
                    'documents' => [
                        'Official academic transcripts',
                        'Personal statement (motivation letter)',
                        'Two letters of recommendation',
                        'CV/Resume',
                        'Passport copy',
                        'Language certificates',
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
                'fr' => [
                    'academic' => [
                        'Diplôme de fin d\'études secondaires avec de bonnes notes',
                        'Formation en commerce, économie ou domaines connexes',
                        'GPA minimum de 2,8/4,0 ou équivalent',
                        'Licence ou équivalent pour les programmes Master'
                    ],
                    'english' => [
                        'IELTS 6,0 ou plus (toutes les sections)',
                        'TOEFL iBT 85 ou plus',
                        'Cambridge English: Advanced (CAE) Grade C ou plus',
                        'Test d\'anglais Duolingo 110 ou plus'
                    ],
                    'french' => [
                        'DELF B1 ou plus',
                        'TCF B1 ou plus',
                        'DALF B2 ou plus (préféré)'
                    ],
                    'documents' => [
                        'Relevés de notes officiels',
                        'Lettre de motivation',
                        'Deux lettres de recommandation',
                        'CV/Curriculum vitae',
                        'Copie du passeport',
                        'Certificats de langue',
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
                ]
            ],
            'NEOMA Business School' => [
                'en' => [
                    'academic' => [
                        'High school diploma with good grades',
                        'Background in business, economics, or related fields',
                        'Minimum GPA of 2.7/4.0 or equivalent',
                        'Bachelor\'s degree or equivalent for Master programs'
                    ],
                    'english' => [
                        'IELTS 6.0 or higher (all sections)',
                        'TOEFL iBT 80 or higher',
                        'Cambridge English: Advanced (CAE) Grade C or higher',
                        'Duolingo English Test 105 or higher'
                    ],
                    'french' => [
                        'DELF B1 or higher',
                        'TCF B1 or higher',
                        'DALF B2 or higher (preferred)'
                    ],
                    'documents' => [
                        'Official academic transcripts',
                        'Personal statement (motivation letter)',
                        'Two letters of recommendation',
                        'CV/Resume',
                        'Passport copy',
                        'Language certificates',
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
                'fr' => [
                    'academic' => [
                        'Diplôme de fin d\'études secondaires avec de bonnes notes',
                        'Formation en commerce, économie ou domaines connexes',
                        'GPA minimum de 2,7/4,0 ou équivalent',
                        'Licence ou équivalent pour les programmes Master'
                    ],
                    'english' => [
                        'IELTS 6,0 ou plus (toutes les sections)',
                        'TOEFL iBT 80 ou plus',
                        'Cambridge English: Advanced (CAE) Grade C ou plus',
                        'Test d\'anglais Duolingo 105 ou plus'
                    ],
                    'french' => [
                        'DELF B1 ou plus',
                        'TCF B1 ou plus',
                        'DALF B2 ou plus (préféré)'
                    ],
                    'documents' => [
                        'Relevés de notes officiels',
                        'Lettre de motivation',
                        'Deux lettres de recommandation',
                        'CV/Curriculum vitae',
                        'Copie du passeport',
                        'Certificats de langue',
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
                ]
            ],
            'AUDENCIA Business School' => [
                'en' => [
                    'academic' => [
                        'High school diploma with good grades',
                        'Background in business, economics, or related fields',
                        'Minimum GPA of 2.6/4.0 or equivalent',
                        'Bachelor\'s degree or equivalent for Master programs'
                    ],
                    'english' => [
                        'IELTS 6.0 or higher (all sections)',
                        'TOEFL iBT 78 or higher',
                        'Cambridge English: Advanced (CAE) Grade C or higher',
                        'Duolingo English Test 100 or higher'
                    ],
                    'french' => [
                        'DELF B1 or higher',
                        'TCF B1 or higher',
                        'DALF B2 or higher (preferred)'
                    ],
                    'documents' => [
                        'Official academic transcripts',
                        'Personal statement (motivation letter)',
                        'Two letters of recommendation',
                        'CV/Resume',
                        'Passport copy',
                        'Language certificates',
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
                'fr' => [
                    'academic' => [
                        'Diplôme de fin d\'études secondaires avec de bonnes notes',
                        'Formation en commerce, économie ou domaines connexes',
                        'GPA minimum de 2,6/4,0 ou équivalent',
                        'Licence ou équivalent pour les programmes Master'
                    ],
                    'english' => [
                        'IELTS 6,0 ou plus (toutes les sections)',
                        'TOEFL iBT 78 ou plus',
                        'Cambridge English: Advanced (CAE) Grade C ou plus',
                        'Test d\'anglais Duolingo 100 ou plus'
                    ],
                    'french' => [
                        'DELF B1 ou plus',
                        'TCF B1 ou plus',
                        'DALF B2 ou plus (préféré)'
                    ],
                    'documents' => [
                        'Relevés de notes officiels',
                        'Lettre de motivation',
                        'Deux lettres de recommandation',
                        'CV/Curriculum vitae',
                        'Copie du passeport',
                        'Certificats de langue',
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
                ]
            ],
            'IESEG School of Management' => [
                'en' => [
                    'academic' => [
                        'High school diploma with excellent grades',
                        'Strong background in business and management',
                        'Minimum GPA of 2.9/4.0 or equivalent',
                        'Bachelor\'s degree or equivalent for Master programs'
                    ],
                    'english' => [
                        'IELTS 6.5 or higher (all sections)',
                        'TOEFL iBT 88 or higher',
                        'Cambridge English: Advanced (CAE) Grade C or higher',
                        'Duolingo English Test 115 or higher'
                    ],
                    'french' => [
                        'DELF B2 or higher',
                        'TCF B2 or higher',
                        'DALF C1 or higher (preferred)'
                    ],
                    'documents' => [
                        'Official academic transcripts',
                        'Personal statement (motivation letter)',
                        'Two letters of recommendation',
                        'CV/Resume',
                        'Passport copy',
                        'Language certificates',
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
                'fr' => [
                    'academic' => [
                        'Diplôme de fin d\'études secondaires avec d\'excellentes notes',
                        'Solide formation en commerce et gestion',
                        'GPA minimum de 2,9/4,0 ou équivalent',
                        'Licence ou équivalent pour les programmes Master'
                    ],
                    'english' => [
                        'IELTS 6,5 ou plus (toutes les sections)',
                        'TOEFL iBT 88 ou plus',
                        'Cambridge English: Advanced (CAE) Grade C ou plus',
                        'Test d\'anglais Duolingo 115 ou plus'
                    ],
                    'french' => [
                        'DELF B2 ou plus',
                        'TCF B2 ou plus',
                        'DALF C1 ou plus (préféré)'
                    ],
                    'documents' => [
                        'Relevés de notes officiels',
                        'Lettre de motivation',
                        'Deux lettres de recommandation',
                        'CV/Curriculum vitae',
                        'Copie du passeport',
                        'Certificats de langue',
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
                ]
            ]
        ];

        $updated = 0;
        foreach ($schoolsData as $schoolName => $requirements) {
            $establishment = $this->entityManager->getRepository(Establishment::class)
                ->findOneBy(['name' => $schoolName]);

            if ($establishment) {
                $establishment->setAdmissionRequirements($requirements['en']);
                $establishment->setAdmissionRequirementsFr($requirements['fr']);

                $this->entityManager->persist($establishment);
                $updated++;

                $io->success("Updated admission requirements for: {$schoolName}");
            } else {
                $io->warning("School not found: {$schoolName}");
            }
        }

        $this->entityManager->flush();

        $io->success("Successfully updated admission requirements for {$updated} French business schools.");

        return Command::SUCCESS;
    }
}
