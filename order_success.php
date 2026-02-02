<?php
require_once __DIR__ . '/header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  echo "<div class='error'>Invalid order.</div>";
  require __DIR__ . '/footer.php';
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
  echo "<div class='error'>Order not found.</div>";
  require __DIR__ . '/footer.php';
  exit;
}

$items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY id");
$items->execute([$id]);
$orderItems = $items->fetchAll();
?>

<h1>Order Placed ✅</h1>

<div class="card">
  <p><strong>Order ID:</strong> #<?= (int)$order['id'] ?></p>
  <p><strong>Status:</strong> <?= e($order['status']) ?></p>
  <p><strong>Total:</strong> <?= money($order['total_amount']) ?></p>
  <p class="muted">Placed on: <?= e($order['created_at']) ?></p>
</div>

<div class="card">
  <h3>Items</h3>
  <table>
    <tr><th>Item</th><th>Unit</th><th>Qty</th><th>Line</th></tr>
    <?php foreach ($orderItems as $it): ?>
      <tr>
        <td><?= e($it['item_name']) ?></td>
        <td><?= money($it['unit_price']) ?></td>
        <td><?= (int)$it['qty'] ?></td>
        <td><?= money($it['line_total']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <div style="margin-top:12px;">
    <a class="btn" href="<?= $BASE_URL ?>/menu.php">Back to Menu</a>


  </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>