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
    name: 'app:generate-slugs',
    description: 'Generate SEO-friendly slugs for all establishments',
)]
class GenerateSlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Generating SEO-friendly slugs for establishments');

        $establishments = $this->entityManager->getRepository(Establishment::class)->findAll();
        $updated = 0;

        foreach ($establishments as $establishment) {
            $slug = $this->generateSlug($establishment->getName());

            // Ensure uniqueness
            $originalSlug = $slug;
            $counter = 1;
            while ($this->slugExists($slug, $establishment->getId())) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $establishment->setSlug($slug);
            $this->entityManager->persist($establishment);
            $updated++;

            $io->text(sprintf('Generated slug for: %s -> %s', $establishment->getName(), $slug));
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully generated slugs for %d establishments', $updated));

        return Command::SUCCESS;
    }

    private function generateSlug(string $name): string
    {
        // Convert to lowercase
        $slug = strtolower($name);

        // Replace spaces and special characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');

        // Limit length
        if (strlen($slug) > 100) {
            $slug = substr($slug, 0, 100);
            $slug = rtrim($slug, '-');
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->getRepository(Establishment::class)->createQueryBuilder('e');
        $qb->where('e.slug = :slug')
            ->setParameter('slug', $slug);

        if ($excludeId) {
            $qb->andWhere('e.id != :id')
                ->setParameter('id', $excludeId);
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}
