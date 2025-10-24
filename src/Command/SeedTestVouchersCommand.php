<?php

namespace App\Command;

use App\Entity\TestVoucher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-test-vouchers',
    description: 'Seed test vouchers data',
)]
class SeedTestVouchersCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Clear existing test vouchers
        $existingVouchers = $this->entityManager->getRepository(TestVoucher::class)->findAll();
        foreach ($existingVouchers as $voucher) {
            $this->entityManager->remove($voucher);
        }
        $this->entityManager->flush();

        $testVouchers = [
            [
                'name' => 'Pearson Test of English (PTE)',
                'nameFr' => 'Test d\'Anglais Pearson (PTE)',
                'vendor' => 'Pearson',
                'vendorLogo' => '/images/vendors/pearson.svg',
                'originalPrice' => 240.00,
                'discountedPrice' => 209.00,
                'currency' => 'USD',
                'category' => 'english',
                'status' => 'sold_out',
                'description' => 'Pick the world-leading Pearson Test of English (PTE), a secure computer-based test accepted worldwide.',
                'descriptionFr' => 'Choisissez le Test d\'Anglais Pearson (PTE) de renommée mondiale, un test sécurisé sur ordinateur accepté dans le monde entier.',
                'recognition' => 'Accepted by thousands of universities, colleges and governments around the world including Australia, New Zealand, UK, Canada, and USA.',
                'recognitionFr' => 'Accepté par des milliers d\'universités, collèges et gouvernements dans le monde entier, notamment en Australie, Nouvelle-Zélande, Royaume-Uni, Canada et États-Unis.',
                'features' => [
                    'Computer-based test with AI scoring',
                    'Results in 2-5 business days',
                    'Accepted by 3,000+ institutions',
                    'Available in 50+ countries',
                    'Flexible test dates'
                ],
                'featuresFr' => [
                    'Test sur ordinateur avec notation IA',
                    'Résultats en 2-5 jours ouvrables',
                    'Accepté par 3 000+ institutions',
                    'Disponible dans 50+ pays',
                    'Dates de test flexibles'
                ],
                'validity' => 'Valid for 2 years from test date',
                'validityFr' => 'Valide pendant 2 ans à partir de la date du test',
                'shareLink' => '#',
                'icon' => '📝',
                'color' => 'bg-blue-600',
                'sortOrder' => 1
            ],
            [
                'name' => 'Test of English as a Foreign Language (TOEFL)',
                'nameFr' => 'Test d\'Anglais comme Langue Étrangère (TOEFL)',
                'vendor' => 'ETS',
                'vendorLogo' => '/images/vendors/ets.svg',
                'originalPrice' => 275.00,
                'discountedPrice' => 247.50,
                'currency' => 'USD',
                'category' => 'english',
                'status' => 'available',
                'description' => 'Choose the Test of English as a Foreign Language (TOEFL) accepted by 11,000+ institutions globally.',
                'descriptionFr' => 'Choisissez le Test d\'Anglais comme Langue Étrangère (TOEFL) accepté par plus de 11 000 institutions dans le monde.',
                'recognition' => 'The most widely accepted English-language test in the world, recognized by universities, immigration departments, and professional organizations in 160+ countries.',
                'recognitionFr' => 'Le test d\'anglais le plus largement accepté au monde, reconnu par les universités, les services d\'immigration et les organisations professionnelles dans plus de 160 pays.',
                'features' => [
                    'Internet-based test (iBT)',
                    'Results in 6-10 days',
                    'Accepted by 11,000+ institutions',
                    'Available in 190+ countries',
                    '4 sections: Reading, Listening, Speaking, Writing'
                ],
                'featuresFr' => [
                    'Test basé sur Internet (iBT)',
                    'Résultats en 6-10 jours',
                    'Accepté par 11 000+ institutions',
                    'Disponible dans 190+ pays',
                    '4 sections : Lecture, Écoute, Expression orale, Rédaction'
                ],
                'validity' => 'Valid for 2 years from test date',
                'validityFr' => 'Valide pendant 2 ans à partir de la date du test',
                'buyLink' => 'https://www.ets.org/toefl',
                'icon' => '🌍',
                'color' => 'bg-red-600',
                'sortOrder' => 2
            ],
            [
                'name' => 'Duolingo English Test',
                'nameFr' => 'Test d\'Anglais Duolingo',
                'vendor' => 'Duolingo',
                'vendorLogo' => '/images/vendors/duolingo.svg',
                'originalPrice' => 70.00,
                'discountedPrice' => 57.00,
                'currency' => 'USD',
                'category' => 'english',
                'status' => 'available',
                'description' => 'The convenient, fast, and affordable English test trusted by 4,500+ institutions around the world.',
                'descriptionFr' => 'Le test d\'anglais pratique, rapide et abordable, approuvé par plus de 4 500 institutions dans le monde.',
                'recognition' => 'Accepted by leading universities including Yale, Duke, Columbia, and many others. Also accepted for immigration purposes in several countries.',
                'recognitionFr' => 'Accepté par des universités de premier plan comme Yale, Duke, Columbia et bien d\'autres. Également accepté à des fins d\'immigration dans plusieurs pays.',
                'features' => [
                    'Take from home',
                    'Results in 48 hours',
                    'Accepted by 4,500+ institutions',
                    'Only $49 USD',
                    '1-hour test duration'
                ],
                'featuresFr' => [
                    'Passer depuis chez soi',
                    'Résultats en 48 heures',
                    'Accepté par 4 500+ institutions',
                    'Seulement 49 $ USD',
                    'Durée du test : 1 heure'
                ],
                'validity' => 'Valid for 2 years from test date',
                'validityFr' => 'Valide pendant 2 ans à partir de la date du test',
                'buyLink' => 'https://englishtest.duolingo.com',
                'icon' => '🦉',
                'color' => 'bg-green-600',
                'sortOrder' => 3
            ],
            [
                'name' => 'Graduate Record Examination (GRE)',
                'nameFr' => 'Examen d\'Enregistrement des Diplômés (GRE)',
                'vendor' => 'ETS',
                'vendorLogo' => '/images/vendors/ets.svg',
                'originalPrice' => 220.00,
                'discountedPrice' => 209.00,
                'currency' => 'USD',
                'category' => 'graduate',
                'status' => 'available',
                'description' => 'The ETS GRE (Graduate Record Examination) is the globally recognized, gold-standard admissions test trusted by top graduate, business, and law schools.',
                'descriptionFr' => 'Le GRE ETS (Examen d\'Enregistrement des Diplômés) est le test d\'admission reconnu mondialement, étalon-or, approuvé par les meilleures écoles de cycle supérieur, de commerce et de droit.',
                'recognition' => 'Accepted by thousands of graduate and business schools worldwide, including top universities like Harvard, MIT, Stanford, and many others.',
                'recognitionFr' => 'Accepté par des milliers d\'écoles de cycle supérieur et de commerce dans le monde, y compris les meilleures universités comme Harvard, MIT, Stanford et bien d\'autres.',
                'features' => [
                    'Computer-based test',
                    'Results in 10-15 days',
                    'Accepted by 1,300+ institutions',
                    'Available in 160+ countries',
                    'Valid for 5 years'
                ],
                'featuresFr' => [
                    'Test sur ordinateur',
                    'Résultats en 10-15 jours',
                    'Accepté par 1 300+ institutions',
                    'Disponible dans 160+ pays',
                    'Valide pendant 5 ans'
                ],
                'validity' => 'Valid for 5 years from test date',
                'validityFr' => 'Valide pendant 5 ans à partir de la date du test',
                'buyLink' => 'https://www.ets.org/gre',
                'icon' => '🎓',
                'color' => 'bg-purple-600',
                'sortOrder' => 4
            ]
        ];

        foreach ($testVouchers as $voucherData) {
            $voucher = new TestVoucher();
            $voucher->setName($voucherData['name']);
            $voucher->setNameFr($voucherData['nameFr']);
            $voucher->setVendor($voucherData['vendor']);
            $voucher->setVendorLogo($voucherData['vendorLogo']);
            $voucher->setOriginalPrice($voucherData['originalPrice']);
            $voucher->setDiscountedPrice($voucherData['discountedPrice']);
            $voucher->setCurrency($voucherData['currency']);
            $voucher->setCategory($voucherData['category']);
            $voucher->setStatus($voucherData['status']);
            $voucher->setDescription($voucherData['description']);
            $voucher->setDescriptionFr($voucherData['descriptionFr']);
            $voucher->setRecognition($voucherData['recognition']);
            $voucher->setRecognitionFr($voucherData['recognitionFr']);
            $voucher->setFeatures($voucherData['features']);
            $voucher->setFeaturesFr($voucherData['featuresFr']);
            $voucher->setValidity($voucherData['validity']);
            $voucher->setValidityFr($voucherData['validityFr']);
            $voucher->setShareLink($voucherData['shareLink'] ?? null);
            $voucher->setBuyLink($voucherData['buyLink'] ?? null);
            $voucher->setIcon($voucherData['icon']);
            $voucher->setColor($voucherData['color']);
            $voucher->setIsActive(true);
            $voucher->setSortOrder($voucherData['sortOrder']);

            $this->entityManager->persist($voucher);
        }

        $this->entityManager->flush();

        $io->success('Test vouchers seeded successfully!');
        return Command::SUCCESS;
    }
}
