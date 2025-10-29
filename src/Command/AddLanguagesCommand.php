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
    name: 'app:add-languages',
    description: 'Add language parameters with full names',
)]
class AddLanguagesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $languages = [
            [
                'code' => 'en',
                'labelEn' => 'English',
                'labelFr' => 'Anglais',
                'meta' => ['flag' => '🇺🇸'],
                'sortOrder' => 1
            ],
            [
                'code' => 'fr',
                'labelEn' => 'French',
                'labelFr' => 'Français',
                'meta' => ['flag' => '🇫🇷'],
                'sortOrder' => 2
            ],
            [
                'code' => 'ar',
                'labelEn' => 'Arabic',
                'labelFr' => 'Arabe',
                'meta' => ['flag' => '🇸🇦'],
                'sortOrder' => 3
            ],
            [
                'code' => 'es',
                'labelEn' => 'Spanish',
                'labelFr' => 'Espagnol',
                'meta' => ['flag' => '🇪🇸'],
                'sortOrder' => 4
            ],
            [
                'code' => 'de',
                'labelEn' => 'German',
                'labelFr' => 'Allemand',
                'meta' => ['flag' => '🇩🇪'],
                'sortOrder' => 5
            ],
            [
                'code' => 'it',
                'labelEn' => 'Italian',
                'labelFr' => 'Italien',
                'meta' => ['flag' => '🇮🇹'],
                'sortOrder' => 6
            ],
            [
                'code' => 'pt',
                'labelEn' => 'Portuguese',
                'labelFr' => 'Portugais',
                'meta' => ['flag' => '🇵🇹'],
                'sortOrder' => 7
            ],
            [
                'code' => 'ru',
                'labelEn' => 'Russian',
                'labelFr' => 'Russe',
                'meta' => ['flag' => '🇷🇺'],
                'sortOrder' => 8
            ],
            [
                'code' => 'zh',
                'labelEn' => 'Chinese',
                'labelFr' => 'Chinois',
                'meta' => ['flag' => '🇨🇳'],
                'sortOrder' => 9
            ],
            [
                'code' => 'ja',
                'labelEn' => 'Japanese',
                'labelFr' => 'Japonais',
                'meta' => ['flag' => '🇯🇵'],
                'sortOrder' => 10
            ],
            [
                'code' => 'ko',
                'labelEn' => 'Korean',
                'labelFr' => 'Coréen',
                'meta' => ['flag' => '🇰🇷'],
                'sortOrder' => 11
            ]
        ];

        // First, remove existing language parameters with codes
        $existingLanguages = $this->entityManager->getRepository(Parameter::class)
            ->findBy(['category' => 'language']);

        foreach ($existingLanguages as $lang) {
            $this->entityManager->remove($lang);
        }

        // Flush the removals first
        $this->entityManager->flush();

        // Add new language parameters
        foreach ($languages as $langData) {
            $parameter = new Parameter();
            $parameter->setCategory('language');
            $parameter->setCode($langData['code']);
            $parameter->setLabelEn($langData['labelEn']);
            $parameter->setLabelFr($langData['labelFr']);
            $parameter->setMeta($langData['meta']);
            $parameter->setSortOrder($langData['sortOrder']);
            $parameter->setIsActive(true);

            $this->entityManager->persist($parameter);
        }

        $this->entityManager->flush();

        $io->success('Languages added successfully with full names!');
        $io->note('Languages now return full names instead of codes in the API.');

        return Command::SUCCESS;
    }
}
