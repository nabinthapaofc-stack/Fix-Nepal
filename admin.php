<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo 'Access denied. <a href="index.html">Login</a>';
    exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin</title></head>
<body>
  <h1>Admin Dashboard</h1>
  <p>Welcome admin ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
  <p><a href="logout.php">Logout</a></p>
</body></html>