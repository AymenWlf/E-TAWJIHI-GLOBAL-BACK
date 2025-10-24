<?php

namespace App\Command;

use App\Entity\Program;
use App\Entity\ProgramRequirement;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-language-test-requirements',
    description: 'Add language test requirements to programs for testing',
)]
class AddLanguageTestRequirementsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ProgramRepository $programRepository;

    public function __construct(EntityManagerInterface $entityManager, ProgramRepository $programRepository)
    {
        $this->entityManager = $entityManager;
        $this->programRepository = $programRepository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Adding Language Test Requirements');

        // This command is deprecated - use structured requirements instead
        $io->success("This command is deprecated - use structured requirements instead.");
        return Command::SUCCESS;
    }
}
