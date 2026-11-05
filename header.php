<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'ShopSmart Store';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | ShopSmart</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script defer src="assets/js/script.js"></script>
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="index.php">Shop<span>Smart</span></a>
    <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">☰</button>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="products.php">Products</a>
      <a href="cart.php">Cart <span class="cart-count"><?= cart_count() ?></span></a>
      <?php if (current_user()): ?>
        <a href="orders.php">My Orders</a>
        <?php if (current_user()['role'] === 'admin'): ?><a href="admin/dashboard.php">Admin</a><?php endif; ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a class="nav-cta" href="register.php">Create account</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container main-content">
<?php show_flash(); ?>
