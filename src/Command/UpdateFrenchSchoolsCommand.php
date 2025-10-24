<?php

namespace App\Command;

use App\Entity\Establishment;
use App\Repository\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-french-schools',
    description: 'Met à jour les informations des écoles françaises avec des données enrichies',
)]
class UpdateFrenchSchoolsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EstablishmentRepository $establishmentRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Affiche les modifications sans les appliquer')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Chemin vers le fichier JSON enrichi', '../french_schools_complete_enriched.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $filePath = $input->getOption('file');

        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            $io->error("Le fichier {$filePath} n'existe pas.");
            return Command::FAILURE;
        }

        // Lire le fichier JSON
        $jsonContent = file_get_contents($filePath);
        $enrichedData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error('Erreur lors du décodage du fichier JSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        $io->title('Mise à jour des écoles françaises');
        $io->info(sprintf('Mode: %s', $dryRun ? 'DRY RUN (simulation)' : 'MISE À JOUR RÉELLE'));
        $io->info(sprintf('Fichier: %s', $filePath));
        $io->info(sprintf('Nombre d\'écoles à traiter: %d', count($enrichedData)));

        $updatedCount = 0;
        $notFoundCount = 0;

        foreach ($enrichedData as $schoolName => $schoolData) {
            $io->section("Traitement de: {$schoolName}");

            // Rechercher l'établissement par nom
            $establishment = $this->establishmentRepository->findOneBy([
                'name' => $schoolName,
                'country' => 'France'
            ]);

            if (!$establishment) {
                $io->warning("École non trouvée dans la base de données: {$schoolName}");
                $notFoundCount++;
                continue;
            }

            // Afficher les modifications prévues
            $io->writeln("📍 Ville: {$establishment->getCity()}");

            if (isset($schoolData['description'])) {
                $io->writeln("📝 Description: " . (strlen($schoolData['description']) > 100 ? substr($schoolData['description'], 0, 100) . '...' : $schoolData['description']));
            }

            if (isset($schoolData['admissionRequirements'])) {
                $io->writeln("📋 Exigences d'admission: " . count($schoolData['admissionRequirements']['academic'] ?? []) . " exigences académiques");
            }

            if (isset($schoolData['costs']['tuition'])) {
                $io->writeln("💰 Frais de scolarité: {$schoolData['costs']['tuition']['min']}-{$schoolData['costs']['tuition']['max']} {$schoolData['costs']['tuition']['currency']}");
            }

            if (isset($schoolData['internationalRanking'])) {
                $ranking = $schoolData['internationalRanking'];
                $rankings = array_filter([
                    'QS' => $ranking['qs'] ?? null,
                    'Times' => $ranking['times'] ?? null,
                    'ARWU' => $ranking['arwu'] ?? null,
                    'US News' => $ranking['usNews'] ?? null
                ]);
                if (!empty($rankings)) {
                    $io->writeln("🏆 Classements: " . implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($rankings), $rankings)));
                }
            }

            if (!$dryRun) {
                // Mettre à jour les données
                if (isset($schoolData['description'])) {
                    $establishment->setDescription($schoolData['description']);
                }

                if (isset($schoolData['descriptionFr'])) {
                    $establishment->setDescriptionFr($schoolData['descriptionFr']);
                }

                if (isset($schoolData['admissionRequirements'])) {
                    $establishment->setAdmissionRequirements($schoolData['admissionRequirements']);
                }

                if (isset($schoolData['admissionRequirementsFr'])) {
                    $establishment->setAdmissionRequirementsFr($schoolData['admissionRequirementsFr']);
                }

                if (isset($schoolData['costs']['tuition'])) {
                    $tuition = $schoolData['costs']['tuition'];
                    $establishment->setTuitionMin($tuition['min']);
                    $establishment->setTuitionMax($tuition['max']);
                    $establishment->setTuitionCurrency($tuition['currency']);
                }

                if (isset($schoolData['costs']['applicationFee'])) {
                    $establishment->setApplicationFee($schoolData['costs']['applicationFee']);
                    $establishment->setApplicationFeeCurrency($schoolData['costs']['applicationFeeCurrency']);
                }

                if (isset($schoolData['costs']['livingCosts'])) {
                    $establishment->setLivingCosts($schoolData['costs']['livingCosts']);
                    $establishment->setLivingCostsCurrency($schoolData['costs']['livingCostsCurrency']);
                }

                if (isset($schoolData['internationalRanking'])) {
                    $ranking = $schoolData['internationalRanking'];
                    $establishment->setQsRanking($ranking['qs']);
                    $establishment->setTimesRanking($ranking['times']);
                    $establishment->setArwuRanking($ranking['arwu']);
                    $establishment->setUsNewsRanking($ranking['usNews']);
                    $establishment->setWorldRanking($ranking['worldRanking']);
                }

                if (isset($schoolData['foundedYear'])) {
                    $establishment->setFoundedYear($schoolData['foundedYear']);
                }

                if (isset($schoolData['website'])) {
                    $establishment->setWebsite($schoolData['website']);
                }

                if (isset($schoolData['email'])) {
                    $establishment->setEmail($schoolData['email']);
                }

                if (isset($schoolData['phone'])) {
                    $establishment->setPhone($schoolData['phone']);
                }

                if (isset($schoolData['address'])) {
                    $establishment->setAddress($schoolData['address']);
                }

                $establishment->setUpdatedAt(new \DateTime());
                $this->entityManager->persist($establishment);
                $io->writeln("✅ Mise à jour effectuée");
            } else {
                $io->writeln("🔍 Simulation - aucune modification appliquée");
            }

            $updatedCount++;
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->success(sprintf(
            'Traitement terminé: %d écoles mises à jour, %d non trouvées',
            $updatedCount,
            $notFoundCount
        ));

        if ($dryRun) {
            $io->note('Mode simulation activé - aucune modification n\'a été appliquée');
            $io->note('Utilisez --dry-run=false pour appliquer les modifications');
        }

        return Command::SUCCESS;
    }
}
