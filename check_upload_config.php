<?php
// Script pour vérifier la configuration PHP pour l'upload
echo "Configuration PHP pour l'upload de fichiers:\n";
echo "==========================================\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_time: " . ini_get('max_input_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "\n";
echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "\n";

// Vérifier si le répertoire temporaire est accessible en écriture
$tmpDir = sys_get_temp_dir();
echo "\nRépertoire temporaire: " . $tmpDir . "\n";
echo "Accessible en écriture: " . (is_writable($tmpDir) ? 'Oui' : 'Non') . "\n";

// Vérifier l'espace disque
echo "Espace disque libre: " . round(disk_free_space($tmpDir) / 1024 / 1024, 2) . " MB\n";
