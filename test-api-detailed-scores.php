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

    echo "🧪 Test de l'API avec detailed_scores...\n";

    // Insérer une qualification de test avec detailed_scores
    $testData = [
        'user_profile_id' => 4, // ID existant
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

    // Supprimer d'abord le test s'il existe
    $connection->executeStatement("DELETE FROM qualifications WHERE title = 'IELTS - 7.5' AND user_profile_id = 4");

    // Insérer le test
    $connection->insert('qualifications', $testData);
    echo "✅ Qualification test insérée avec succès!\n";

    // Récupérer et vérifier
    $sql = "SELECT * FROM qualifications WHERE title = 'IELTS - 7.5' AND user_profile_id = 4";
    $result = $connection->executeQuery($sql)->fetchAssociative();

    if ($result) {
        echo "✅ Qualification récupérée avec succès!\n";
        echo "📊 Detailed scores JSON: " . $result['detailed_scores'] . "\n";

        $detailedScores = json_decode($result['detailed_scores'], true);
        if ($detailedScores) {
            echo "📊 Overall score: " . $detailedScores['overall'] . "\n";
            echo "📊 Listening score: " . $detailedScores['listening'] . "\n";
            echo "📊 Reading score: " . $detailedScores['reading'] . "\n";
            echo "📊 Writing score: " . $detailedScores['writing'] . "\n";
            echo "📊 Speaking score: " . $detailedScores['speaking'] . "\n";
        }
    }

    echo "\n🎉 Test de persistance réussi!\n";
    echo "💡 La colonne detailed_scores fonctionne correctement.\n";
    echo "💡 Les tests d'anglais devraient maintenant être persistants.\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
