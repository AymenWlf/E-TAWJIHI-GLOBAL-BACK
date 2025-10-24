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
    name: 'app:populate-costs-data',
    description: 'Populates application fees and living costs data for establishments.',
)]
class PopulateCostsDataCommand extends Command
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
        $io->title('Populating Costs Data');

        $establishmentsData = [
            'cole-polytechnique' => [
                'applicationFee' => '125.00',
                'applicationFeeCurrency' => 'EUR',
                'livingCosts' => '12000.00',
                'livingCostsCurrency' => 'EUR'
            ],
            'harvard-university' => [
                'applicationFee' => '85.00',
                'applicationFeeCurrency' => 'USD',
                'livingCosts' => '18000.00',
                'livingCostsCurrency' => 'USD'
            ],
            'stanford-university' => [
                'applicationFee' => '90.00',
                'applicationFeeCurrency' => 'USD',
                'livingCosts' => '20000.00',
                'livingCostsCurrency' => 'USD'
            ]
        ];

        foreach ($establishmentsData as $slug => $data) {
            $establishment = $this->establishmentRepository->findOneBy(['slug' => $slug]);
            if ($establishment) {
                $establishment->setApplicationFee($data['applicationFee']);
                $establishment->setApplicationFeeCurrency($data['applicationFeeCurrency']);
                $establishment->setLivingCosts($data['livingCosts']);
                $establishment->setLivingCostsCurrency($data['livingCostsCurrency']);

                $this->entityManager->persist($establishment);
                $io->success(sprintf('Updated %s costs data', $establishment->getName()));
            } else {
                $io->warning(sprintf('Establishment not found: %s', $slug));
            }
        }

        $this->entityManager->flush();

        $io->success('Successfully populated costs data for all establishments');

        return Command::SUCCESS;
    }
}
