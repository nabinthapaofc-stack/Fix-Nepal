<?php

session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$full_name = trim($_POST['full_name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm = $_POST['confirm_password'] ?? '';

	$errors = [];
	if ($username === '') $errors[] = 'Username is required';
	if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
	if ($password === '') $errors[] = 'Password is required';
	if ($password !== $confirm) $errors[] = 'Passwords do not match';

	if ($errors) {
		$resp = ['ok' => false, 'error' => implode('; ', $errors)];
		header('Content-Type: application/json');
		echo json_encode($resp);
		exit;
	}

	// Check duplicates
	$stmt = $pdo->prepare('SELECT id FROM users_reg WHERE username = ? OR email = ? LIMIT 1');
	$stmt->execute([$username, $email]);
	if ($stmt->fetch()) {
		header('Content-Type: application/json');
		echo json_encode(['ok' => false, 'error' => 'Username or email already registered']);
		exit;
	}

	$hash = password_hash($password, PASSWORD_DEFAULT);
	$insert = $pdo->prepare('INSERT INTO users_reg (username, full_name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
	$role = 'user';
	$insert->execute([$username, $full_name, $email, $hash, $role]);

	header('Content-Type: application/json');
	echo json_encode(['ok' => true, 'redirect' => 'index.html']);
	exit;
}

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
?>