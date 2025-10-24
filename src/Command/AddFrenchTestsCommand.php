<?php

namespace App\Command;

use App\Entity\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-french-tests',
    description: 'Add French language tests (TCF, DELF, DALF) to englishTest parameters',
)]
class AddFrenchTestsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Adding French Language Tests to englishTest Parameters');

        // French language tests data
        $frenchTests = [
            [
                'code' => 'tcf',
                'labelEn' => 'TCF',
                'labelFr' => 'TCF',
                'descriptionEn' => 'Test de Connaissance du Français - French language proficiency test for non-native speakers. It evaluates general French language skills and is recognized by French institutions.',
                'descriptionFr' => 'Test de Connaissance du Français - Test de compétence en français pour les non-francophones. Il évalue les compétences générales en français et est reconnu par les institutions françaises.',
                'scoreRange' => '100-699 points',
                'sortOrder' => 10
            ],
            [
                'code' => 'delf',
                'labelEn' => 'DELF',
                'labelFr' => 'DELF',
                'descriptionEn' => 'Diplôme d\'Études en Langue Française - French language diploma for non-native speakers. Available in different levels (A1, A2, B1, B2) to assess French language skills.',
                'descriptionFr' => 'Diplôme d\'Études en Langue Française - Diplôme de langue française pour les non-francophones. Disponible en différents niveaux (A1, A2, B1, B2) pour évaluer les compétences en français.',
                'scoreRange' => '50/100 points minimum',
                'sortOrder' => 11
            ],
            [
                'code' => 'dalf',
                'labelEn' => 'DALF',
                'labelFr' => 'DALF',
                'descriptionEn' => 'Diplôme Approfondi de Langue Française - Advanced French language diploma for non-native speakers. Available in C1 and C2 levels for advanced French proficiency.',
                'descriptionFr' => 'Diplôme Approfondi de Langue Française - Diplôme approfondi de langue française pour les non-francophones. Disponible en niveaux C1 et C2 pour une maîtrise avancée du français.',
                'scoreRange' => '50/100 points minimum',
                'sortOrder' => 12
            ]
        ];

        $addedCount = 0;
        $skippedCount = 0;

        foreach ($frenchTests as $testData) {
            // Check if parameter already exists
            $existingParameter = $this->entityManager->getRepository(Parameter::class)
                ->findOneBy([
                    'category' => 'englishTest',
                    'code' => $testData['code']
                ]);

            if ($existingParameter) {
                $io->note("Parameter {$testData['code']} already exists. Skipping...");
                $skippedCount++;
                continue;
            }

            // Create new parameter
            $parameter = new Parameter();
            $parameter->setCategory('englishTest');
            $parameter->setCode($testData['code']);
            $parameter->setLabelEn($testData['labelEn']);
            $parameter->setLabelFr($testData['labelFr']);
            $parameter->setDescriptionEn($testData['descriptionEn']);
            $parameter->setDescriptionFr($testData['descriptionFr']);
            $parameter->setScoreRange($testData['scoreRange']);
            $parameter->setSortOrder($testData['sortOrder']);
            $parameter->setIsActive(true);

            $this->entityManager->persist($parameter);
            $addedCount++;

            $io->text("Added: {$testData['labelEn']} ({$testData['code']})");
        }

        $this->entityManager->flush();

        $io->success("French language tests added successfully!");
        $io->table(
            ['Metric', 'Count'],
            [
                ['Added', $addedCount],
                ['Skipped (already exist)', $skippedCount],
                ['Total processed', count($frenchTests)]
            ]
        );

        return Command::SUCCESS;
    }
}
