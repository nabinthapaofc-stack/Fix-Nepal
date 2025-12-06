<?php
session_start();
require_once __DIR__ . '/db.php';

// Auth check: must be logged in and admin
if (empty($_SESSION['admin_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Access denied', 'redirect' => 'index.html']);
    exit;
}

// Fetch admin info
$adminId = (int) $_SESSION['admin_id'];
$stmt = $pdo->prepare('SELECT id, admin_name, full_name, email, phone_number, created_at FROM admins_reg WHERE id = ? LIMIT 1');
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

// Example: fetch all users for admin panel
$stmt = $pdo->query('SELECT id, username, full_name, email, role, created_at FROM users_reg ORDER BY created_at DESC');
$users = $stmt->fetchAll();

// Optionally fetch reports
$stmt = $pdo->query('SELECT * FROM dash_report ORDER BY date_of_report DESC');
$reports = $stmt->fetchAll();

// Render admin interface using $admin, $users, $reports
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin</title></head>
<body>
  <h1>Admin Dashboard</h1>
  <p>Welcome admin ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
  <p><a href="logout.php">Logout</a></p>
  <ul>
    <?php foreach ($users as $user): ?>
      <li><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>) - <?php echo htmlspecialchars($user['role']); ?></li>
    <?php endforeach; ?>
  </ul>
  <h2>Admin Info</h2>
  <p><?php echo htmlspecialchars($admin['admin_name']); ?> - <?php echo htmlspecialchars($admin['full_name']); ?> - <?php echo htmlspecialchars($admin['email']); ?> - <?php echo htmlspecialchars($admin['phone_number']); ?> - <?php echo htmlspecialchars($admin['created_at']); ?></p>
  <h2>Reports</h2>
  <ul>
    <?php foreach ($reports as $report): ?>
      <li><?php echo htmlspecialchars($report['report_name']); ?> - <?php echo htmlspecialchars($report['date_of_report']); ?></li>
    <?php endforeach; ?>
  </ul>
</body></html>