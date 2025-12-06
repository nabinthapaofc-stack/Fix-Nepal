<?php
session_start();
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
  <p>Welcome user ID: <?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?></p>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>