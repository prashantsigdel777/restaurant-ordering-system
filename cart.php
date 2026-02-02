<?php
require_once __DIR__ . '/header.php';
cart_init();

$cart = $_SESSION['cart'];
$items = [];
$total = 0.0;

if ($cart) {
  $ids = array_keys($cart);
  $ph = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT id, name, price, is_available FROM menu_items WHERE id IN ($ph)");
  $stmt->execute($ids);
  $items = $stmt->fetchAll();

  foreach ($items as $it) {
    $id = (int)$it['id'];
    $qty = max(1, (int)($cart[$id] ?? 1));
    if ((int)$it['is_available'] !== 1) continue;
    $total += (float)$it['price'] * $qty;
  }
}
?>

<h1>Your Cart</h1>

<?php if (!$cart): ?>
  <div class="card">Your cart is empty. <a href="menu.php">Browse menu</a></div>
<?php else: ?>
  <div class="card">
    <table>
      <tr><th>Item</th><th>Price</th><th>Qty</th><th>Line</th><th></th></tr>

      <?php foreach ($items as $it):
        $id = (int)$it['id'];
        $qty = max(1, (int)($cart[$id] ?? 1));
        $line = (float)$it['price'] * $qty;
      ?>
        <tr>
          <td><?= e($it['name']) ?></td>
          <td><?= money($it['price']) ?></td>
          <td><?= $qty ?></td>
          <td><?= money($line) ?></td>
          <td>
            <form method="post" action="/ajax/remove_from_cart.php">
              <input type="hidden" name="item_id" value="<?= $id ?>">
              <button class="btn danger" type="submit">Remove</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>

      <tr>
        <th colspan="3">Grand Total</th>
        <th><?= money($total) ?></th>
        <th></th>
      </tr>
    </table>

    <div style="margin-top:12px;">
      <a class="btn" href="menu.php">Back to Menu</a>
      <a class="btn primary" href="<?= $BASE_URL ?>/checkout.php">Checkout</a>


    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>