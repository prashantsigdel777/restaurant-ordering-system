<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_admin();

$error = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

/* DELETE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  csrf_validate_post();
  $deleteId = (int)$_POST['delete_id'];

  $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id=?");
  $stmt->execute([$deleteId]);

  header("Location: {$BASE_URL}/admin/items.php");
  exit;
}

/* CREATE / UPDATE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
  csrf_validate_post();

  $category_id = (int)($_POST['category_id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $cuisine = trim($_POST['cuisine'] ?? '');
  $priceRaw = $_POST['price'] ?? '';
  $desc = trim($_POST['description'] ?? '');
  $available = isset($_POST['is_available']) ? 1 : 0;
  $image = trim($_POST['image_url'] ?? 'placeholder.jpg');
  $updateId = (int)($_POST['update_id'] ?? 0);

  if ($category_id <= 0) $error = "Select category.";
  elseif ($name === '' || mb_strlen($name) < 2) $error = "Enter item name.";
  elseif ($cuisine === '' || mb_strlen($cuisine) < 2) $error = "Enter cuisine.";
  elseif ($priceRaw === '' || !is_numeric($priceRaw)) $error = "Price must be numeric.";
  else {
    $price = (float)$priceRaw;
    if ($price < 0) $error = "Price cannot be negative.";
    elseif (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/', $image))
      $error = "Image must be filename like momo.png in assets/images/.";
  }

  if ($error === '') {
    if ($updateId > 0) {
      $stmt = $pdo->prepare("
        UPDATE menu_items
        SET category_id=?, name=?, cuisine=?, price=?, is_available=?, description=?, image_url=?
        WHERE id=?
      ");
      $stmt->execute([$category_id, $name, $cuisine, $price, $available, $desc, $image, $updateId]);
    } else {
      $stmt = $pdo->prepare("
        INSERT INTO menu_items (category_id, name, cuisine, price, is_available, description, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      ");
      $stmt->execute([$category_id, $name, $cuisine, $price, $available, $desc, $image]);
    }

    header("Location: {$BASE_URL}/admin/items.php");
    exit;
  }
}

/* EDIT MODE */
$edit = null;
if ($action === 'edit' && $id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id=?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}

/* Categories */
$cats = $pdo->query("SELECT id,name FROM menu_categories ORDER BY name")->fetchAll();

/* Items */
$items = $pdo->query("
  SELECT mi.*, mc.name AS category_name
  FROM menu_items mi
  JOIN menu_categories mc ON mc.id = mi.category_id
  ORDER BY mc.name, mi.name
")->fetchAll();

require_once __DIR__ . '/../header.php';
?>

<h1>Admin - Manage Items</h1>

<div class="card">
  <a class="btn" href="<?= $BASE_URL ?>/admin/categories.php">Categories</a>
  <a class="btn" href="<?= $BASE_URL ?>/admin/orders.php">Orders</a>
  <a class="btn" href="<?= $BASE_URL ?>/admin/logout.php">Logout</a>
</div>

<?php if ($error): ?>
  <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
  <h3><?= $edit ? "Edit Item" : "Add Item" ?></h3>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <?php if ($edit): ?><input type="hidden" name="update_id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>

    <label>Category</label>
    <select name="category_id" required>
      <option value="">Select category</option>
      <?php foreach ($cats as $c): ?>
        <?php $sel = ($edit && (int)$edit['category_id']===(int)$c['id']) ? 'selected' : ''; ?>
        <option value="<?= (int)$c['id'] ?>" <?= $sel ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Item Name</label>
    <input name="name" required value="<?= e($edit['name'] ?? '') ?>">

    <label>Cuisine</label>
    <input name="cuisine" required value="<?= e($edit['cuisine'] ?? '') ?>">

    <label>Price (Rs.)</label>
    <input name="price" type="number" min="0" step="0.01" required value="<?= e((string)($edit['price'] ?? '')) ?>">

    <label>Description</label>
    <input name="description" value="<?= e($edit['description'] ?? '') ?>">

    <label>Image filename (assets/images/)</label>
    <input name="image_url" required value="<?= e($edit['image_url'] ?? 'placeholder.jpg') ?>">

    <label style="margin-top:10px;">
      <input type="checkbox" name="is_available" <?= (!$edit || (int)$edit['is_available']===1) ? 'checked' : '' ?>>
      Available
    </label>

    <div style="margin-top:12px;">
      <button class="btn primary" type="submit"><?= $edit ? "Update" : "Add" ?></button>
      <?php if ($edit): ?><a class="btn" href="<?= $BASE_URL ?>/admin/items.php">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card">
  <h3>All Items</h3>
  <table>
    <tr><th>ID</th><th>Category</th><th>Name</th><th>Cuisine</th><th>Price</th><th>Available</th><th>Image</th><th class="actions">Actions</th></tr>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?= (int)$it['id'] ?></td>
        <td><?= e($it['category_name']) ?></td>
        <td><?= e($it['name']) ?></td>
        <td><?= e($it['cuisine']) ?></td>
        <td><?= money($it['price']) ?></td>
        <td><?= ((int)$it['is_available']===1) ? 'Yes' : 'No' ?></td>
        <td><?= e($it['image_url']) ?></td>
        <td class="actions">
          <a class="btn" href="<?= $BASE_URL ?>/admin/items.php?action=edit&id=<?= (int)$it['id'] ?>">Edit</a>
          <form method="post" style="display:inline;" onsubmit="return confirm('Delete this item?');">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="delete_id" value="<?= (int)$it['id'] ?>">
            <button class="btn" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
