<?php
session_start();

// Clear session data
$_SESSION = [];

// Remove session cookie if present
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'],
		$params['secure'], $params['httponly']
	);
}

// Destroy the session
session_destroy();

// Respond based on request type
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($isAjax) {
	header('Content-Type: application/json');
	echo json_encode(['ok' => true, 'redirect' => 'index1.html']);
	exit;
}

// Non-AJAX: redirect to login page
header('Location: index.html');
exit;
?>