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
    name: 'app:update-scholarship-data',
    description: 'Update scholarship types and descriptions for existing establishments',
)]
class UpdateScholarshipDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Updating Scholarship Data for Establishments');

        // Scholarship data mapping for each establishment
        $scholarshipData = [
            'University of Toronto' => [
                'types' => ['university_full', 'merit', 'international'],
                'description' => 'University of Toronto offers comprehensive scholarship programs including full tuition coverage for exceptional students, merit-based awards for academic excellence, and special international student scholarships. The university is committed to making education accessible to talented students from around the world.'
            ],
            'Sorbonne University' => [
                'types' => ['government', 'university_partial', 'merit'],
                'description' => 'Sorbonne University provides various scholarship opportunities including French government scholarships for international students, partial university scholarships covering 25-50% of tuition fees, and merit-based awards for outstanding academic performance.'
            ],
            'University of Oxford' => [
                'types' => ['university_full', 'merit', 'research', 'international'],
                'description' => 'Oxford offers prestigious scholarship programs including full tuition and living expense coverage through Rhodes Scholarships, merit-based awards for academic excellence, research scholarships for PhD candidates, and special international student funding opportunities.'
            ],
            'Harvard University' => [
                'types' => ['university_full', 'need_based', 'merit', 'international'],
                'description' => 'Harvard University provides need-based financial aid covering 100% of demonstrated need, merit scholarships for exceptional students, and comprehensive international student support programs. The university is committed to making education accessible regardless of financial background.'
            ],
            'MIT' => [
                'types' => ['university_full', 'merit', 'research', 'international'],
                'description' => 'MIT offers generous scholarship programs including full tuition coverage for students from families earning less than $90,000, merit-based awards for academic excellence, research funding for graduate students, and special international student scholarships.'
            ],
            'University of Melbourne' => [
                'types' => ['university_partial', 'merit', 'international', 'sports'],
                'description' => 'University of Melbourne provides partial scholarships covering 25-75% of tuition fees, merit-based awards for academic excellence, international student scholarships, and athletic scholarships for exceptional sports performers.'
            ],
            'Tsinghua University' => [
                'types' => ['government', 'university_partial', 'merit'],
                'description' => 'Tsinghua University offers Chinese government scholarships for international students, partial university scholarships, and merit-based awards. The university has strong partnerships with various scholarship programs to support international students.'
            ],
            'University of Tokyo' => [
                'types' => ['government', 'university_partial', 'research'],
                'description' => 'University of Tokyo provides Japanese government scholarships (MEXT), partial university scholarships covering up to 50% of tuition fees, and research scholarships for graduate students. The university supports international students through various funding programs.'
            ],
            'University of Sydney' => [
                'types' => ['university_partial', 'merit', 'international', 'sports'],
                'description' => 'University of Sydney offers partial scholarships covering 25-50% of tuition fees, merit-based awards for academic excellence, international student scholarships, and athletic scholarships for outstanding sports achievements.'
            ],
            'École Polytechnique' => [
                'types' => ['government', 'university_full', 'merit'],
                'description' => 'École Polytechnique provides French government scholarships, full university scholarships for exceptional students, and merit-based awards. The institution is known for its strong support of international students through various funding programs.'
            ],
            'Stanford University' => [
                'types' => ['university_full', 'need_based', 'merit', 'research'],
                'description' => 'Stanford University offers comprehensive financial aid including need-based grants covering 100% of demonstrated need, merit scholarships for exceptional students, and research funding for graduate students. The university is committed to making education accessible to all qualified students.'
            ]
        ];

        $establishments = $this->entityManager->getRepository(Establishment::class)->findAll();
        $updated = 0;

        foreach ($establishments as $establishment) {
            if (isset($scholarshipData[$establishment->getName()])) {
                $data = $scholarshipData[$establishment->getName()];

                $establishment->setScholarshipTypes($data['types']);
                $establishment->setScholarshipDescription($data['description']);

                $this->entityManager->persist($establishment);
                $updated++;

                $io->text(sprintf('Updated scholarship data for: %s', $establishment->getName()));
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully updated scholarship data for %d establishments', $updated));

        return Command::SUCCESS;
    }
}
