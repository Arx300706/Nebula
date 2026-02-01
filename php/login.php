<?php
session_start(); // Indispensable au debut de chaque fichier
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    $users = file_exists('users.txt') ? file('users.txt', FILE_IGNORE_NEW_LINES) : [];
    foreach ($users as $line) {
        list($name, $uEmail, $uPass) = explode('|', $line);
        if ($email === $uEmail && password_verify($pass, $uPass)) {
            // On stocke les infos de l'utilisateur dans la session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_id'] = md5($uEmail); // Créer un ID unique basé sur l'email
            
            echo json_encode(['status' => 'success', 'name' => $name]);
            exit;
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Identifiants invalides']);
}