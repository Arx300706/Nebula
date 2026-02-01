<?php
session_start();
header('Content-Type: application/json');

// 1. VVerification de la connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expirée. Veuillez vous reconnecter.']);
    exit;
}

// 2. Configuration des chemins et du quota
$userSpace = $_SESSION['user_id'];
$baseUploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
$userUploadDir = $baseUploadDir . DIRECTORY_SEPARATOR . $userSpace;
$quotaMax = 1024 * 1024 * 100; // Limite fixée à 100 Mo

// Fonction pour calculer l'espace deja utilise
function getDirSize($path) {
    $size = 0;
    if (!is_dir($path)) return 0;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($files as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    return $size;
}

// 3. Creation du dossier utilisateur s'il n'existe pas
if (!is_dir($userUploadDir)) {
    if (!mkdir($userUploadDir, 0775, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur systeme : Impossible de creer votre espace prive.']);
        exit;
    }
}

// 4. Traitement du fichier
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $fileSize = $file['size'];
    
    // Verification du Quota
    $currentUsed = getDirSize($userUploadDir);
    if (($currentUsed + $fileSize) > $quotaMax) {
        echo json_encode(['status' => 'error', 'message' => 'Espace de stockage insuffisant (Limite de 100 Mo atteinte).']);
        exit;
    }

    // Gestion des dossiers (si envoye via l'import de dossier)
    $relativePath = $_POST['fullPath'] ?? $fileName;
    $targetFile = $userUploadDir . DIRECTORY_SEPARATOR . $relativePath;
    $targetFolder = dirname($targetFile);

    if (!is_dir($targetFolder)) {
        mkdir($targetFolder, 0775, true);
    }

    // Deplacement final
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Securite : on retire les droits d'execution sur le fichier televerse
        chmod($targetFile, 0644);
        echo json_encode(['status' => 'success', 'message' => 'Fichier envoye avec succes !']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Echec du transfert. VVerifiez les permissions du dossier uploads.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aucun fichier recu ou fichier trop volumineux pour le serveur.']);
}