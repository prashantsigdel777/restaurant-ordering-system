<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/**
 * BASE_URL auto-detects:
 * - localhost: /restaurant
 * - student server: /~NP03CS4A240171/restaurant
 */
$BASE_URL = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if (preg_match('~/admin$~', $BASE_URL) || preg_match('~/ajax$~', $BASE_URL)) {
  $BASE_URL = dirname($BASE_URL);
}

/* ====== DATABASE SETTINGS (EDIT THESE FOR COLLEGE SERVER) ======
   DO NOT use root/blank password on student server.
   Use the database name/user/password given in your student portal.
*/
$host = 'localhost';
$db   = 'NP03CS4A240171';
$user = 'NP03CS4A240171';
$pass = 'ZdAAKSJzy1';

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO(
    "mysql:host={$host};dbname={$db};charset=utf8mb4",
    $user,
    $pass,
    $options
  );
} catch (Throwable $e) {
  http_response_code(500);
  exit("Database connection failed.");
}

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
