<?php
session_start();
if (!isset($_SESSION['user_id'])) die("Accès interdit");

$userSpace = $_SESSION['user_id'];
$uploadDir = realpath(__DIR__ . '/../uploads/' . $userSpace);
$filePath = $_GET['file'] ?? '';

// On force la recherche UNIQUEMENT dans le dossier de l'utilisateur
$file = realpath($uploadDir . DIRECTORY_SEPARATOR . $filePath);

if ($file && strpos($file, $uploadDir) === 0 && is_file($file)) {
    header('Content-Type: ' . mime_content_type($file));
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    readfile($file);
    exit;
}
http_response_code(403); // Interdit