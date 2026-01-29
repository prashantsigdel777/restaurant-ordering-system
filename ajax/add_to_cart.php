<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

cart_init();
header('Content-Type: application/json');

$itemId = (int)($_POST['item_id'] ?? 0);
$qty    = (int)($_POST['qty'] ?? 1);

if ($itemId <= 0 || $qty <= 0) {
  echo json_encode(['ok' => false, 'error' => 'Invalid data']);
  exit;
}

$stmt = $pdo->prepare("SELECT is_available FROM menu_items WHERE id = ?");
$stmt->execute([$itemId]);
$item = $stmt->fetch();

if (!$item || (int)$item['is_available'] !== 1) {
  echo json_encode(['ok' => false, 'error' => 'Item unavailable']);
  exit;
}

$_SESSION['cart'][$itemId] = (int)($_SESSION['cart'][$itemId] ?? 0) + $qty;

echo json_encode(['ok' => true, 'cartCount' => cart_count()]);
