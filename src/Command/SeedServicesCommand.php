<?php

namespace App\Command;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-services',
    description: 'Seed the database with services data',
)]
class SeedServicesCommand extends Command
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

        // Clear existing services
        $this->entityManager->createQuery('DELETE FROM App\Entity\Service')->execute();

        $services = [
            [
                'name' => 'Complete Diagnostic System',
                'nameFr' => 'Système de Diagnostic Complet',
                'description' => 'Comprehensive analysis of your academic profile, career goals, and study preferences to provide personalized recommendations for your educational journey.',
                'descriptionFr' => 'Analyse complète de votre profil académique, objectifs de carrière et préférences d\'études pour fournir des recommandations personnalisées pour votre parcours éducatif.',
                'price' => 50.00,
                'currency' => 'USD',
                'category' => 'diagnostic',
                'targetCountries' => [],
                'features' => [
                    'Detailed academic profile analysis',
                    'Career path recommendations',
                    'University matching algorithm',
                    'Downloadable comprehensive report',
                    'Personalized study plan'
                ],
                'featuresFr' => [
                    'Analyse détaillée du profil académique',
                    'Recommandations de parcours de carrière',
                    'Algorithme de correspondance universitaire',
                    'Rapport complet téléchargeable',
                    'Plan d\'études personnalisé'
                ],
                'icon' => '🔍',
                'color' => 'bg-blue-500',
                'duration' => 24,
                'durationUnit' => 'hours'
            ],
            [
                'name' => 'TASSJIL Service',
                'nameFr' => 'Service TASSJIL',
                'description' => 'Complete assistance for registration in Moroccan public, private, and semi-public schools. Includes application guidance and mobile app for tracking.',
                'descriptionFr' => 'Assistance complète pour l\'inscription dans les écoles marocaines publiques, privées et semi-publiques. Inclut l\'orientation des candidatures et l\'application mobile pour le suivi.',
                'price' => 2300.00,
                'currency' => 'MAD',
                'category' => 'morocco',
                'targetCountries' => ['Morocco'],
                'features' => [
                    'Application form assistance',
                    'Document verification',
                    'Mobile app for tracking',
                    'Priority support',
                    'Registration deadline alerts'
                ],
                'featuresFr' => [
                    'Assistance aux formulaires de candidature',
                    'Vérification des documents',
                    'Application mobile pour le suivi',
                    'Support prioritaire',
                    'Alertes de délais d\'inscription'
                ],
                'icon' => '🎓',
                'color' => 'bg-red-500',
                'duration' => 7,
                'durationUnit' => 'days'
            ],
            [
                'name' => 'TASSJIL TOP 15',
                'nameFr' => 'TASSJIL TOP 15',
                'description' => 'Premium service for registration in Morocco\'s top 15 schools. Includes priority processing and exclusive support.',
                'descriptionFr' => 'Service premium pour l\'inscription dans les 15 meilleures écoles du Maroc. Inclut le traitement prioritaire et le support exclusif.',
                'price' => 1800.00,
                'currency' => 'MAD',
                'category' => 'morocco',
                'targetCountries' => ['Morocco'],
                'features' => [
                    'Top 15 schools access',
                    'Priority processing',
                    'Exclusive support',
                    'Success guarantee',
                    'Mobile app premium features'
                ],
                'featuresFr' => [
                    'Accès aux 15 meilleures écoles',
                    'Traitement prioritaire',
                    'Support exclusif',
                    'Garantie de succès',
                    'Fonctionnalités premium de l\'app mobile'
                ],
                'icon' => '⭐',
                'color' => 'bg-yellow-500',
                'duration' => 5,
                'durationUnit' => 'days'
            ],
            [
                'name' => 'TAWJIH PLUS',
                'nameFr' => 'TAWJIH PLUS',
                'description' => 'Mobile application that sends notifications for Moroccan opportunities and international scholarships for Moroccans (Bac to Doctorate).',
                'descriptionFr' => 'Application mobile qui envoie des notifications pour les opportunités marocaines et les bourses internationales pour les Marocains (du Bac au Doctorat).',
                'price' => 500.00,
                'currency' => 'MAD',
                'category' => 'morocco',
                'targetCountries' => ['Morocco'],
                'features' => [
                    'Real-time notifications',
                    'Scholarship alerts',
                    'Opportunity tracking',
                    'Bac to Doctorate coverage',
                    'Mobile app access'
                ],
                'featuresFr' => [
                    'Notifications en temps réel',
                    'Alertes de bourses',
                    'Suivi des opportunités',
                    'Couverture du Bac au Doctorat',
                    'Accès à l\'application mobile'
                ],
                'icon' => '📱',
                'color' => 'bg-green-500',
                'duration' => 1,
                'durationUnit' => 'day'
            ],
            [
                'name' => 'CAMPUS FRANCE',
                'nameFr' => 'CAMPUS FRANCE',
                'description' => 'Complete assistance for registration in French public schools through the Campus France procedure for all countries with Campus France.',
                'descriptionFr' => 'Assistance complète pour l\'inscription dans les écoles publiques françaises via la procédure Campus France pour tous les pays disposant de Campus France.',
                'price' => 3500.00,
                'currency' => 'MAD',
                'category' => 'france',
                'targetCountries' => ['Morocco', 'Algeria', 'Tunisia', 'Senegal', 'Ivory Coast', 'Cameroon', 'Mali', 'Burkina Faso', 'Niger', 'Chad', 'Madagascar', 'Mauritius', 'Lebanon', 'Syria', 'Jordan', 'Egypt', 'Libya'],
                'features' => [
                    'Campus France procedure guidance',
                    'Document preparation',
                    'Application assistance',
                    'Interview preparation',
                    'Visa support'
                ],
                'featuresFr' => [
                    'Orientation de la procédure Campus France',
                    'Préparation des documents',
                    'Assistance aux candidatures',
                    'Préparation aux entretiens',
                    'Support visa'
                ],
                'icon' => '🇫🇷',
                'color' => 'bg-blue-600',
                'duration' => 14,
                'durationUnit' => 'days'
            ],
            [
                'name' => 'Parcoursup',
                'nameFr' => 'Parcoursup',
                'description' => 'Assistance for CPGE and BTS applications in France for all countries with Campus France.',
                'descriptionFr' => 'Assistance pour les candidatures CPGE et BTS en France pour tous les pays disposant de Campus France.',
                'price' => 2500.00,
                'currency' => 'MAD',
                'category' => 'france',
                'targetCountries' => ['Morocco', 'Algeria', 'Tunisia', 'Senegal', 'Ivory Coast', 'Cameroon', 'Mali', 'Burkina Faso', 'Niger', 'Chad', 'Madagascar', 'Mauritius', 'Lebanon', 'Syria', 'Jordan', 'Egypt', 'Libya'],
                'features' => [
                    'Parcoursup platform guidance',
                    'CPGE application support',
                    'BTS application support',
                    'Document verification',
                    'Application tracking'
                ],
                'featuresFr' => [
                    'Orientation de la plateforme Parcoursup',
                    'Support candidature CPGE',
                    'Support candidature BTS',
                    'Vérification des documents',
                    'Suivi des candidatures'
                ],
                'icon' => '📚',
                'color' => 'bg-indigo-500',
                'duration' => 10,
                'durationUnit' => 'days'
            ],
            [
                'name' => 'Student Visa Assistance',
                'nameFr' => 'Service d\'accompagnement visa étudiant',
                'description' => 'Complete assistance for student visa applications to any study destination worldwide.',
                'descriptionFr' => 'Assistance complète pour les demandes de visa étudiant vers toute destination d\'études dans le monde.',
                'price' => 4000.00,
                'currency' => 'MAD',
                'category' => 'international',
                'targetCountries' => [],
                'features' => [
                    'Visa application guidance',
                    'Document preparation',
                    'Interview preparation',
                    'Embassy liaison',
                    'Application tracking'
                ],
                'featuresFr' => [
                    'Orientation de la demande de visa',
                    'Préparation des documents',
                    'Préparation aux entretiens',
                    'Liaison avec l\'ambassade',
                    'Suivi de la candidature'
                ],
                'icon' => '✈️',
                'color' => 'bg-purple-500',
                'duration' => 21,
                'durationUnit' => 'days'
            ],
            [
                'name' => 'Housing & Guarantor Service',
                'nameFr' => 'Service Logement et Garant',
                'description' => 'Assistance in finding suitable housing and guarantor services based on your study destination.',
                'descriptionFr' => 'Assistance pour trouver un logement adapté et des services de garant selon votre destination d\'études.',
                'price' => 4000.00,
                'currency' => 'MAD',
                'category' => 'international',
                'targetCountries' => [],
                'features' => [
                    'Housing search assistance',
                    'Guarantor services',
                    'Legal document support',
                    'Local network access',
                    'Ongoing support'
                ],
                'featuresFr' => [
                    'Assistance à la recherche de logement',
                    'Services de garant',
                    'Support documents légaux',
                    'Accès au réseau local',
                    'Support continu'
                ],
                'icon' => '🏠',
                'color' => 'bg-orange-500',
                'duration' => 30,
                'durationUnit' => 'days'
            ],
            [
                'name' => 'Official Document Translation',
                'nameFr' => 'Traduction de Documents Officiels',
                'description' => 'Professional translation of official documents with variable pricing based on document type, language pair, and number of pages.',
                'descriptionFr' => 'Traduction professionnelle de documents officiels avec tarification variable selon le type de document, la paire de langues et le nombre de pages.',
                'price' => 0.00,
                'currency' => 'MAD',
                'category' => 'translation',
                'targetCountries' => [],
                'features' => [
                    'Professional translation',
                    'Variable pricing',
                    '48-hour delivery',
                    'Certified translation',
                    'Multiple language pairs'
                ],
                'featuresFr' => [
                    'Traduction professionnelle',
                    'Tarification variable',
                    'Livraison 48h',
                    'Traduction certifiée',
                    'Plusieurs paires de langues'
                ],
                'icon' => '📄',
                'color' => 'bg-purple-600',
                'duration' => 2,
                'durationUnit' => 'days'
            ]
        ];

        foreach ($services as $serviceData) {
            $service = new Service();
            $service->setName($serviceData['name']);
            $service->setNameFr($serviceData['nameFr']);
            $service->setDescription($serviceData['description']);
            $service->setDescriptionFr($serviceData['descriptionFr']);
            $service->setPrice($serviceData['price']);
            $service->setCurrency($serviceData['currency']);
            $service->setCategory($serviceData['category']);
            $service->setTargetCountries($serviceData['targetCountries']);
            $service->setFeatures($serviceData['features']);
            $service->setFeaturesFr($serviceData['featuresFr']);
            $service->setIcon($serviceData['icon']);
            $service->setColor($serviceData['color']);
            $service->setDuration($serviceData['duration']);
            $service->setDurationUnit($serviceData['durationUnit']);
            $service->setIsActive(true);

            $this->entityManager->persist($service);
        }

        $this->entityManager->flush();

        $io->success('Services have been successfully seeded to the database!');
        $io->note('Total services created: ' . count($services));

        return Command::SUCCESS;
    }
}
