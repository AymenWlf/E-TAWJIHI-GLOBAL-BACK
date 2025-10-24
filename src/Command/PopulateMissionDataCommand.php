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
    name: 'app:populate-mission-data',
    description: 'Populates mission and description data for establishments.',
)]
class PopulateMissionDataCommand extends Command
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
        $io->title('Populating Mission and Description Data');

        $establishmentsData = [
            'cole-polytechnique' => [
                'description' => '<p>École Polytechnique, founded in 1794, is France\'s most prestigious engineering school. Located in Palaiseau, it trains the future scientific and technological leaders of tomorrow.</p><p>The school offers excellence programs in engineering, fundamental sciences, economics and management, with a unique multidisciplinary approach.</p>',
                'descriptionFr' => '<p>L\'École Polytechnique, fondée en 1794, est la plus prestigieuse école d\'ingénieurs de France. Située à Palaiseau, elle forme les futurs leaders scientifiques et technologiques de demain.</p><p>L\'école propose des programmes d\'excellence en ingénierie, sciences fondamentales, économie et management, avec une approche multidisciplinaire unique.</p>',
                'mission' => '<p>Our mission is to train excellent engineers and scientists, capable of innovating and leading in a constantly evolving world. We are committed to promoting academic excellence, cutting-edge research and entrepreneurship.</p>',
                'missionFr' => '<p>Notre mission est de former des ingénieurs et scientifiques d\'excellence, capables d\'innover et de diriger dans un monde en constante évolution. Nous nous engageons à promouvoir l\'excellence académique, la recherche de pointe et l\'entrepreneuriat.</p>',
                'foundedYear' => 1794,
                'students' => 3000,
                'language' => 'French',
                'worldRanking' => 38
            ],
            'harvard-university' => [
                'description' => '<p>Harvard University, established in 1636, is America\'s oldest institution of higher learning and one of the most prestigious universities in the world. Located in Cambridge, Massachusetts, Harvard has been at the forefront of academic excellence for nearly four centuries.</p><p>The university offers a comprehensive range of undergraduate and graduate programs across various disciplines, fostering innovation, critical thinking, and global leadership.</p>',
                'descriptionFr' => '<p>L\'Université Harvard, fondée en 1636, est la plus ancienne institution d\'enseignement supérieur d\'Amérique et l\'une des universités les plus prestigieuses au monde. Située à Cambridge, Massachusetts, Harvard est à l\'avant-garde de l\'excellence académique depuis près de quatre siècles.</p><p>L\'université propose une gamme complète de programmes de premier cycle et de cycles supérieurs dans diverses disciplines, favorisant l\'innovation, la pensée critique et le leadership mondial.</p>',
                'mission' => '<p>Harvard University\'s mission is to educate the citizens and citizen-leaders for our society. We do this through our commitment to the transformative power of a liberal arts and sciences education.</p>',
                'missionFr' => '<p>La mission de l\'Université Harvard est d\'éduquer les citoyens et les leaders citoyens de notre société. Nous le faisons par notre engagement envers le pouvoir transformateur d\'une éducation en arts libéraux et sciences.</p>',
                'foundedYear' => 1636,
                'students' => 23000,
                'language' => 'English',
                'worldRanking' => 1
            ],
            'stanford-university' => [
                'description' => '<p>Stanford University, founded in 1885, is one of the world\'s leading research universities. Located in the heart of Silicon Valley, Stanford has been a driving force behind technological innovation and entrepreneurship.</p><p>The university offers a diverse range of academic programs and is known for its cutting-edge research, interdisciplinary approach, and strong connections to industry.</p>',
                'descriptionFr' => '<p>L\'Université Stanford, fondée en 1885, est l\'une des principales universités de recherche au monde. Située au cœur de la Silicon Valley, Stanford a été une force motrice derrière l\'innovation technologique et l\'entrepreneuriat.</p><p>L\'université propose une gamme diversifiée de programmes académiques et est connue pour sa recherche de pointe, son approche interdisciplinaire et ses liens solides avec l\'industrie.</p>',
                'mission' => '<p>Stanford University\'s mission is to promote the public welfare by exercising an influence in behalf of humanity and civilization. We seek to educate students for lives of leadership and contribution with integrity.</p>',
                'missionFr' => '<p>La mission de l\'Université Stanford est de promouvoir le bien-être public en exerçant une influence au nom de l\'humanité et de la civilisation. Nous cherchons à éduquer les étudiants pour des vies de leadership et de contribution avec intégrité.</p>',
                'foundedYear' => 1885,
                'students' => 17000,
                'language' => 'English',
                'worldRanking' => 2
            ]
        ];

        foreach ($establishmentsData as $slug => $data) {
            $establishment = $this->establishmentRepository->findOneBy(['slug' => $slug]);
            if ($establishment) {
                $establishment->setDescription($data['description']);
                $establishment->setDescriptionFr($data['descriptionFr']);
                $establishment->setMission($data['mission']);
                $establishment->setMissionFr($data['missionFr']);
                $establishment->setFoundedYear($data['foundedYear']);
                $establishment->setStudents($data['students']);
                $establishment->setLanguage($data['language']);
                $establishment->setWorldRanking($data['worldRanking']);

                $this->entityManager->persist($establishment);
                $io->success(sprintf('Updated %s mission and description data', $establishment->getName()));
            } else {
                $io->warning(sprintf('Establishment not found: %s', $slug));
            }
        }

        $this->entityManager->flush();

        $io->success('Successfully populated mission and description data for all establishments');

        return Command::SUCCESS;
    }
}
