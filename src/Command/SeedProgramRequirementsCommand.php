<?php

namespace App\Command;

use App\Entity\Program;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-program-requirements',
    description: 'Seed program academic qualification and grade requirements',
)]
class SeedProgramRequirementsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProgramRepository $programRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $programs = $this->programRepository->findAll();

        $academicQualifications = [
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'High School Diploma',
            'Associate Degree',
            'Doctorate',
            'Professional Certificate'
        ];

        $gradeSystems = ['CGPA_4', 'CGPA_20', 'PERCENTAGE', 'GPA_5', 'GPA_10'];

        $io->progressStart(count($programs));

        foreach ($programs as $program) {
            // Randomly assign academic qualification requirements
            $requiresQualification = rand(0, 1) === 1;
            $program->setRequiresAcademicQualification($requiresQualification);

            if ($requiresQualification) {
                // Randomly select 1-3 academic qualifications
                $numQualifications = rand(1, 3);
                $selectedQualifications = array_rand($academicQualifications, $numQualifications);
                if (!is_array($selectedQualifications)) {
                    $selectedQualifications = [$selectedQualifications];
                }

                $qualifications = array_map(function ($index) use ($academicQualifications) {
                    return $academicQualifications[$index];
                }, $selectedQualifications);

                $program->setAcademicQualifications($qualifications);

                // Set grade requirements
                $gradeSystem = $gradeSystems[array_rand($gradeSystems)];
                $program->setGradeSystem($gradeSystem);

                // Set minimum grade based on system
                $minimumGrade = match ($gradeSystem) {
                    'CGPA_4' => rand(25, 40) / 10, // 2.5 to 4.0
                    'CGPA_20' => rand(120, 180) / 10, // 12.0 to 18.0
                    'PERCENTAGE' => rand(60, 90), // 60% to 90%
                    'GPA_5' => rand(30, 50) / 10, // 3.0 to 5.0
                    'GPA_10' => rand(60, 100) / 10, // 6.0 to 10.0
                    default => 3.0
                };

                $program->setMinimumGrade((string)$minimumGrade);

                // Set grade requirements for different systems
                $gradeRequirements = [];
                foreach ($gradeSystems as $system) {
                    if ($system !== $gradeSystem) {
                        // Convert minimum grade to other systems (simplified conversion)
                        $convertedGrade = match ($system) {
                            'CGPA_4' => $minimumGrade * 0.8,
                            'CGPA_20' => $minimumGrade * 4,
                            'PERCENTAGE' => $minimumGrade * 20,
                            'GPA_5' => $minimumGrade * 1.25,
                            'GPA_10' => $minimumGrade * 2.5,
                            default => $minimumGrade
                        };
                        $gradeRequirements[$system] = round($convertedGrade, 2);
                    }
                }
                $program->setGradeRequirements($gradeRequirements);
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();

        $io->success('Program requirements seeded successfully!');

        return Command::SUCCESS;
    }
}
