<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-school-logos',
    description: 'Update school logos in the database',
)]
class UpdateSchoolLogosCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mise à jour des logos des écoles');

        // Mapping des logos
        $logoMapping = [
            // Grandes Écoles de Commerce
            'EM Lyon' => '/images/school_logos/logo-em-lyon.png',
            'SKEMA Business School' => '/images/school_logos/SKEMA_logo.png',
            'NEOMA Business School' => '/images/school_logos/NEOMA_logo.png',
            'AUDENCIA Business School' => '/images/school_logos/AUDENCIA_logo.png',
            'IESEG School of Management' => '/images/school_logos/IESEG_logo.jpeg',
            'TBS' => '/images/school_logos/TBS_logo.png',
            'EXCELIA' => '/images/school_logos/EXCELIA_logo.jpeg',
            'KEDGE' => '/images/school_logos/KEDGE_logo.webp',
            'MBS' => '/images/school_logos/MBS_logo.png',
            'EM STRASBOURG' => '/images/school_logos/EM_STRASBOURG_logo.png',
            'RENNES SB' => '/images/school_logos/RENNES_SB_logo.jpeg',
            'ICN' => '/images/school_logos/Logo_icn_business_school.png',
            'EM NORMANDIE' => '/images/school_logos/EM_NORMANDIE_logo.jpeg',
            'ESSCA' => '/images/school_logos/ESSCA_logo.jpeg',
            'ISC Paris' => '/images/school_logos/ISC_Paris_logo.png',
            'EDC' => '/images/school_logos/EDC_logo.png',
            'CLERMONT SB' => '/images/school_logos/CLERMONT_SB_logo.png',
            'PSB' => '/images/school_logos/PSB_logo.png',
            'EMLV' => '/images/school_logos/EMLV_logo.png',
            'ESCE' => '/images/school_logos/ESCE_logo.jpeg',

            // Écoles de Commerce
            'INSEEC GE' => '/images/school_logos/INSEEC_GE_logo.png',
            'EBS' => '/images/school_logos/EBS_logo.jpeg',
            'IPAG' => '/images/school_logos/IPAG_logo.png',
            'BREST BS' => '/images/school_logos/BREST_BS_logo.png',
            'EKLORE' => '/images/school_logos/EKLORE_logo.png',

            // Écoles d'Ingénierie
            'EFREI' => '/images/school_logos/EFREI_logo.jpeg',
            'CYTECH' => '/images/school_logos/CYTECH_logo.png',
            'ESILV' => '/images/school_logos/ESILV_logo.png',
            'ECE' => '/images/school_logos/ECE_logo.jpeg',

            // Écoles d'Informatique
            'HETIC' => '/images/school_logos/HETIC_logo.png',
            'ÉCOLE 89' => '/images/school_logos/ÉCOLE_89_logo.webp',
            'RED SUP' => '/images/school_logos/RED_SUP_logo.png',
            'AIVANCITY' => '/images/school_logos/AIVANCITY_logo.png',

            // Écoles de Communication
            'SUP DE PUB' => '/images/school_logos/SUP_DE_PUB_logo.jpeg',
            'NARRATIV' => '/images/school_logos/NARRATIV_logo.png',
            'ISCOM' => '/images/school_logos/ISCOM_logo.png',

            // Écoles Artistiques
            'LISAA' => '/images/school_logos/LISAA_logo.jpeg',
            'STRATE' => '/images/school_logos/STRATE_logo.png',
            'IESA' => '/images/school_logos/IESA_logo.png',
            'COURS FLORENT' => '/images/school_logos/COURS_FLORENT_logo.jpeg',

            // Écoles Spécialisées
            'INSEEC MSC' => '/images/school_logos/INSEEC_MSC_logo.webp',
            'ESG - PROVINCES' => '/images/school_logos/ESG_-_PROVINCES_logo.png',
            'INSEEC BACHELOR' => '/images/school_logos/INSEEC_BACHELOR_logo.png',
            'ESGCI' => '/images/school_logos/ESGCI_logo.png',
            'ESG FINANCE' => '/images/school_logos/ESG_FINANCE_logo.png',
            'IHECF' => '/images/school_logos/IHECF_logo.jpeg',
            'DIGITAL CAMPUS' => '/images/school_logos/DIGITAL_CAMPUS_logo.png',
            'MY DIGITAL SCHOOL' => '/images/school_logos/MY_DIGITAL_SCHOOL_logo.png',
            'PSTB' => '/images/school_logos/PSTB_logo.jpeg',
            'HEIP' => '/images/school_logos/HEIP_logo.png',
            'ELIJE' => '/images/school_logos/ELIJE_logo.png',
            'IIM' => '/images/school_logos/IIM_logo.png',
            'MBWAY' => '/images/school_logos/MBWAY_logo.png',
            '3IS' => '/images/school_logos/3IS_logo.png',
            'ESARC' => '/images/school_logos/ESARC_logo.png',
            'ESG SPORT' => '/images/school_logos/ESG_SPORT_logo.jpeg',
            'IPAC' => '/images/school_logos/IPAC_logo.png',
            'ESG IMMOBILIER' => '/images/school_logos/ESG_IMMOBILIER_logo.png',
            'FHL' => '/images/school_logos/FHL_logo.jpeg',
            'ESG TOURISME' => '/images/school_logos/ESG_TOURISME_logo.png',
            'ESG LUXE' => '/images/school_logos/ESG_LUXE_logo.png',
            'ESG RH' => '/images/school_logos/ESG_RH_logo.png',
            'MBA ESG' => '/images/school_logos/MBA_ESG_logo.png',
            'UCO' => '/images/school_logos/UCO_logo.png',
            'AD EDUCATION' => '/images/school_logos/AD_EDUCATION_logo.png',
            'ATELIER DE SÈVRES' => '/images/school_logos/ATELIER_DE_SÈVRES_logo.png',

            // Universités Internationales
            'Harvard University' => 'https://upload.wikimedia.org/wikipedia/en/thumb/2/29/Harvard_shield_wreath.svg/1200px-Harvard_shield_wreath.svg.png',
            'Stanford University' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b7/Stanford_University_seal_2003.svg/1200px-Stanford_University_seal_2003.svg.png',
            'University of Oxford' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/ff/Oxford_University_Coat_of_Arms.svg/1200px-Oxford_University_Coat_of_Arms.svg.png',
            'University of Tokyo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/University_of_Tokyo_logo.svg/1200px-University_of_Tokyo_logo.svg.png',
            'ETH Zurich' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/ETH_Zurich_logo.svg/1200px-ETH_Zurich_logo.svg.png',
            'University of Melbourne' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/8c/University_of_Melbourne_coat_of_arms.svg/1200px-University_of_Melbourne_coat_of_arms.svg.png',
            'McGill University' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/McGill_University_CoA.svg/1200px-McGill_University_CoA.svg.png',
            'University of Toronto' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/University_of_Toronto_crest.svg/1200px-University_of_Toronto_crest.svg.png',
            'Tsinghua University' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Tsinghua_University_Logo.svg/1200px-Tsinghua_University_Logo.svg.png',
            'École Polytechnique' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Logo_%C3%89cole_Polytechnique.svg/1200px-Logo_%C3%89cole_Polytechnique.svg.png',
            'Sorbonne University' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Sorbonne_University_logo.svg/1200px-Sorbonne_University_logo.svg.png'
        ];

        $updated = 0;
        $notFound = 0;

        $progressBar = $io->createProgressBar(count($logoMapping));
        $progressBar->start();

        foreach ($logoMapping as $schoolName => $logoPath) {
            try {
                $result = $this->entityManager->getConnection()->executeStatement(
                    'UPDATE establishments SET logo = ? WHERE name = ?',
                    [$logoPath, $schoolName]
                );

                if ($result > 0) {
                    $updated++;
                } else {
                    $notFound++;
                }
            } catch (\Exception $e) {
                $io->error("Erreur lors de la mise à jour de {$schoolName}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $io->newLine(2);

        $io->success("Mise à jour terminée !");
        $io->table(
            ['Métrique', 'Valeur'],
            [
                ['Écoles mises à jour', $updated],
                ['Écoles non trouvées', $notFound],
                ['Total traité', count($logoMapping)]
            ]
        );

        return Command::SUCCESS;
    }
}
