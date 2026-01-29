<?php
require_once __DIR__ . '/../header.php';
require_admin();
?>

<h1>Admin Dashboard</h1>

<div class="card">
  <p>Welcome Admin. Manage the system here:</p>
  <div class="row" style="justify-content:flex-start; gap:10px;">
    <a class="btn" href="/restaurant/admin/categories.php">Categories</a>
    <a class="btn" href="/restaurant/admin/items.php">Menu Items</a>
    <a class="btn primary" href="/restaurant/admin/orders.php">Orders</a>
    <a class="btn" href="/restaurant/admin/logout.php">Logout</a>
  </div>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
