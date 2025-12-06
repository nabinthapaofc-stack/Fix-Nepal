<?php
session_start();
require_once __DIR__ . '/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        // handle error (return JSON or set flash message)
        $err = 'Email and password required';
    } else {
        // Try admin login first
        $stmt = $pdo->prepare('SELECT id, admin_name, email, password FROM admins_reg WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            // Authenticated
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['email'] = $admin['email'];
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'redirect' => 'admin.php']);
            exit;
        }

        // Try user login
        $stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users_reg WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // Authenticated
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            $_SESSION['email'] = $user['email'];
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'redirect' => 'user.php']);
            exit;
        }

        $err = 'Invalid credentials';
    }
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($email === '' || $password === '') {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Email and password required']);
    } else {
        $_SESSION['login_error'] = 'Email and password required';
        header('Location: index.html');
    }
    exit;
}

$stmt = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'] ?? 'user';

    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: user.php');
        exit;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Invalid credentials']);
} else {
    $_SESSION['login_error'] = 'Invalid credentials';
    header('Location: index.html');
}
?>