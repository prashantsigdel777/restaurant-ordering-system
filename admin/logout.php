<?php
session_start();

/* Unset all session variables */
$_SESSION = [];

/* Destroy the session */
session_destroy();

/* Regenerate session ID for safety */
session_regenerate_id(true);

/* Redirect to admin login page */
header('Location: /restaurant/admin/login.php');
exit;
