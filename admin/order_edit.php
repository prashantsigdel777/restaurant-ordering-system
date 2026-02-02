<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) exit("Invalid order id.");

$error = '';

/* Update status */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_validate_post();

  $status = $_POST['status'] ?? '';
  $allowed = ['Pending','Preparing','Ready','Completed','Cancelled'];

  if (!in_array($status, $allowed, true)) {
    $error = "Invalid status selected.";
  } else {
    $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);

    // Flash message on orders page
    $_SESSION['success'] = "✅ Order #{$id} status changed to <strong>" . e($status) . "</strong>";

    header("Location: {$BASE_URL}/admin/orders.php");
    exit;
  }
}

/* Load order */
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) exit("Order not found.");

/* Load items */
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id=? ORDER BY id");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<h1>Order #<?= (int)$order['id'] ?></h1>

<div class="card">
  <a class="btn" href="<?= $BASE_URL ?>/admin/orders.php">Back to Orders</a>
  <a class="btn" href="<?= $BASE_URL ?>/admin/logout.php">Logout</a>
</div>

<?php if ($error): ?>
  <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
  <p><strong>Customer:</strong> <?= e($order['customer_name']) ?></p>
  <p><strong>Phone:</strong> <?= e($order['customer_phone']) ?></p>
  <p><strong>Address:</strong> <?= e($order['customer_address']) ?></p>
  <p><strong>Total:</strong> <?= money($order['total_amount']) ?></p>
  <p class="muted"><strong>Created:</strong> <?= e($order['created_at']) ?></p>
</div>

<div class="card">
  <h3>Order Items</h3>
  <table>
    <tr><th>Item</th><th>Qty</th><th>Unit</th><th>Line</th></tr>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?= e($it['item_name']) ?></td>
        <td><?= (int)$it['qty'] ?></td>
        <td><?= money($it['unit_price']) ?></td>
        <td><?= money($it['line_total']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="card">
  <h3>Update Status</h3>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Status</label>
    <select name="status" required>
      <?php foreach (['Pending','Preparing','Ready','Completed','Cancelled'] as $s): ?>
        <option value="<?= e($s) ?>" <?= ($order['status'] === $s) ? 'selected' : '' ?>>
          <?= e($s) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <div style="margin-top:12px;">
      <button class="btn primary" type="submit">Save Status</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
