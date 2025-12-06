<?php  
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');    // XAMPP default
define('DB_PASS', '');        // XAMPP default
define('DB_NAME', 'fixnepal');

function db_connect() {
  $m = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($m->connect_error) {
    die('DB connect error');
  }
  return $m;
}
?>