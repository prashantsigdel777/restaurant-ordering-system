<?php

function e($s) {
  return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
}

function cart_init() {
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }
}

function cart_count() {
  cart_init();
  return array_sum($_SESSION['cart']);
}

function is_admin() {
  return !empty($_SESSION['is_admin']);
}

function require_admin() {
  if (!is_admin()) {
    header("Location: /admin/login.php");
    exit;
  }
}

// CSRF
function csrf_token() {
  return $_SESSION['csrf'];
}

function csrf_validate_post() {
  if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
    exit("CSRF validation failed");
  }
}

// Nepal currency helper
function money($amount) {
  return 'Rs. ' . number_format((float)$amount, 2);
}