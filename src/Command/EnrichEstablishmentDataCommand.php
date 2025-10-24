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
    name: 'app:enrich-establishment-data',
    description: 'Enrich establishment data with multilingual content and additional information',
)]
class EnrichEstablishmentDataCommand extends Command
{
    public function __construct(
        private EstablishmentRepository $establishmentRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Enriching Establishment Data');

        $establishments = $this->establishmentRepository->findAll();

        // Data enrichment map (slug => data)
        $enrichmentData = [
            'eth-zurich' => [
                'foundedYear' => 1855,
                'description' => 'ETH Zurich is a public research university in Zurich, Switzerland. Founded in 1855, it is one of the leading universities for science and technology worldwide.',
                'descriptionFr' => 'ETH Zurich est une université de recherche publique à Zurich, en Suisse. Fondée en 1855, c\'est l\'une des universités leaders pour la science et la technologie dans le monde.',
                'mission' => 'To be an internationally significant research university, with undergraduate, graduate and professional programs of excellent quality.',
                'missionFr' => 'Être une université de recherche de renommée internationale, avec des programmes de premier cycle, de cycles supérieurs et professionnels d\'excellente qualité.',
            ],
            'harvard-university' => [
                'foundedYear' => 1636,
                'description' => 'Harvard University is a private Ivy League research university in Cambridge, Massachusetts. Established in 1636, it is the oldest institution of higher education in the United States.',
                'descriptionFr' => 'L\'Université Harvard est une université de recherche privée de l\'Ivy League à Cambridge, Massachusetts. Fondée en 1636, c\'est la plus ancienne institution d\'enseignement supérieur aux États-Unis.',
                'mission' => 'To educate citizens and citizen-leaders for our society through a commitment to the transformative power of a liberal arts and sciences education.',
                'missionFr' => 'Éduquer les citoyens et les leaders citoyens de notre société grâce à un engagement envers le pouvoir transformateur d\'une éducation en arts libéraux et en sciences.',
            ],
            'stanford-university' => [
                'foundedYear' => 1885,
                'description' => 'Stanford University is a private research university in Stanford, California. Founded in 1885, it is known for its entrepreneurial character and excellence in research.',
                'descriptionFr' => 'L\'Université Stanford est une université de recherche privée à Stanford, en Californie. Fondée en 1885, elle est connue pour son caractère entrepreneurial et son excellence en recherche.',
                'mission' => 'To qualify students for personal success and direct usefulness in life, and to promote the public welfare by exercising an influence on behalf of humanity and civilization.',
                'missionFr' => 'Qualifier les étudiants pour le succès personnel et l\'utilité directe dans la vie, et promouvoir le bien-être public en exerçant une influence au nom de l\'humanité et de la civilisation.',
            ],
            'massachusetts-institute-of-technology-mit' => [
                'foundedYear' => 1861,
                'description' => 'The Massachusetts Institute of Technology is a private land-grant research university in Cambridge, Massachusetts. Founded in 1861, MIT is renowned for its programs in engineering and physical sciences.',
                'descriptionFr' => 'Le Massachusetts Institute of Technology est une université de recherche privée à Cambridge, Massachusetts. Fondé en 1861, le MIT est réputé pour ses programmes en ingénierie et en sciences physiques.',
                'mission' => 'To advance knowledge and educate students in science, technology, and other areas of scholarship that will best serve the nation and the world in the 21st century.',
                'missionFr' => 'Faire progresser les connaissances et éduquer les étudiants en science, technologie et autres domaines d\'études qui serviront le mieux la nation et le monde au 21e siècle.',
            ],
            'university-of-cambridge' => [
                'foundedYear' => 1209,
                'description' => 'The University of Cambridge is a collegiate research university in Cambridge, United Kingdom. Founded in 1209, it is the second-oldest university in the English-speaking world.',
                'descriptionFr' => 'L\'Université de Cambridge est une université de recherche collégiale à Cambridge, au Royaume-Uni. Fondée en 1209, c\'est la deuxième plus ancienne université du monde anglophone.',
                'mission' => 'To contribute to society through the pursuit of education, learning, and research at the highest international levels of excellence.',
                'missionFr' => 'Contribuer à la société par la poursuite de l\'éducation, de l\'apprentissage et de la recherche aux plus hauts niveaux d\'excellence internationaux.',
            ],
            'university-of-oxford' => [
                'foundedYear' => 1096,
                'description' => 'The University of Oxford is a collegiate research university in Oxford, England. There is evidence of teaching as early as 1096, making it the oldest university in the English-speaking world.',
                'descriptionFr' => 'L\'Université d\'Oxford est une université de recherche collégiale à Oxford, en Angleterre. Il y a des preuves d\'enseignement dès 1096, ce qui en fait la plus ancienne université du monde anglophone.',
                'mission' => 'To aim for world-class excellence in research and education, and to develop leaders in many spheres of life.',
                'missionFr' => 'Viser l\'excellence mondiale en recherche et en éducation, et développer des leaders dans de nombreux domaines de la vie.',
            ],
            'california-institute-of-technology-caltech' => [
                'foundedYear' => 1891,
                'description' => 'The California Institute of Technology is a private research university in Pasadena, California. Founded in 1891, Caltech is known for its strength in science and engineering.',
                'descriptionFr' => 'Le California Institute of Technology est une université de recherche privée à Pasadena, en Californie. Fondé en 1891, Caltech est connu pour sa force en sciences et en ingénierie.',
                'mission' => 'To expand human knowledge and benefit society through research integrated with education.',
                'missionFr' => 'Élargir les connaissances humaines et bénéficier à la société par la recherche intégrée à l\'éducation.',
            ],
            'imperial-college-london' => [
                'foundedYear' => 1907,
                'description' => 'Imperial College London is a public research university in London, United Kingdom. Founded in 1907, it specializes in science, engineering, medicine and business.',
                'descriptionFr' => 'Imperial College London est une université de recherche publique à Londres, au Royaume-Uni. Fondé en 1907, il se spécialise en sciences, ingénierie, médecine et commerce.',
                'mission' => 'To achieve enduring excellence in research and education in science, engineering, medicine and business for the benefit of society.',
                'missionFr' => 'Atteindre l\'excellence durable en recherche et en éducation en sciences, ingénierie, médecine et commerce pour le bénéfice de la société.',
            ],
            'university-of-chicago' => [
                'foundedYear' => 1890,
                'description' => 'The University of Chicago is a private research university in Chicago, Illinois. Founded in 1890, it is known for its influential research and rigorous academics.',
                'descriptionFr' => 'L\'Université de Chicago est une université de recherche privée à Chicago, Illinois. Fondée en 1890, elle est connue pour sa recherche influente et ses académiques rigoureux.',
                'mission' => 'To provide an education that empowers individuals to challenge conventional thinking and develop creative solutions to complex problems.',
                'missionFr' => 'Fournir une éducation qui permet aux individus de remettre en question la pensée conventionnelle et de développer des solutions créatives aux problèmes complexes.',
            ],
            'ucl-university-college-london' => [
                'foundedYear' => 1826,
                'description' => 'University College London is a public research university in London, United Kingdom. Founded in 1826, UCL was the first university institution to be established in London.',
                'descriptionFr' => 'University College London est une université de recherche publique à Londres, au Royaume-Uni. Fondée en 1826, UCL fut la première institution universitaire établie à Londres.',
                'mission' => 'To have a transformative impact on society through world-class research and education.',
                'missionFr' => 'Avoir un impact transformateur sur la société grâce à la recherche et à l\'éducation de classe mondiale.',
            ],
        ];

        $updated = 0;

        foreach ($establishments as $establishment) {
            $slug = $establishment->getSlug();

            if (isset($enrichmentData[$slug])) {
                $data = $enrichmentData[$slug];

                $establishment->setFoundedYear($data['foundedYear']);
                $establishment->setDescription($data['description']);
                $establishment->setDescriptionFr($data['descriptionFr']);
                $establishment->setMission($data['mission']);
                $establishment->setMissionFr($data['missionFr']);

                $this->entityManager->persist($establishment);
                $updated++;

                $io->success(sprintf('Updated: %s', $establishment->getName()));
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully enriched %d establishments', $updated));

        return Command::SUCCESS;
    }
}
