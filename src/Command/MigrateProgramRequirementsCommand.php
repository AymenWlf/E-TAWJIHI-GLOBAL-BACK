<?php

namespace App\Command;

use App\Service\ProgramRequirementService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migrate-program-requirements',
    description: 'Migrate existing program data to dynamic requirements system',
)]
class MigrateProgramRequirementsCommand extends Command
{
    private ProgramRequirementService $requirementService;

    public function __construct(ProgramRequirementService $requirementService)
    {
        $this->requirementService = $requirementService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Migrating Program Requirements');

        try {
            $migrated = $this->requirementService->migrateAllPrograms();

            $io->success("Successfully migrated {$migrated} programs to dynamic requirements system.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
