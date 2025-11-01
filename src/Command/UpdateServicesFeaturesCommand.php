<?php

namespace App\Command;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-services-features',
    description: 'Update services features with the new detailed lists',
)]
class UpdateServicesFeaturesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ServiceRepository $serviceRepository;

    public function __construct(EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $this->entityManager = $entityManager;
        $this->serviceRepository = $serviceRepository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Define the new features for each service
        $servicesFeatures = [
            // TASSJIL PLUS (or TASSJIL Service)
            'TASSJIL PLUS' => [
                'featuresFr' => [
                    "Application d'orientation et d'annonces",
                    "Inscription correcte dans les écoles sélectionnées",
                    "Écoles publiques, privées, militaires et semi-publiques",
                    "Séance d'orientation pour sélectionner les écoles convenables",
                    "Aide à la préparation de votre dossier final et à l'envoi postal pour les écoles concernées",
                    "Réductions exclusives auprès des établissements privés partenaires",
                    "Test d'orientation + rapport certifié",
                    "Support VIP prioritaire",
                    "Suivi personnalisé jusqu'à l'inscription finale"
                ],
                'features' => [
                    "Orientation and announcements application",
                    "Correct registration in selected schools",
                    "Public, private, military and semi-public schools",
                    "Orientation session to select suitable schools",
                    "Help preparing your final file and postal sending for concerned schools",
                    "Exclusive discounts at partner private establishments",
                    "Orientation test + certified report",
                    "VIP priority support",
                    "Personalized follow-up until final registration"
                ]
            ],
            // TASSJIL TOP 15
            'TASSJIL TOP 15' => [
                'featuresFr' => [
                    "Application d'orientation et d'annonces",
                    "Inscription correcte dans les 15 écoles sélectionnées",
                    "Écoles publiques, privées, militaires et semi-publiques",
                    "Séance d'orientation pour sélectionner les écoles convenables",
                    "Aide à la préparation de votre dossier final et à l'envoi postal pour les écoles concernées",
                    "Réductions exclusives auprès des établissements privés partenaires",
                    "Test d'orientation + rapport certifié"
                ],
                'features' => [
                    "Orientation and announcements application",
                    "Correct registration in the 15 selected schools",
                    "Public, private, military and semi-public schools",
                    "Orientation session to select suitable schools",
                    "Help preparing your final file and postal sending for concerned schools",
                    "Exclusive discounts at partner private establishments",
                    "Orientation test + certified report"
                ]
            ],
            // CAMPUS FRANCE
            'CAMPUS FRANCE' => [
                'featuresFr' => [
                    "Application et Consultation d'orientation personnalisée",
                    "Élaboration d'un projet d'étude solide et choix des écoles adaptées",
                    "Inscription complète sur PASTEL (Campus France)",
                    "Accompagnement pour la préparation des documents nécessaires",
                    "Procédure \"Je suis accepté\" sur PASTEL (Campus France)",
                    "Procédure VISA, logement et garant (services à la demande)"
                ],
                'features' => [
                    "Application and Personalized orientation consultation",
                    "Development of a solid study project and selection of suitable schools",
                    "Complete registration on PASTEL (Campus France)",
                    "Support for preparing necessary documents",
                    "\"I am accepted\" procedure on PASTEL (Campus France)",
                    "VISA, accommodation and guarantor procedures (on-demand services)"
                ]
            ],
            // PARCOURSUP
            'Parcoursup' => [
                'featuresFr' => [
                    "Application et Consultation d'orientation personnalisée",
                    "Élaboration d'un projet d'étude solide et choix des écoles adaptées",
                    "Inscription complète sur PARCOURSUP",
                    "Accompagnement pour la préparation des documents nécessaires",
                    "Procédure VISA, logement et garant (services à la demande)"
                ],
                'features' => [
                    "Application and Personalized orientation consultation",
                    "Development of a solid study project and selection of suitable schools",
                    "Complete registration on PARCOURSUP",
                    "Support for preparing necessary documents",
                    "VISA, accommodation and guarantor procedures (on-demand services)"
                ]
            ],
            // PARCOURSUP (alternative name)
            'PARCOURSUP' => [
                'featuresFr' => [
                    "Application et Consultation d'orientation personnalisée",
                    "Élaboration d'un projet d'étude solide et choix des écoles adaptées",
                    "Inscription complète sur PARCOURSUP",
                    "Accompagnement pour la préparation des documents nécessaires",
                    "Procédure VISA, logement et garant (services à la demande)"
                ],
                'features' => [
                    "Application and Personalized orientation consultation",
                    "Development of a solid study project and selection of suitable schools",
                    "Complete registration on PARCOURSUP",
                    "Support for preparing necessary documents",
                    "VISA, accommodation and guarantor procedures (on-demand services)"
                ]
            ]
        ];

        $updated = 0;
        $notFound = [];

        foreach ($servicesFeatures as $serviceName => $features) {
            // Try to find service by name or nameFr
            $service = $this->serviceRepository->createQueryBuilder('s')
                ->where('s.name = :name OR s.nameFr = :name')
                ->setParameter('name', $serviceName)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$service) {
                // Also try case-insensitive search
                $service = $this->serviceRepository->createQueryBuilder('s')
                    ->where('LOWER(s.name) = LOWER(:name) OR LOWER(s.nameFr) = LOWER(:name)')
                    ->setParameter('name', $serviceName)
                    ->getQuery()
                    ->getOneOrNullResult();
            }

            // Also check for TASSJIL Service (might be named differently)
            if (!$service && $serviceName === 'TASSJIL PLUS') {
                $service = $this->serviceRepository->createQueryBuilder('s')
                    ->where('s.name LIKE :pattern OR s.nameFr LIKE :pattern')
                    ->setParameter('pattern', '%TASSJIL%')
                    ->andWhere('s.name NOT LIKE :top15')
                    ->setParameter('top15', '%TOP 15%')
                    ->getQuery()
                    ->getOneOrNullResult();
            }

            if ($service) {
                $service->setFeatures($features['features']);
                $service->setFeaturesFr($features['featuresFr']);
                $service->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($service);
                $updated++;
                $io->success("Updated features for: {$serviceName}");
            } else {
                $notFound[] = $serviceName;
                $io->warning("Service not found: {$serviceName}");
            }
        }

        if ($updated > 0) {
            $this->entityManager->flush();
            $io->success("Successfully updated {$updated} service(s)!");
        }

        if (!empty($notFound)) {
            $io->note('Services not found: ' . implode(', ', $notFound));
            $io->note('You may need to check the exact service names in the database.');
        }

        return Command::SUCCESS;
    }
}

