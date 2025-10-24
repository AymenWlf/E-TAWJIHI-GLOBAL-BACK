<?php

namespace App\Command;

use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:generate-program-slugs',
    description: 'Generate slugs for programs that don\'t have them',
)]
class GenerateProgramSlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Génération des slugs pour les programmes');

        // Récupérer tous les programmes sans slug
        $programs = $this->entityManager->getRepository(Program::class)
            ->createQueryBuilder('p')
            ->where('p.slug IS NULL OR p.slug = :empty')
            ->setParameter('empty', '')
            ->getQuery()
            ->getResult();

        if (empty($programs)) {
            $io->success('Tous les programmes ont déjà un slug.');
            return Command::SUCCESS;
        }

        $io->info(sprintf('Trouvé %d programmes sans slug.', count($programs)));

        $updatedCount = 0;

        foreach ($programs as $program) {
            $slug = $this->generateSlug($program);
            $program->setSlug($slug);

            $this->entityManager->persist($program);
            $updatedCount++;

            $io->text(sprintf('✓ Slug généré pour "%s": %s', $program->getName(), $slug));
        }

        $this->entityManager->flush();

        $io->success(sprintf('Slugs générés pour %d programmes.', $updatedCount));

        return Command::SUCCESS;
    }

    private function generateSlug(Program $program): string
    {
        $name = $program->getName();

        // Nettoyer le nom pour créer un slug
        $slug = $this->slugger->slug($name)->lower();

        // Vérifier l'unicité du slug
        $originalSlug = $slug;
        $counter = 1;

        while ($this->isSlugExists($slug, $program->getId())) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function isSlugExists(string $slug, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->getRepository(Program::class)
            ->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug);

        if ($excludeId) {
            $qb->andWhere('p.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}
