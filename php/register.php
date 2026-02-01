<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $line = "$name|$email|$pass" . PHP_EOL;
    file_put_contents('users.txt', $line, FILE_APPEND);
    echo json_encode(['status' => 'success']);
}