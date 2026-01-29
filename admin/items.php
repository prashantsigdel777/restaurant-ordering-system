<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_admin();

$error = '';
$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// ----------------------
// DELETE (POST + CSRF)
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  csrf_validate_post();

  $deleteId = (int)$_POST['delete_id'];
  if ($deleteId > 0) {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$deleteId]);
  }
  header("Location: items.php");
  exit;
}

// ----------------------
// CREATE / UPDATE (POST + CSRF)
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
  csrf_validate_post();

  $category_id  = (int)($_POST['category_id'] ?? 0);
  $name         = trim($_POST['name'] ?? '');
  $cuisine      = trim($_POST['cuisine'] ?? '');
  $priceRaw     = $_POST['price'] ?? '';
  $description  = trim($_POST['description'] ?? '');
  $available    = isset($_POST['is_available']) ? 1 : 0;

  // local image filename only
  $image_file = trim($_POST['image_url'] ?? '');

  $updateId = (int)($_POST['update_id'] ?? 0);

  // Validation
  if ($category_id <= 0) {
    $error = "Please select a category.";
  } elseif ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 80) {
    $error = "Item name must be 2–80 characters.";
  } elseif ($cuisine === '' || mb_strlen($cuisine) < 2 || mb_strlen($cuisine) > 40) {
    $error = "Cuisine must be 2–40 characters.";
  } elseif ($priceRaw === '' || !is_numeric($priceRaw)) {
    $error = "Price must be a valid number.";
  } else {
    $price = (float)$priceRaw;
    if ($price < 0) {
      $error = "Price cannot be negative.";
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/', $image_file)) {
      $error = "Image must be a local filename like pizza.jpg (stored in assets/images/).";
    }
  }

  if ($error === '') {
    if ($updateId > 0) {
      $stmt = $pdo->prepare("
        UPDATE menu_items
        SET category_id=?, name=?, cuisine=?, price=?, is_available=?, description=?, image_url=?
        WHERE id=?
      ");
      $stmt->execute([$category_id, $name, $cuisine, $price, $available, $description, $image_file, $updateId]);
    } else {
      $stmt = $pdo->prepare("
        INSERT INTO menu_items (category_id, name, cuisine, price, is_available, description, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      ");
      $stmt->execute([$category_id, $name, $cuisine, $price, $available, $description, $image_file]);
    }

    header("Location: items.php");
    exit;
  }
}

// ----------------------
// EDIT MODE (GET)
// ----------------------
$edit = null;
if ($action === 'edit' && $id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}

// Categories for dropdown
$cats = $pdo->query("SELECT id, name FROM menu_categories ORDER BY name")->fetchAll();

// Items list
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
  <a class="btn" href="/restaurant/admin/categories.php">Manage Categories</a>
  <a class="btn" href="/restaurant/admin/orders.php">Manage Orders</a>
  <a class="btn" href="/restaurant/admin/logout.php">Logout</a>
</div>

<?php if ($error): ?>
  <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<!-- ADD / EDIT FORM -->
<div class="card">
  <h3><?= $edit ? "Edit Item" : "Add Item" ?></h3>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <?php if ($edit): ?>
      <input type="hidden" name="update_id" value="<?= (int)$edit['id'] ?>">
    <?php endif; ?>

    <label>Category</label>
    <select name="category_id" required>
      <option value="">Select category</option>
      <?php foreach ($cats as $c): ?>
        <?php
          $selected = '';
          if ($edit && (int)$edit['category_id'] === (int)$c['id']) $selected = 'selected';
        ?>
        <option value="<?= (int)$c['id'] ?>" <?= $selected ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Item Name</label>
    <input name="name" required minlength="2" maxlength="80" value="<?= e($edit['name'] ?? '') ?>">

    <label>Cuisine</label>
    <input name="cuisine" required minlength="2" maxlength="40" value="<?= e($edit['cuisine'] ?? '') ?>" placeholder="Italian, Indian...">

    <label>Price</label>
    <input name="price" type="number" min="0" step="0.01" required value="<?= e((string)($edit['price'] ?? '')) ?>">

    <label>Description</label>
    <input name="description" maxlength="200" value="<?= e($edit['description'] ?? '') ?>">

    <label>Image File (assets/images/)</label>
    <input
      name="image_url"
      required
      pattern="[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)"
      placeholder="pizza.jpg"
      value="<?= e($edit['image_url'] ?? 'placeholder.jpg') ?>"
    >
    <small class="muted">Example: pizza.jpg (must exist in /restaurant/assets/images/)</small>

    <label style="margin-top:10px;">
      <input type="checkbox" name="is_available" <?= (!$edit || (int)$edit['is_available'] === 1) ? 'checked' : '' ?>>
      Available
    </label>

    <div style="margin-top:12px;">
      <button class="btn primary" type="submit"><?= $edit ? "Update Item" : "Add Item" ?></button>
      <?php if ($edit): ?>
        <a class="btn" href="items.php">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- ITEMS TABLE -->
<div class="card">
  <h3>All Items</h3>

  <table>
    <tr>
      <th>ID</th>
      <th>Category</th>
      <th>Name</th>
      <th>Cuisine</th>
      <th>Price</th>
      <th>Available</th>
      <th>Image</th>
      <th>Actions</th>
    </tr>

    <?php foreach ($items as $it): ?>
      <tr>
        <td><?= (int)$it['id'] ?></td>
        <td><?= e($it['category_name']) ?></td>
        <td><?= e($it['name']) ?></td>
        <td><?= e($it['cuisine']) ?></td>
        <td><?= money($it['price']) ?></td>

        <td><?= ((int)$it['is_available'] === 1) ? 'Yes' : 'No' ?></td>
        <?php $img = $it['image_url'] ?? 'placeholder.jpg'; ?>
<td>
  <div style="display:flex; gap:10px; align-items:center;">
    <img
      src="/restaurant/assets/images/<?= e($img) ?>"
      alt="img"
      loading="lazy"
      style="width:48px; height:48px; object-fit:cover; border-radius:10px; border:1px solid #eee;"
    >
    <span><?= e($img) ?></span>
  </div>
</td>


        <td style="white-space:nowrap;">
          <a class="btn" href="items.php?action=edit&id=<?= (int)$it['id'] ?>">Edit</a>

          <form method="post" style="display:inline;" onsubmit="return confirm('Delete this item?');">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="delete_id" value="<?= (int)$it['id'] ?>">
            <button class="btn danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
   </table>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
