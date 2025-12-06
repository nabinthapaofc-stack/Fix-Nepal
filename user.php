
<?php
session_start();
require_once __DIR__ . '/db.php';

// Protect page: require login
if (empty($_SESSION['user_id'])) {
    // If AJAX, respond JSON; else redirect
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Not authenticated', 'redirect' => 'login.php']);
        exit;
    }
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT id, username, full_name, email, role, created_at FROM users_reg WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    // invalid session, force logout
    session_unset();
    session_destroy();
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Session invalid', 'redirect' => 'login.php']);
        exit;
    }
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    http_response_code(403);
    echo 'Access denied. <a href="index.html">Login</a>';
    exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>User Dashboard</title></head>
<body>
  <h1>User Dashboard</h1>
  <p>Welcome user ID: <?php echo htmlspecialchars($user['id'] ?? ''); ?></p>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>