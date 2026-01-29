<?php
require_once 'config.php';
require_once 'functions.php';
cart_init();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ghas Paat Restro</title>
  <link rel="stylesheet" href="/restaurant/assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="wrap row">
    <a class="brand" href="/restaurant/index.php">🍃 Ghas Paat Restro</a>
    <nav>
      <a href="/restaurant/index.php">Home</a>
      <a href="/restaurant/menu.php">Menu</a>
      <a href="/restaurant/cart.php">
  Cart (<span id="cartCount"><?= cart_count() ?></span>)
</a>


      <?php if (is_admin()): ?>
        <a href="/restaurant/admin/index.php">Admin</a>
        <a href="/restaurant/admin/logout.php">Logout</a>
      <?php else: ?>
        <a href="/restaurant/admin/login.php">Admin Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="wrap">
