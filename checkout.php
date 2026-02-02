<?php
require_once __DIR__ . '/header.php';
cart_init();

$cart = $_SESSION['cart'];
if (!$cart) {
  header('Location: cart.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_validate_post();

  $name    = trim($_POST['customer_name'] ?? '');
  $phone   = trim($_POST['customer_phone'] ?? '');
  $address = trim($_POST['customer_address'] ?? '');

  if ($name === '' || $phone === '' || $address === '') {
    $error = 'Please fill in all customer details.';
  } elseif (!preg_match('/^\d{10}$/', $phone)) {
    $error = 'Phone number must be exactly 10 digits.';
  } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 60) {
    $error = 'Name must be between 2 and 60 characters.';
  } elseif (mb_strlen($address) < 5 || mb_strlen($address) > 200) {
    $error = 'Address must be between 5 and 200 characters.';
  } else {
    $ids = array_keys($cart);
    $ph = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, name, price, is_available FROM menu_items WHERE id IN ($ph)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();

    $map = [];
    foreach ($rows as $r) $map[(int)$r['id']] = $r;

    $total = 0.0;
    foreach ($cart as $id => $qty) {
      $id = (int)$id;
      $qty = max(1, (int)$qty);
      if (!isset($map[$id])) continue;
      if ((int)$map[$id]['is_available'] !== 1) continue;
      $total += (float)$map[$id]['price'] * $qty;
    }

    if ($total <= 0) {
      $error = 'Cart total invalid (items unavailable).';
    } else {
      $pdo->beginTransaction();
      try {
        $o = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, status, total_amount) VALUES (?, ?, ?, 'Pending', ?)");
        $o->execute([$name, $phone, $address, $total]);
        $orderId = (int)$pdo->lastInsertId();

        $oi = $pdo->prepare("INSERT INTO order_items (order_id, item_id, item_name, unit_price, qty, line_total) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($cart as $id => $qty) {
          $id = (int)$id;
          $qty = max(1, (int)$qty);
          if (!isset($map[$id])) continue;
          if ((int)$map[$id]['is_available'] !== 1) continue;

          $unit = (float)$map[$id]['price'];
          $line = $unit * $qty;
          $oi->execute([$orderId, $id, $map[$id]['name'], $unit, $qty, $line]);
        }

        $_SESSION['cart'] = [];
        $pdo->commit();

        header("Location: order_success.php?id=" . $orderId);
        exit;
      } catch (Throwable $e) {
        $pdo->rollBack();
        $error = 'Checkout failed.';
      }
    }
  }
}
?>

<h1>Checkout</h1>

<?php if ($error): ?>
  <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Customer Name</label>
    <input name="customer_name" required minlength="2" maxlength="60" placeholder="Your name">

    <label>Phone (10 digits)</label>
    <input name="customer_phone" required pattern="\d{10}" maxlength="10" placeholder="98xxxxxxxx">

    <label>Address</label>
    <input name="customer_address" required minlength="5" maxlength="200" placeholder="Delivery address">

    <div style="margin-top:12px;">
      <button class="btn primary" type="submit">Place Order</button>
      <a class="btn" href="cart.php">Back</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>