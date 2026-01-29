<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_admin();

$error = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Handle DELETE via POST + CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  csrf_validate_post();

  $deleteId = (int)$_POST['delete_id'];
  if ($deleteId > 0) {
    try {
      $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE id = ?");
      $stmt->execute([$deleteId]);
    } catch (Throwable $e) {
      // If category is linked to menu_items, FK may block delete
      $error = "Cannot delete category because it is used by menu items.";
    }
  }
}

// Handle CREATE / UPDATE via POST + CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
  csrf_validate_post();

  $name = trim($_POST['name'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $updateId = (int)($_POST['update_id'] ?? 0);

  if ($name === '') {
    $error = "Category name is required.";
  } else {
    if ($updateId > 0) {
      $stmt = $pdo->prepare("UPDATE menu_categories SET name = ?, description = ? WHERE id = ?");
      $stmt->execute([$name, $desc, $updateId]);
    } else {
      $stmt = $pdo->prepare("INSERT INTO menu_categories (name, description) VALUES (?, ?)");
      $stmt->execute([$name, $desc]);
    }
    header("Location: categories.php");
    exit;
  }
}

// Load category for EDIT form
$edit = null;
if ($action === 'edit' && $id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE id = ?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}

// Get all categories
$cats = $pdo->query("SELECT * FROM menu_categories ORDER BY name")->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<h1>Admin - Categories</h1>

<div class="card">
  <a class="btn" href="/restaurant/admin/items.php">Manage Items</a>
  <a class="btn" href="/restaurant/admin/orders.php">Manage Orders</a>
  <a class="btn" href="/restaurant/admin/logout.php">Logout</a>
</div>

<?php if ($error): ?>
  <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
  <h3><?= $edit ? "Edit Category" : "Add Category" ?></h3>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <?php if ($edit): ?>
      <input type="hidden" name="update_id" value="<?= (int)$edit['id'] ?>">
    <?php endif; ?>

    <label>Name</label>
    <input name="name" required value="<?= e($edit['name'] ?? '') ?>">

    <label>Description</label>
    <input name="description" value="<?= e($edit['description'] ?? '') ?>">

    <div style="margin-top:12px;">
      <button class="btn primary" type="submit"><?= $edit ? "Update" : "Create" ?></button>
      <?php if ($edit): ?>
        <a class="btn" href="categories.php">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <h3>All Categories</h3>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Created</th>
      <th>Actions</th>
    </tr>

    <?php foreach ($cats as $c): ?>
      <tr>
        <td><?= (int)$c['id'] ?></td>
        <td><?= e($c['name']) ?></td>
        <td><?= e($c['description'] ?? '') ?></td>
        <td><?= e($c['created_at']) ?></td>
        <td style="white-space:nowrap;">
          <a class="btn" href="categories.php?action=edit&id=<?= (int)$c['id'] ?>">Edit</a>

          <form method="post" style="display:inline;" onsubmit="return confirm('Delete this category?');">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="delete_id" value="<?= (int)$c['id'] ?>">
            <button class="btn danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
