<?php
// Test simple d'upload de fichier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];

    echo "Informations sur le fichier uploadé:\n";
    echo "=====================================\n";
    echo "Nom: " . $file['name'] . "\n";
    echo "Taille: " . $file['size'] . " bytes\n";
    echo "Type: " . $file['type'] . "\n";
    echo "Erreur: " . $file['error'] . "\n";
    echo "Fichier temporaire: " . $file['tmp_name'] . "\n";
    echo "Existe: " . (file_exists($file['tmp_name']) ? 'Oui' : 'Non') . "\n";

    if (file_exists($file['tmp_name'])) {
        echo "Taille réelle: " . filesize($file['tmp_name']) . " bytes\n";
    }

    // Essayer de déplacer le fichier
    $targetDir = __DIR__ . '/public/uploads/test/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetFile = $targetDir . 'test_' . time() . '_' . $file['name'];

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo "Fichier déplacé avec succès vers: " . $targetFile . "\n";
    } else {
        echo "Échec du déplacement du fichier\n";
    }
} else {
    echo "Formulaire de test d'upload:\n";
    echo "<form method='POST' enctype='multipart/form-data'>\n";
    echo "<input type='file' name='test_file'>\n";
    echo "<button type='submit'>Tester Upload</button>\n";
    echo "</form>\n";
}
