<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if (!empty($_SESSION['is_admin'])) {
  header('Location: /restaurant/admin/index.php');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_validate_post();
  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');

  if ($u === ADMIN_USER && $p === ADMIN_PASS) {
    $_SESSION['is_admin'] = true;
    header('Location: /restaurant/admin/index.php');
    exit;
  }
  $error = 'Invalid admin login.';
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="/restaurant/assets/style.css">
</head>
<body>
<main class="wrap">
  <h1>Admin Login</h1>

  <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

  <div class="card">
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <label>Username</label>
      <input name="username" required>
      <label>Password</label>
      <input name="password" type="password" required>
      <button class="btn primary" type="submit">Login</button>
      <a class="btn" href="/restaurant/index.php">Back</a>
    </form>
    
  </div>
</main>
</body>
</html>
