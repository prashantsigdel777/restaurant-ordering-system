<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_admin();

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<h1>Admin - Orders</h1>

<div class="card">
  <a class="btn" href="/admin/categories.php">Manage Categories</a>
  <a class="btn" href="/admin/items.php">Manage Items</a>
  <a class="btn" href="/admin/logout.php">Logout</a>
</div>

<div class="card">
  <table>
    <tr>
      <th>Order ID</th>
      <th>Customer</th>
      <th>Phone</th>
      <th>Status</th>
      <th>Total</th>
      <th>Created</th>
      <th></th>
    </tr>

    <?php foreach ($orders as $o): ?>
      <tr>
        <td>#<?= (int)$o['id'] ?></td>
        <td><?= e($o['customer_name']) ?></td>
        <td><?= e($o['customer_phone']) ?></td>
        <td><?= e($o['status']) ?></td>
        <td><?= money($o['total_amount']) ?></td>
        <td><?= e($o['created_at']) ?></td>
        <td><a class="btn" href="/admin/order_edit.php?id=<?= (int)$o['id'] ?>">View / Update</a></td>
      </tr>
    <?php endforeach; ?>

  </table>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>