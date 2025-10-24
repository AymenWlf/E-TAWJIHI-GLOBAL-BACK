<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cleanup-data',
    description: 'Clean up corrupted data in the database.',
)]
class CleanupDataCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->info('Cleaning up corrupted data...');

            // Fix annual_budget column
            $this->connection->executeStatement(
                "UPDATE user_profiles SET annual_budget = NULL WHERE annual_budget = '' OR annual_budget = '0'"
            );

            // Fix any other corrupted decimal values
            $this->connection->executeStatement(
                "UPDATE qualifications SET score = NULL WHERE score = '' OR score = '0'"
            );

            $io->success('Data cleanup completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error during cleanup: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
