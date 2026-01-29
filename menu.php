<?php
require_once __DIR__ . '/header.php';

// --- Search filters ---
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$cuisine  = trim($_GET['cuisine'] ?? '');
$avail    = trim($_GET['available'] ?? '1'); // default: available only

$where = [];
$params = [];

$sql = "
  SELECT mi.*, mc.name AS category
  FROM menu_items mi
  JOIN menu_categories mc ON mc.id = mi.category_id
  WHERE 1=1
";

if ($minPrice !== '' && is_numeric($minPrice) && (float)$minPrice >= 0) {
  $sql .= " AND mi.price >= ?";
  $params[] = (float)$minPrice;
}

if ($maxPrice !== '' && is_numeric($maxPrice) && (float)$maxPrice >= 0) {
  $sql .= " AND mi.price <= ?";
  $params[] = (float)$maxPrice;
}

if ($cuisine !== '') {
  $sql .= " AND mi.cuisine = ?";
  $params[] = $cuisine;
}

if ($avail === '1' || $avail === '0') {
  $sql .= " AND mi.is_available = ?";
  $params[] = (int)$avail;
}

$sql .= " ORDER BY mc.name, mi.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

// cuisines dropdown
$cuisines = $pdo->query("SELECT DISTINCT cuisine FROM menu_items ORDER BY cuisine")->fetchAll(PDO::FETCH_COLUMN);
?>

<h1>Menu</h1>

<!-- SEARCH BOX -->
<div class="card">
  <form method="get" class="grid">
    <label>Min Price (Rs.)
      <input type="number" step="0.01" min="0" name="min_price" value="<?= e($minPrice) ?>" placeholder="0">
    </label>

    <label>Max Price (Rs.)
      <input type="number" step="0.01" min="0" name="max_price" value="<?= e($maxPrice) ?>" placeholder="500">
    </label>

    <label>Cuisine
      <select name="cuisine">
        <option value="">Any</option>
        <?php foreach ($cuisines as $cu): ?>
          <option value="<?= e($cu) ?>" <?= $cu === $cuisine ? 'selected' : '' ?>>
            <?= e($cu) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Availability
      <select name="available">
        <option value="1" <?= $avail === '1' ? 'selected' : '' ?>>Available</option>
        <option value=""  <?= $avail === '' ? 'selected' : '' ?>>Any</option>
        <option value="0" <?= $avail === '0' ? 'selected' : '' ?>>Unavailable</option>
      </select>
    </label>

    <div style="align-self:end; display:flex; gap:10px;">
      <button class="btn primary" type="submit">Search</button>
      <a class="btn" href="/restaurant/menu.php">Reset</a>
    </div>
  </form>

  <p class="muted" style="margin-top:10px;">
  
  </p>
</div>

<!-- MENU ITEMS -->
<div class="grid">
<?php foreach ($items as $i): ?>
  <?php
    $imgFile = trim((string)($i['image_url'] ?? ''));
    if ($imgFile === '') $imgFile = 'placeholder.jpg';
    $imgPath = "/restaurant/assets/images/" . $imgFile;
  ?>
  <div class="card">
    <img
      src="<?= e($imgPath) ?>"
      class="menu-img"
      loading="lazy"
      alt="<?= e($i['name']) ?>"
    >

    <h3><?= e($i['name']) ?></h3>
    <small><?= e($i['category']) ?> · <?= e($i['cuisine']) ?></small>

    <p><?= e($i['description'] ?? '') ?></p>
    <strong><?= function_exists('money') ? money($i['price']) : ('Rs. ' . number_format((float)$i['price'], 2)) ?></strong>

    <div style="margin-top:12px;">
      <?php if ((int)$i['is_available'] === 1): ?>
        <button class="btn" data-add-to-cart="<?= (int)$i['id'] ?>">Add to Cart</button>
      <?php else: ?>
        <span class="badge">Unavailable</span>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
