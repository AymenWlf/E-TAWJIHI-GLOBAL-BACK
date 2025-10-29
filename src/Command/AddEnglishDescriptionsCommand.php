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
    name: 'app:add-english-descriptions',
    description: 'Add English descriptions for French business schools',
)]
class AddEnglishDescriptionsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Adding English Descriptions for French Business Schools');

        // Define English descriptions for each school
        $schoolsData = [
            'EM Lyon' => '<div class="school-description"><h2 class="text-2xl font-bold text-gray-900 mb-4">EM Lyon Business School</h2><div class="prose prose-lg max-w-none"><p class="text-gray-700 mb-4">Founded in 1872, <strong>EM Lyon Business School</strong> is one of the most prestigious business schools in France and Europe. Located in Écully, in the Lyon metropolitan area, the school embodies academic excellence and pedagogical innovation.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🏆 Excellence and Recognition</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Triple accreditation</strong>: AACSB, EQUIS, AMBA</li><li><strong>Ranked 15th</strong> in QS World University Rankings</li><li><strong>12th position</strong> in Times Higher Education</li><li><strong>18th global rank</strong> in general ranking</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🌍 International and Innovation</h3><p class="text-gray-700 mb-4">EM Lyon stands out for its international approach with <strong>7 campuses worldwide</strong> (Lyon, Paris, Shanghai, Bhubaneswar, Casablanca, Mumbai, Saint-Étienne) and more than <strong>190 partner universities</strong> in 50 countries.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎓 Excellence Programs</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Master in Management</strong> - School\'s flagship program</li><li><strong>MBA</strong> - Executive and International</li><li><strong>MSc Finance</strong> - Quantitative finance specialization</li><li><strong>MSc Marketing</strong> - Digital marketing and innovation</li><li><strong>MSc Digital Marketing</strong> - Digital transformation</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">💼 Entrepreneurship and Innovation</h3><p class="text-gray-700 mb-4">EM Lyon is recognized for its <strong>business incubator</strong> which has supported more than 1,200 startups. The school has a <strong>FabLab</strong> and a <strong>Learning Lab</strong> for pedagogical innovation.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎯 Career Prospects</h3><p class="text-gray-700 mb-4">EM Lyon graduates join the world\'s largest companies: <strong>McKinsey, BCG, Goldman Sachs, L\'Oréal, Danone, Total</strong>. <strong>95% of graduates</strong> find employment within 3 months of graduation.</p></div></div>',

            'SKEMA Business School' => '<div class="school-description"><h2 class="text-2xl font-bold text-gray-900 mb-4">SKEMA Business School</h2><div class="prose prose-lg max-w-none"><p class="text-gray-700 mb-4">Founded in 2009, <strong>SKEMA Business School</strong> is a leading French business school with a strong international presence. With campuses in France, China, Brazil, and the United States, SKEMA offers a truly global education experience.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🏆 Excellence and Recognition</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Triple accreditation</strong>: AACSB, EQUIS, AMBA</li><li><strong>Ranked 25th</strong> in QS World University Rankings</li><li><strong>Top 20</strong> in Financial Times European Business School Rankings</li><li><strong>Global network</strong> of 7 campuses worldwide</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🌍 Global Presence</h3><p class="text-gray-700 mb-4">SKEMA operates <strong>7 campuses globally</strong> (Lille, Paris, Sophia Antipolis, Suzhou, Belo Horizonte, Raleigh, Cape Town) and has partnerships with <strong>150+ universities</strong> in 50 countries, providing students with unparalleled international exposure.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎓 Excellence Programs</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Master in Management</strong> - Grande École program</li><li><strong>Global Executive MBA</strong> - International focus</li><li><strong>MSc International Business</strong> - Global business strategy</li><li><strong>MSc Digital Marketing</strong> - Digital transformation</li><li><strong>MSc Project and Program Management</strong> - Leadership skills</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">💼 Innovation and Entrepreneurship</h3><p class="text-gray-700 mb-4">SKEMA is known for its <strong>innovation ecosystem</strong> with dedicated labs for artificial intelligence, digital transformation, and sustainable development. The school supports student entrepreneurship through its <strong>SKEMA Ventures</strong> program.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎯 Career Prospects</h3><p class="text-gray-700 mb-4">SKEMA graduates are highly sought after by international companies: <strong>Amazon, Google, Microsoft, LVMH, Total, BNP Paribas</strong>. <strong>92% of graduates</strong> find employment within 6 months of graduation.</p></div></div>',

            'NEOMA Business School' => '<div class="school-description"><h2 class="text-2xl font-bold text-gray-900 mb-4">NEOMA Business School</h2><div class="prose prose-lg max-w-none"><p class="text-gray-700 mb-4">Founded in 2013 through the merger of Reims Management School and Rouen Business School, <strong>NEOMA Business School</strong> is a leading French business school with a strong focus on innovation and internationalization.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🏆 Excellence and Recognition</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Triple accreditation</strong>: AACSB, EQUIS, AMBA</li><li><strong>Ranked 35th</strong> in QS World University Rankings</li><li><strong>Top 30</strong> in Financial Times European Business School Rankings</li><li><strong>Strong research</strong> in management and business</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🌍 International Focus</h3><p class="text-gray-700 mb-4">NEOMA operates <strong>3 campuses in France</strong> (Reims, Rouen, Paris) and has partnerships with <strong>200+ universities</strong> worldwide. The school offers <strong>double degree programs</strong> with prestigious international institutions.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎓 Excellence Programs</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Master in Management</strong> - Grande École program</li><li><strong>Global MBA</strong> - International business focus</li><li><strong>MSc International Business</strong> - Global strategy</li><li><strong>MSc Marketing and Brand Management</strong> - Brand expertise</li><li><strong>MSc Finance and Banking</strong> - Financial services</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">💼 Innovation and Research</h3><p class="text-gray-700 mb-4">NEOMA is recognized for its <strong>research excellence</strong> with 6 research centers covering areas such as digital transformation, sustainable development, and international business. The school fosters innovation through its <strong>NEOMA Innovation Lab</strong>.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎯 Career Prospects</h3><p class="text-gray-700 mb-4">NEOMA graduates excel in various sectors: <strong>Consulting, Finance, Luxury, Technology, FMCG</strong>. <strong>94% of graduates</strong> find employment within 4 months of graduation, with an average starting salary of €45,000.</p></div></div>',

            'AUDENCIA Business School' => '<div class="school-description"><h2 class="text-2xl font-bold text-gray-900 mb-4">AUDENCIA Business School</h2><div class="prose prose-lg max-w-none"><p class="text-gray-700 mb-4">Founded in 1900, <strong>AUDENCIA Business School</strong> is one of France\'s oldest and most respected business schools. Located in Nantes, the school is known for its commitment to responsible management and sustainable business practices.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🏆 Excellence and Recognition</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Triple accreditation</strong>: AACSB, EQUIS, AMBA</li><li><strong>Ranked 40th</strong> in QS World University Rankings</li><li><strong>Top 50</strong> in Financial Times European Business School Rankings</li><li><strong>Pioneer</strong> in responsible management education</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🌍 International Network</h3><p class="text-gray-700 mb-4">AUDENCIA has <strong>3 campuses in France</strong> (Nantes, Paris, Beijing) and partnerships with <strong>180+ universities</strong> worldwide. The school offers <strong>exchange programs</strong> and <strong>double degrees</strong> with leading international institutions.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎓 Excellence Programs</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Master in Management</strong> - Grande École program</li><li><strong>Executive MBA</strong> - Leadership development</li><li><strong>MSc International Management</strong> - Global business</li><li><strong>MSc Marketing and Communication</strong> - Brand management</li><li><strong>MSc Supply Chain and Purchasing</strong> - Operations excellence</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">💼 Responsible Management</h3><p class="text-gray-700 mb-4">AUDENCIA is a <strong>pioneer in responsible management</strong> education, integrating sustainability and ethics into all programs. The school has been recognized by the UN Global Compact and is committed to the <strong>Sustainable Development Goals</strong>.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎯 Career Prospects</h3><p class="text-gray-700 mb-4">AUDENCIA graduates are valued for their <strong>responsible leadership</strong> skills: <strong>L\'Oréal, Danone, Schneider Electric, Capgemini, Accenture</strong>. <strong>93% of graduates</strong> find employment within 5 months of graduation.</p></div></div>',

            'IESEG School of Management' => '<div class="school-description"><h2 class="text-2xl font-bold text-gray-900 mb-4">IESEG School of Management</h2><div class="prose prose-lg max-w-none"><p class="text-gray-700 mb-4">Founded in 1964, <strong>IESEG School of Management</strong> is a leading French business school with a strong focus on academic excellence and internationalization. The school is part of the Catholic University of Lille and is known for its rigorous academic standards.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🏆 Excellence and Recognition</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Triple accreditation</strong>: AACSB, EQUIS, AMBA</li><li><strong>Ranked 45th</strong> in QS World University Rankings</li><li><strong>Top 40</strong> in Financial Times European Business School Rankings</li><li><strong>Strong academic</strong> reputation and research</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🌍 Global Perspective</h3><p class="text-gray-700 mb-4">IESEG operates <strong>2 campuses in France</strong> (Lille, Paris) and has partnerships with <strong>220+ universities</strong> worldwide. The school offers <strong>international exchange programs</strong> and <strong>double degree opportunities</strong> with prestigious institutions.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎓 Excellence Programs</h3><ul class="list-disc pl-6 text-gray-700 mb-4"><li><strong>Master in Management</strong> - Grande École program</li><li><strong>International MBA</strong> - Global business focus</li><li><strong>MSc International Business</strong> - International strategy</li><li><strong>MSc Finance</strong> - Financial markets and risk</li><li><strong>MSc Digital Marketing</strong> - Digital transformation</li></ul><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">💼 Academic Excellence</h3><p class="text-gray-700 mb-4">IESEG is known for its <strong>academic rigor</strong> and <strong>research excellence</strong>. The school has 5 research centers and publishes regularly in top-tier academic journals. Students benefit from <strong>small class sizes</strong> and <strong>personalized attention</strong>.</p><h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">🎯 Career Prospects</h3><p class="text-gray-700 mb-4">IESEG graduates are highly successful: <strong>Investment Banking, Consulting, Technology, Luxury, FMCG</strong>. <strong>96% of graduates</strong> find employment within 3 months of graduation, with strong career progression opportunities.</p></div></div>'
        ];

        $updated = 0;
        foreach ($schoolsData as $schoolName => $englishDescription) {
            $establishment = $this->entityManager->getRepository(Establishment::class)
                ->findOneBy(['name' => $schoolName]);

            if ($establishment) {
                $establishment->setDescriptionEn($englishDescription);

                $this->entityManager->persist($establishment);
                $updated++;

                $io->success("Added English description for: {$schoolName}");
            } else {
                $io->warning("School not found: {$schoolName}");
            }
        }

        $this->entityManager->flush();

        $io->success("Successfully added English descriptions for {$updated} French business schools.");

        return Command::SUCCESS;
    }
}











