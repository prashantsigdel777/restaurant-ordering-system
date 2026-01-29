<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

cart_init();
$itemId = (int)($_POST['item_id'] ?? 0);
if ($itemId > 0 && isset($_SESSION['cart'][$itemId])) {
  unset($_SESSION['cart'][$itemId]);
}
header('Location: /restaurant/cart.php');
exit;
