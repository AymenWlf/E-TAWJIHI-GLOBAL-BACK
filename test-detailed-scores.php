<?php

require_once 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

// Configuration de la base de données
$connectionParams = [
    'dbname' => 'symfony_react_jwt',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

try {
    $connection = DriverManager::getConnection($connectionParams);

    // Vérifier si la colonne detailed_scores existe
    $sql = "SHOW COLUMNS FROM qualifications LIKE 'detailed_scores'";
    $result = $connection->executeQuery($sql)->fetchAllAssociative();

    if (empty($result)) {
        echo "❌ La colonne detailed_scores n'existe pas. Ajout en cours...\n";

        // Ajouter la colonne
        $connection->executeStatement("ALTER TABLE qualifications ADD COLUMN detailed_scores JSON DEFAULT NULL");
        echo "✅ Colonne detailed_scores ajoutée avec succès!\n";
    } else {
        echo "✅ La colonne detailed_scores existe déjà.\n";
    }

    // Tester l'insertion d'une qualification avec detailed_scores
    $testData = [
        'user_profile_id' => 1,
        'type' => 'language',
        'title' => 'IELTS - 7.5',
        'institution' => 'IELTS',
        'field' => 'English Language',
        'score' => '7.5',
        'score_type' => 'IELTS',
        'status' => 'valid',
        'detailed_scores' => json_encode([
            'overall' => '7.5',
            'listening' => '8.0',
            'reading' => '7.0',
            'writing' => '7.0',
            'speaking' => '8.0'
        ]),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    echo "🧪 Test d'insertion d'une qualification avec detailed_scores...\n";

    // Supprimer d'abord le test s'il existe
    $connection->executeStatement("DELETE FROM qualifications WHERE title = 'IELTS - 7.5'");

    // Insérer le test
    $connection->insert('qualifications', $testData);
    echo "✅ Qualification test insérée avec succès!\n";

    // Récupérer et vérifier
    $sql = "SELECT * FROM qualifications WHERE title = 'IELTS - 7.5'";
    $result = $connection->executeQuery($sql)->fetchAssociative();

    if ($result) {
        echo "✅ Qualification récupérée avec succès!\n";
        echo "📊 Detailed scores: " . $result['detailed_scores'] . "\n";

        $detailedScores = json_decode($result['detailed_scores'], true);
        echo "📊 Overall score: " . $detailedScores['overall'] . "\n";
        echo "📊 Listening score: " . $detailedScores['listening'] . "\n";
    }

    // Nettoyer le test
    $connection->executeStatement("DELETE FROM qualifications WHERE title = 'IELTS - 7.5'");
    echo "🧹 Test nettoyé.\n";

    echo "\n🎉 Tous les tests sont passés avec succès!\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
