<?php

session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'pothole';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = 'user';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Invalid input']);
    exit;
}

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'DB']); exit; }

$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $mysqli->close();
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Email already exists']);
    exit;
}
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare('INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $email, $hash, $role);
$ok = $stmt->execute();
$stmt->close();
$mysqli->close();

header('Content-Type: application/json');
echo json_encode(['ok' => $ok]);
?>