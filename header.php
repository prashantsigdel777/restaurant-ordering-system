<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
cart_init();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Ghas Paat Restro</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/style.css">
</head>
<body data-base="<?= e($BASE_URL) ?>">

<header class="topbar">
  <div class="wrap row">
    <a class="brand" href="<?= $BASE_URL ?>/index.php">🍃 Ghas Paat Restro</a>

    <nav class="nav">
      <a href="<?= $BASE_URL ?>/index.php">Home</a>
      <a href="<?= $BASE_URL ?>/menu.php">Menu</a>
      <a href="<?= $BASE_URL ?>/cart.php">Cart (<span id="cartCount"><?= cart_count() ?></span>)</a>

      <?php if (!empty($_SESSION['is_admin'])): ?>
        <a href="<?= $BASE_URL ?>/admin/index.php">Admin</a>
        <a href="<?= $BASE_URL ?>/admin/logout.php">Logout</a>
      <?php else: ?>
        <a href="<?= $BASE_URL ?>/admin/login.php">Admin Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="wrap">
