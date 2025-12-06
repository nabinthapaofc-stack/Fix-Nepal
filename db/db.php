<?php
$DB_HOST = '127.0.0.1';          // or 'localhost'
$DB_NAME = 'fixnepal';
$DB_USER = 'root';
$DB_PASS = '';                   // default XAMPP password is empty
$DB_PORT = 3306;                 // <- change from 4430 to 3306

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed.');
}

if (!function_exists('get_pdo')) {
    function get_pdo() {
        global $pdo;
        return $pdo;
    }
}
