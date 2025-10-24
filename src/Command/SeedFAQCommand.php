<?php

namespace App\Command;

use App\Entity\FAQ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-faqs',
    description: 'Seeds comprehensive FAQ data inspired by the E-TAWJIHI showcase website.',
)]
class SeedFAQCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $faqsData = [
            // General Questions
            [
                'category' => 'general',
                'question' => 'What is E-TAWJIHI?',
                'questionFr' => 'Qu\'est-ce qu\'E-TAWJIHI ?',
                'answer' => 'E-TAWJIHI is a comprehensive educational platform that provides guidance and services for students seeking to study abroad, particularly in France and other international destinations. We offer personalized counseling, document processing, test preparation, and application support to help students achieve their academic goals.',
                'answerFr' => 'E-TAWJIHI est une plateforme éducative complète qui fournit des conseils et des services aux étudiants souhaitant étudier à l\'étranger, particulièrement en France et dans d\'autres destinations internationales. Nous offrons des conseils personnalisés, le traitement de documents, la préparation aux tests et le support de candidature pour aider les étudiants à atteindre leurs objectifs académiques.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '🎓',
                'color' => 'bg-blue-500'
            ],
            [
                'category' => 'general',
                'question' => 'How can E-TAWJIHI help me with my studies?',
                'questionFr' => 'Comment E-TAWJIHI peut-il m\'aider dans mes études ?',
                'answer' => 'E-TAWJIHI provides comprehensive support including: academic counseling, university selection, application assistance, document translation, test preparation, visa guidance, and ongoing support throughout your academic journey. Our experienced counselors work with you to create a personalized study plan.',
                'answerFr' => 'E-TAWJIHI fournit un support complet incluant : conseils académiques, sélection d\'universités, assistance aux candidatures, traduction de documents, préparation aux tests, guidance pour les visas, et support continu tout au long de votre parcours académique. Nos conseillers expérimentés travaillent avec vous pour créer un plan d\'études personnalisé.',
                'sortOrder' => 20,
                'isPopular' => true,
                'icon' => '🚀',
                'color' => 'bg-blue-500'
            ],
            [
                'category' => 'general',
                'question' => 'What countries do you provide services for?',
                'questionFr' => 'Pour quels pays fournissez-vous des services ?',
                'answer' => 'We specialize in France (Campus France procedures) and Morocco (TASSJIL services), but we also provide guidance for other international destinations including Canada, Germany, the UK, and other European countries. Our services are tailored to each destination\'s specific requirements.',
                'answerFr' => 'Nous nous spécialisons en France (procédures Campus France) et au Maroc (services TASSJIL), mais nous fournissons aussi des conseils pour d\'autres destinations internationales incluant le Canada, l\'Allemagne, le Royaume-Uni, et d\'autres pays européens. Nos services sont adaptés aux exigences spécifiques de chaque destination.',
                'sortOrder' => 30,
                'isPopular' => false,
                'icon' => '🌍',
                'color' => 'bg-blue-500'
            ],

            // Account & Profile
            [
                'category' => 'account',
                'question' => 'How do I create an account?',
                'questionFr' => 'Comment créer un compte ?',
                'answer' => 'Creating an account is simple: 1) Click on "Register" on our homepage, 2) Fill in your basic information (name, email, phone), 3) Verify your email address, 4) Complete your profile with academic information. Your account will be activated immediately and you can start using our services.',
                'answerFr' => 'Créer un compte est simple : 1) Cliquez sur "S\'inscrire" sur notre page d\'accueil, 2) Remplissez vos informations de base (nom, email, téléphone), 3) Vérifiez votre adresse email, 4) Complétez votre profil avec vos informations académiques. Votre compte sera activé immédiatement et vous pourrez commencer à utiliser nos services.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '👤',
                'color' => 'bg-green-500'
            ],
            [
                'category' => 'account',
                'question' => 'How do I update my profile information?',
                'questionFr' => 'Comment mettre à jour les informations de mon profil ?',
                'answer' => 'To update your profile: 1) Log into your account, 2) Go to "Basic Info" section, 3) Click "Edit" to modify your information, 4) Update your details (personal info, academic background, preferences), 5) Click "Save" to confirm changes. All changes are saved automatically.',
                'answerFr' => 'Pour mettre à jour votre profil : 1) Connectez-vous à votre compte, 2) Allez à la section "Informations de Base", 3) Cliquez sur "Modifier" pour modifier vos informations, 4) Mettez à jour vos détails (infos personnelles, parcours académique, préférences), 5) Cliquez sur "Sauvegarder" pour confirmer les changements. Tous les changements sont sauvegardés automatiquement.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '✏️',
                'color' => 'bg-green-500'
            ],
            [
                'category' => 'account',
                'question' => 'I forgot my password. How can I reset it?',
                'questionFr' => 'J\'ai oublié mon mot de passe. Comment le réinitialiser ?',
                'answer' => 'To reset your password: 1) Go to the login page, 2) Click "Forgot Password?", 3) Enter your email address, 4) Check your email for reset instructions, 5) Follow the link to create a new password. If you don\'t receive the email, check your spam folder or contact our support team.',
                'answerFr' => 'Pour réinitialiser votre mot de passe : 1) Allez à la page de connexion, 2) Cliquez sur "Mot de passe oublié ?", 3) Entrez votre adresse email, 4) Vérifiez votre email pour les instructions de réinitialisation, 5) Suivez le lien pour créer un nouveau mot de passe. Si vous ne recevez pas l\'email, vérifiez votre dossier spam ou contactez notre équipe de support.',
                'sortOrder' => 30,
                'isPopular' => true,
                'icon' => '🔐',
                'color' => 'bg-green-500'
            ],

            // Services
            [
                'category' => 'services',
                'question' => 'What services does E-TAWJIHI offer?',
                'questionFr' => 'Quels services E-TAWJIHI offre-t-il ?',
                'answer' => 'Our services include: 1) Complete Diagnostic System - Comprehensive academic assessment, 2) TASSJIL Services - Moroccan school registration assistance, 3) TAWJIH PLUS - Mobile app for opportunities and scholarships, 4) Campus France - French university application support, 5) Parcoursup - CPGE and BTS applications, 6) Student Visa Support, 7) Document Translation Services.',
                'answerFr' => 'Nos services incluent : 1) Système de Diagnostic Complet - Évaluation académique complète, 2) Services TASSJIL - Assistance à l\'inscription dans les écoles marocaines, 3) TAWJIH PLUS - Application mobile pour les opportunités et bourses, 4) Campus France - Support aux candidatures universitaires françaises, 5) Parcoursup - Candidatures CPGE et BTS, 6) Support Visa Étudiant, 7) Services de Traduction de Documents.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '🎯',
                'color' => 'bg-purple-500'
            ],
            [
                'category' => 'services',
                'question' => 'How much do your services cost?',
                'questionFr' => 'Combien coûtent vos services ?',
                'answer' => 'Our pricing varies by service: Complete Diagnostic System ($50), TASSJIL Service (2,300 MAD), TASSJIL TOP 15 (1,800 MAD), TAWJIH PLUS (500 MAD), Campus France (3,500 MAD), Student Visa Support (4,000 MAD), Document Translation (variable pricing). All prices are in local currency and include full support throughout the process.',
                'answerFr' => 'Nos tarifs varient selon le service : Système de Diagnostic Complet (50$), Service TASSJIL (2 300 MAD), TASSJIL TOP 15 (1 800 MAD), TAWJIH PLUS (500 MAD), Campus France (3 500 MAD), Support Visa Étudiant (4 000 MAD), Traduction de Documents (tarifs variables). Tous les prix sont en devise locale et incluent un support complet tout au long du processus.',
                'sortOrder' => 20,
                'isPopular' => true,
                'icon' => '💰',
                'color' => 'bg-purple-500'
            ],
            [
                'category' => 'services',
                'question' => 'What is the Complete Diagnostic System?',
                'questionFr' => 'Qu\'est-ce que le Système de Diagnostic Complet ?',
                'answer' => 'The Complete Diagnostic System is our comprehensive academic assessment service that evaluates your academic profile, identifies your strengths and areas for improvement, recommends suitable universities and programs, and creates a personalized study plan. It includes a detailed report with actionable recommendations and a roadmap for your academic journey.',
                'answerFr' => 'Le Système de Diagnostic Complet est notre service d\'évaluation académique complet qui évalue votre profil académique, identifie vos forces et domaines d\'amélioration, recommande des universités et programmes appropriés, et crée un plan d\'études personnalisé. Il inclut un rapport détaillé avec des recommandations actionables et une feuille de route pour votre parcours académique.',
                'sortOrder' => 30,
                'isPopular' => false,
                'icon' => '📊',
                'color' => 'bg-purple-500'
            ],

            // Documents
            [
                'category' => 'documents',
                'question' => 'What documents do I need to upload?',
                'questionFr' => 'Quels documents dois-je télécharger ?',
                'answer' => 'Required documents include: Identity documents (Passport, National ID), Academic certificates (Secondary School, High School, University transcripts), English test results (IELTS, TOEFL, Duolingo), CV/Resume, Statement of Purpose, Letters of Recommendation, Financial documents, and any other documents specific to your chosen program or country.',
                'answerFr' => 'Les documents requis incluent : Documents d\'identité (Passeport, Carte d\'identité nationale), Certificats académiques (École secondaire, Lycée, Relevés universitaires), Résultats de tests d\'anglais (IELTS, TOEFL, Duolingo), CV, Lettre de motivation, Lettres de recommandation, Documents financiers, et tout autre document spécifique à votre programme ou pays choisi.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '📄',
                'color' => 'bg-orange-500'
            ],
            [
                'category' => 'documents',
                'question' => 'How do I upload documents to my profile?',
                'questionFr' => 'Comment télécharger des documents sur mon profil ?',
                'answer' => 'To upload documents: 1) Go to the "Documents" section in your profile, 2) Click "Add Document", 3) Select the document category and type, 4) Choose your file (PDF, JPG, PNG formats accepted), 5) Add any additional information, 6) Click "Upload". Your document will be processed and validated by our team.',
                'answerFr' => 'Pour télécharger des documents : 1) Allez à la section "Documents" dans votre profil, 2) Cliquez sur "Ajouter un Document", 3) Sélectionnez la catégorie et le type de document, 4) Choisissez votre fichier (formats PDF, JPG, PNG acceptés), 5) Ajoutez toute information supplémentaire, 6) Cliquez sur "Télécharger". Votre document sera traité et validé par notre équipe.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '📤',
                'color' => 'bg-orange-500'
            ],
            [
                'category' => 'documents',
                'question' => 'What is the document validation process?',
                'questionFr' => 'Quel est le processus de validation des documents ?',
                'answer' => 'Our document validation process includes: 1) Initial review for completeness and format, 2) Verification of document authenticity, 3) Translation if required, 4) Quality check and formatting, 5) Final approval by our team. Documents are typically processed within 2-3 business days. You\'ll receive notifications about the status of your documents.',
                'answerFr' => 'Notre processus de validation des documents inclut : 1) Révision initiale pour la complétude et le format, 2) Vérification de l\'authenticité du document, 3) Traduction si nécessaire, 4) Contrôle qualité et formatage, 5) Approbation finale par notre équipe. Les documents sont généralement traités dans les 2-3 jours ouvrables. Vous recevrez des notifications sur le statut de vos documents.',
                'sortOrder' => 30,
                'isPopular' => false,
                'icon' => '✅',
                'color' => 'bg-orange-500'
            ],

            // Translations
            [
                'category' => 'translations',
                'question' => 'What languages do you translate documents to/from?',
                'questionFr' => 'Vers/quelles langues traduisez-vous les documents ?',
                'answer' => 'We provide professional translation services between Arabic, French, English, and Spanish. Our certified translators specialize in academic documents, official certificates, transcripts, and legal documents. We ensure accuracy and maintain the original formatting of your documents.',
                'answerFr' => 'Nous fournissons des services de traduction professionnels entre l\'arabe, le français, l\'anglais et l\'espagnol. Nos traducteurs certifiés se spécialisent dans les documents académiques, certificats officiels, relevés de notes et documents légaux. Nous garantissons la précision et maintenons le formatage original de vos documents.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '🌐',
                'color' => 'bg-indigo-500'
            ],
            [
                'category' => 'translations',
                'question' => 'How long does document translation take?',
                'questionFr' => 'Combien de temps prend la traduction de documents ?',
                'answer' => 'Translation time depends on document length and complexity: Simple documents (1-2 pages): 24-48 hours, Standard documents (3-10 pages): 2-3 business days, Complex documents (10+ pages): 3-5 business days. Rush services are available for urgent requests with additional fees.',
                'answerFr' => 'Le temps de traduction dépend de la longueur et complexité du document : Documents simples (1-2 pages) : 24-48 heures, Documents standards (3-10 pages) : 2-3 jours ouvrables, Documents complexes (10+ pages) : 3-5 jours ouvrables. Des services express sont disponibles pour les demandes urgentes avec des frais supplémentaires.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '⏰',
                'color' => 'bg-indigo-500'
            ],
            [
                'category' => 'translations',
                'question' => 'How is translation pricing calculated?',
                'questionFr' => 'Comment le prix de traduction est-il calculé ?',
                'answer' => 'Translation pricing is based on: 1) Document type (academic, legal, technical), 2) Language pair (common pairs are less expensive), 3) Number of pages, 4) Complexity and technical content, 5) Urgency level. We provide transparent pricing with no hidden fees. You can get an instant quote using our translation calculator.',
                'answerFr' => 'Le prix de traduction est basé sur : 1) Type de document (académique, légal, technique), 2) Paire de langues (les paires communes sont moins chères), 3) Nombre de pages, 4) Complexité et contenu technique, 5) Niveau d\'urgence. Nous fournissons des prix transparents sans frais cachés. Vous pouvez obtenir un devis instantané en utilisant notre calculateur de traduction.',
                'sortOrder' => 30,
                'isPopular' => false,
                'icon' => '💵',
                'color' => 'bg-indigo-500'
            ],

            // Test Vouchers
            [
                'category' => 'tests',
                'question' => 'What test vouchers do you offer?',
                'questionFr' => 'Quels vouchers de test offrez-vous ?',
                'answer' => 'We offer vouchers for: PTE (Pearson Test of English), TOEFL (Test of English as a Foreign Language), Duolingo English Test, GRE (Graduate Record Examination). All vouchers are official and can be used at authorized test centers worldwide. We provide discounted rates and full support throughout the testing process.',
                'answerFr' => 'Nous offrons des vouchers pour : PTE (Pearson Test of English), TOEFL (Test of English as a Foreign Language), Duolingo English Test, GRE (Graduate Record Examination). Tous les vouchers sont officiels et peuvent être utilisés dans les centres de test autorisés dans le monde entier. Nous fournissons des tarifs réduits et un support complet tout au long du processus de test.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '📝',
                'color' => 'bg-red-500'
            ],
            [
                'category' => 'tests',
                'question' => 'How do I purchase a test voucher?',
                'questionFr' => 'Comment acheter un voucher de test ?',
                'answer' => 'To purchase a test voucher: 1) Go to "My Test Vouchers" section, 2) Browse available tests, 3) Click "Buy Now" on your chosen test, 4) Select payment method (Credit Card, PayPal, Bank Transfer), 5) Complete payment, 6) Receive voucher code via email, 7) Use code to register for your test. Our team provides support throughout the process.',
                'answerFr' => 'Pour acheter un voucher de test : 1) Allez à la section "Mes Vouchers de Test", 2) Parcourez les tests disponibles, 3) Cliquez sur "Acheter Maintenant" sur votre test choisi, 4) Sélectionnez le moyen de paiement (Carte de Crédit, PayPal, Virement Bancaire), 5) Complétez le paiement, 6) Recevez le code voucher par email, 7) Utilisez le code pour vous inscrire à votre test. Notre équipe fournit un support tout au long du processus.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '🛒',
                'color' => 'bg-red-500'
            ],
            [
                'category' => 'tests',
                'question' => 'Are your test vouchers valid worldwide?',
                'questionFr' => 'Vos vouchers de test sont-ils valides dans le monde entier ?',
                'answer' => 'Yes, all our test vouchers are official and valid at authorized test centers worldwide. PTE, TOEFL, and GRE vouchers are accepted globally, while Duolingo can be taken online from anywhere. Each voucher includes detailed instructions on how to use it and where to find test centers in your area.',
                'answerFr' => 'Oui, tous nos vouchers de test sont officiels et valides dans les centres de test autorisés dans le monde entier. Les vouchers PTE, TOEFL et GRE sont acceptés globalement, tandis que Duolingo peut être passé en ligne depuis n\'importe où. Chaque voucher inclut des instructions détaillées sur comment l\'utiliser et où trouver les centres de test dans votre région.',
                'sortOrder' => 30,
                'isPopular' => false,
                'icon' => '🌍',
                'color' => 'bg-red-500'
            ],

            // Payments
            [
                'category' => 'payments',
                'question' => 'What payment methods do you accept?',
                'questionFr' => 'Quels moyens de paiement acceptez-vous ?',
                'answer' => 'We accept multiple payment methods: Credit/Debit Cards (Visa, Mastercard, American Express), PayPal, Bank Transfers, and local payment methods in Morocco and France. All payments are processed securely with SSL encryption. You\'ll receive a receipt and confirmation email for all transactions.',
                'answerFr' => 'Nous acceptons plusieurs moyens de paiement : Cartes de Crédit/Débit (Visa, Mastercard, American Express), PayPal, Virements Bancaires, et moyens de paiement locaux au Maroc et en France. Tous les paiements sont traités de manière sécurisée avec chiffrement SSL. Vous recevrez un reçu et un email de confirmation pour toutes les transactions.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '💳',
                'color' => 'bg-emerald-500'
            ],
            [
                'category' => 'payments',
                'question' => 'Is my payment information secure?',
                'questionFr' => 'Mes informations de paiement sont-elles sécurisées ?',
                'answer' => 'Yes, we use industry-standard security measures including SSL encryption, secure payment gateways, and PCI DSS compliance. We never store your complete payment information on our servers. All transactions are processed through trusted third-party providers like Stripe and PayPal.',
                'answerFr' => 'Oui, nous utilisons des mesures de sécurité standard de l\'industrie incluant le chiffrement SSL, des passerelles de paiement sécurisées, et la conformité PCI DSS. Nous ne stockons jamais vos informations de paiement complètes sur nos serveurs. Toutes les transactions sont traitées via des fournisseurs tiers de confiance comme Stripe et PayPal.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '🔒',
                'color' => 'bg-emerald-500'
            ],
            [
                'category' => 'payments',
                'question' => 'Can I get a refund if I\'m not satisfied?',
                'questionFr' => 'Puis-je obtenir un remboursement si je ne suis pas satisfait ?',
                'answer' => 'We offer refunds under specific conditions: 1) Service not delivered as promised, 2) Technical issues preventing service delivery, 3) Cancellation within 24 hours of purchase (for certain services). Refund requests must be submitted within 30 days of purchase. Contact our support team to discuss your specific situation.',
                'answerFr' => 'Nous offrons des remboursements sous certaines conditions : 1) Service non livré comme promis, 2) Problèmes techniques empêchant la livraison du service, 3) Annulation dans les 24 heures suivant l\'achat (pour certains services). Les demandes de remboursement doivent être soumises dans les 30 jours suivant l\'achat. Contactez notre équipe de support pour discuter de votre situation spécifique.',
                'sortOrder' => 30,
                'isPopular' => false,
                'icon' => '↩️',
                'color' => 'bg-emerald-500'
            ],

            // Technical Support
            [
                'category' => 'technical',
                'question' => 'I\'m having trouble accessing my account. What should I do?',
                'questionFr' => 'J\'ai des difficultés à accéder à mon compte. Que dois-je faire ?',
                'answer' => 'If you\'re having trouble accessing your account: 1) Check your internet connection, 2) Clear your browser cache and cookies, 3) Try using a different browser, 4) Ensure you\'re using the correct email and password, 5) Check if your account is activated, 6) Contact our technical support if issues persist.',
                'answerFr' => 'Si vous avez des difficultés à accéder à votre compte : 1) Vérifiez votre connexion internet, 2) Effacez le cache et les cookies de votre navigateur, 3) Essayez d\'utiliser un navigateur différent, 4) Assurez-vous d\'utiliser le bon email et mot de passe, 5) Vérifiez si votre compte est activé, 6) Contactez notre support technique si les problèmes persistent.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '🔧',
                'color' => 'bg-gray-500'
            ],
            [
                'category' => 'technical',
                'question' => 'The website is loading slowly. Is there an issue?',
                'questionFr' => 'Le site web se charge lentement. Y a-t-il un problème ?',
                'answer' => 'Slow loading can be due to: 1) High traffic on our servers, 2) Your internet connection speed, 3) Browser issues, 4) Server maintenance. Try refreshing the page, clearing your browser cache, or using a different browser. If problems persist, contact our technical support team.',
                'answerFr' => 'Le chargement lent peut être dû à : 1) Fort trafic sur nos serveurs, 2) Vitesse de votre connexion internet, 3) Problèmes de navigateur, 4) Maintenance serveur. Essayez de rafraîchir la page, d\'effacer le cache de votre navigateur, ou d\'utiliser un navigateur différent. Si les problèmes persistent, contactez notre équipe de support technique.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '🐌',
                'color' => 'bg-gray-500'
            ],

            // Campus France
            [
                'category' => 'campus-france',
                'question' => 'What is Campus France and how can you help?',
                'questionFr' => 'Qu\'est-ce que Campus France et comment pouvez-vous aider ?',
                'answer' => 'Campus France is the official French agency for promoting higher education to international students. Our Campus France service includes: 1) Application guidance and document preparation, 2) Campus France portal registration assistance, 3) Interview preparation, 4) Visa application support, 5) University selection and application, 6) Ongoing support throughout the process.',
                'answerFr' => 'Campus France est l\'agence française officielle pour promouvoir l\'enseignement supérieur aux étudiants internationaux. Notre service Campus France inclut : 1) Guidance de candidature et préparation de documents, 2) Assistance à l\'inscription sur le portail Campus France, 3) Préparation aux entretiens, 4) Support aux candidatures de visa, 5) Sélection et candidature universitaire, 6) Support continu tout au long du processus.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '🇫🇷',
                'color' => 'bg-blue-600'
            ],
            [
                'category' => 'campus-france',
                'question' => 'What documents do I need for Campus France?',
                'questionFr' => 'Quels documents ai-je besoin pour Campus France ?',
                'answer' => 'Required documents include: 1) Passport (valid for at least 6 months), 2) Academic transcripts and diplomas, 3) French language certificate (if applicable), 4) English language test results, 5) CV/Resume, 6) Statement of Purpose, 7) Letters of Recommendation, 8) Financial documents, 9) Birth certificate, 10) Any other documents specific to your program.',
                'answerFr' => 'Les documents requis incluent : 1) Passeport (valide pour au moins 6 mois), 2) Relevés de notes et diplômes académiques, 3) Certificat de langue française (si applicable), 4) Résultats de tests d\'anglais, 5) CV, 6) Lettre de motivation, 7) Lettres de recommandation, 8) Documents financiers, 9) Acte de naissance, 10) Tout autre document spécifique à votre programme.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '📋',
                'color' => 'bg-blue-600'
            ],

            // Morocco Services
            [
                'category' => 'morocco',
                'question' => 'What is TASSJIL and how does it work?',
                'questionFr' => 'Qu\'est-ce que TASSJIL et comment ça marche ?',
                'answer' => 'TASSJIL is the Moroccan system for registering in public, private, and semi-public schools. Our TASSJIL service provides: 1) Complete application assistance, 2) Document preparation and submission, 3) School selection guidance, 4) Application tracking via mobile app, 5) Support throughout the registration process, 6) Priority processing for TASSJIL TOP 15 schools.',
                'answerFr' => 'TASSJIL est le système marocain pour s\'inscrire dans les écoles publiques, privées et semi-publiques. Notre service TASSJIL fournit : 1) Assistance complète aux candidatures, 2) Préparation et soumission de documents, 3) Guidance de sélection d\'école, 4) Suivi de candidature via application mobile, 5) Support tout au long du processus d\'inscription, 6) Traitement prioritaire pour les écoles TASSJIL TOP 15.',
                'sortOrder' => 10,
                'isPopular' => true,
                'icon' => '🇲🇦',
                'color' => 'bg-red-600'
            ],
            [
                'category' => 'morocco',
                'question' => 'What is TAWJIH PLUS mobile app?',
                'questionFr' => 'Qu\'est-ce que l\'application mobile TAWJIH PLUS ?',
                'answer' => 'TAWJIH PLUS is our mobile application that provides: 1) Real-time notifications about Moroccan educational opportunities, 2) International scholarship alerts for Moroccan students, 3) University application deadlines and reminders, 4) Educational news and updates, 5) Personalized recommendations based on your profile, 6) Direct access to our services and support.',
                'answerFr' => 'TAWJIH PLUS est notre application mobile qui fournit : 1) Notifications en temps réel sur les opportunités éducatives marocaines, 2) Alertes de bourses internationales pour les étudiants marocains, 3) Dates limites de candidature universitaire et rappels, 4) Actualités et mises à jour éducatives, 5) Recommandations personnalisées basées sur votre profil, 6) Accès direct à nos services et support.',
                'sortOrder' => 20,
                'isPopular' => false,
                'icon' => '📱',
                'color' => 'bg-red-600'
            ]
        ];

        foreach ($faqsData as $data) {
            $faq = new FAQ();
            $faq->setCategory($data['category']);
            $faq->setQuestion($data['question']);
            $faq->setQuestionFr($data['questionFr']);
            $faq->setAnswer($data['answer']);
            $faq->setAnswerFr($data['answerFr']);
            $faq->setSortOrder($data['sortOrder']);
            $faq->setIsActive($data['isActive'] ?? true);
            $faq->setIsPopular($data['isPopular']);
            $faq->setIcon($data['icon']);
            $faq->setColor($data['color']);

            $this->entityManager->persist($faq);
        }

        $this->entityManager->flush();

        $io->success('FAQ data seeded successfully! ' . count($faqsData) . ' FAQs created.');

        return Command::SUCCESS;
    }
}
