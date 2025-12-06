<?php
session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'pothole';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Email and password required']);
    } else {
        $_SESSION['login_error'] = 'Email and password required';
        header('Location: index.html');
    }
    exit;
}

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'DB connect error: ' . $mysqli->connect_error]);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, password_hash, role FROM users WHERE email = ? LIMIT 1');
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'DB prepare error']);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($id, $hash, $role);
$found = $stmt->fetch();
$stmt->close();
$mysqli->close();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($found && $hash !== null && password_verify($password, $hash)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['role'] = $role;
    $redirect = ($role === 'admin') ? 'admin.php' : 'user.php';

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'redirect' => $redirect]);
    } else {
        header('Location: ' . $redirect);
    }
    exit;
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Invalid credentials']);
} else {
    $_SESSION['login_error'] = 'Invalid credentials';
    header('Location: index.html');
}
?>