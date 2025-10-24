<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fix-annual-budget',
    description: 'Fix annual budget data type issues in user profiles.',
)]
class FixAnnualBudgetCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Fix empty string values in annual_budget column
            $connection = $this->entityManager->getConnection();

            $io->info('Fixing annual_budget column...');

            // Update empty strings to NULL
            $result = $connection->executeStatement(
                "UPDATE user_profiles SET annual_budget = NULL WHERE annual_budget = '' OR annual_budget = '0'"
            );

            $io->success(sprintf('Updated %d user profiles with empty annual_budget values.', $result));

            // Show current data
            $profiles = $connection->fetchAllAssociative(
                "SELECT id, first_name, last_name, annual_budget FROM user_profiles WHERE annual_budget IS NOT NULL"
            );

            if (!empty($profiles)) {
                $io->info('Current annual_budget values:');
                foreach ($profiles as $profile) {
                    $io->text(sprintf(
                        'ID: %d, Name: %s %s, Budget: %s',
                        $profile['id'],
                        $profile['first_name'] ?? 'N/A',
                        $profile['last_name'] ?? 'N/A',
                        $profile['annual_budget'] ?? 'NULL'
                    ));
                }
            } else {
                $io->info('No profiles with annual_budget values found.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error fixing annual_budget: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
