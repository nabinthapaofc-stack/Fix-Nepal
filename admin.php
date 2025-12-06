<?php
require_once __DIR__ . '/db/db.php';
session_start();

// require admin
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo 'Access denied. <a href="index.html">Login</a>';
    exit;
}

$mysqli = db_connect();

// handle resolve action (GET is fine for internal admin UI)
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'resolve') {
    $id = (int) $_GET['id'];
    $u = $mysqli->prepare('UPDATE issues SET status = ? WHERE id = ?');
    $status = 'resolved';
    $u->bind_param('si', $status, $id);
    $u->execute();
    $u->close();
    header('Location: admin.php');
    exit;
}

// fetch issues
$res = $mysqli->query('SELECT id, user_id, email, title, description, location, status, created_at FROM issues ORDER BY created_at DESC');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Issues</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Admin Dashboard</h1>
  <p>Welcome admin ID: <?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?></p>

  <h2>Reported issues</h2>

  <?php if ($res && $res->num_rows > 0): ?>
    <table border="1" cellpadding="6" cellspacing="0">
      <tr>
        <th>ID</th><th>Title</th><th>Description</th><th>Location</th><th>Reporter</th><th>Status</th><th>When</th><th>Action</th>
      </tr>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?php echo (int)$row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['title']); ?></td>
          <td style="max-width:400px"><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
          <td><?php echo htmlspecialchars($row['location']); ?></td>
          <td><?php echo htmlspecialchars($row['email'] ?: 'UID:' . (int)$row['user_id']); ?></td>
          <td><?php echo htmlspecialchars($row['status']); ?></td>
          <td><?php echo htmlspecialchars($row['created_at']); ?></td>
          <td>
            <?php if ($row['status'] === 'open'): ?>
              <a href="admin.php?action=resolve&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Mark this issue resolved?')">Resolve</a>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No reported issues.</p>
  <?php endif; ?>

  <p><a href="logout.php">Logout</a></p>
</body>
</html>
<?php
$mysqli->close();
?>