<?php
require_once __DIR__ . '/db/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');
$email = trim($_POST['email'] ?? ($_SESSION['email'] ?? ''));

if ($title === '' || $description === '') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Title and description required']);
    exit;
}

$mysqli = db_connect();
$stmt = $mysqli->prepare('INSERT INTO issues (user_id, email, title, description, location) VALUES (?, ?, ?, ?, ?)');
$user_id = $_SESSION['user_id'] ?? null;
$stmt->bind_param('issss', $user_id, $email, $title, $description, $location);
$ok = $stmt->execute();
$stmt->close();
$mysqli->close();

header('Content-Type: application/json');
echo json_encode(['ok' => (bool)$ok]);
?>