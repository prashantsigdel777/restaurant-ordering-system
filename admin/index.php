<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_admin();

require_once __DIR__ . '/../header.php';
?>

<h1>Admin Dashboard</h1>

<div class="card">
  <div style="display:flex; gap:10px; flex-wrap:wrap;">
    <a class="btn" href="<?= $BASE_URL ?>/admin/categories.php">Categories</a>
    <a class="btn" href="<?= $BASE_URL ?>/admin/items.php">Manage Items</a>
    <a class="btn primary" href="<?= $BASE_URL ?>/admin/orders.php">Orders</a>
    <a class="btn" href="<?= $BASE_URL ?>/admin/logout.php">Logout</a>
  </div>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
