<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

$error = '';

// If already logged in
if (!empty($_SESSION['is_admin'])) {
  header("Location: index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // If CSRF function exists, enforce it (safe)
  if (function_exists('csrf_validate_post')) {
    csrf_validate_post();
  }

  $u = trim($_POST['username'] ?? '');
  $p = trim($_POST['password'] ?? '');

  if ($u === 'admin' && $p === 'admin123') {
    $_SESSION['is_admin'] = true;
    header("Location: index.php"); // relative, server-safe
    exit;
  } else {
    $error = "Invalid login.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <!-- Use BASE_URL if available, else relative fallback -->
  <link rel="stylesheet" href="<?php echo isset($BASE_URL) ? $BASE_URL . '/assets/style.css' : '../assets/style.css'; ?>">
</head>
<body>
<main class="wrap">
  <h1>Admin Login</h1>

  <?php if ($error): ?>
    <div class="error"><?php echo e($error); ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="post">
      <?php if (function_exists('csrf_token')): ?>
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
      <?php endif; ?>

      <label>Username</label>
      <input name="username" required>

      <label>Password</label>
      <input name="password" type="password" required>

      <div style="margin-top:12px; display:flex; gap:10px;">
        <button class="btn primary" type="submit">Login</button>
        <a class="btn" href="<?php echo isset($BASE_URL) ? $BASE_URL . '/index.php' : '../index.php'; ?>">Back</a>
      </div>
    </form>
  </div>
</main>
</body>
</html>
