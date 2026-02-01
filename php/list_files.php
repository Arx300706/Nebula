<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['status' => 'error', 'message' => 'Non connecte']));
}

$userSpace = $_SESSION['user_id'];
$userPath = __DIR__ . '/../uploads/' . $userSpace;
$quotaMax = 1024 * 1024 * 100; // Limite a 100 Mo (en octets)

// Fonction pour calculer la taille totale d'un dossier
function getDirSize($path) {
    $size = 0;
    if (!is_dir($path)) return 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

$usedSpace = getDirSize($userPath);
$files = [];

if (is_dir($userPath)) {
    foreach (scandir($userPath) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $userPath . DIRECTORY_SEPARATOR . $item;
        $files[] = [
            'name' => $item,
            'size' => is_dir($fullPath) ? '-' : round(filesize($fullPath) / 1024, 2) . ' KB',
            'isDir' => is_dir($fullPath)
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'files' => $files,
    'storage' => [
        'used' => $usedSpace,
        'limit' => $quotaMax,
        'percent' => ($usedSpace / $quotaMax) * 100,
        'readableUsed' => round($usedSpace / (1024 * 1024), 2) . ' MB',
        'readableLimit' => round($quotaMax / (1024 * 1024), 2) . ' MB'
    ]
]);