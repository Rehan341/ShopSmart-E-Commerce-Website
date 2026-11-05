<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.active=1 ORDER BY p.created_at DESC LIMIT 8");
$products = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="hero-copy">
    <p class="eyebrow">A simple online shopping demo</p>
    <h1>Find your next<br><span>everyday essential.</span></h1>
    <p>Explore sample products, add items to your cart, and place a demo Cash on Delivery order.</p>
    <a class="button button-light" href="products.php">Explore products</a>
  </div>
  <div class="hero-art" aria-hidden="true"><div class="art-card">✦<br><strong>GOOD<br>FINDS</strong><br>for everyday</div></div>
</section>
<section class="section-heading"><div><p class="eyebrow">Hand-picked for the demo</p><h2>Featured products</h2></div><a href="products.php" class="text-link">View all products →</a></section>
<div class="product-grid">
<?php foreach ($products as $product): ?>
  <article class="product-card">
    <a href="product-details.php?id=<?= (int)$product['id'] ?>" class="product-image">
      <img src="<?= e($product['image'] ?: 'assets/images/product-placeholder.svg') ?>" alt="<?= e($product['name']) ?>" onerror="this.src='assets/images/product-placeholder.svg'">
    </a>
    <div class="product-info"><span class="category"><?= e($product['category_name'] ?? 'General') ?></span>
      <h3><a href="product-details.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a></h3>
      <div class="product-bottom"><strong><?= money($product['price']) ?></strong><a class="small-link" href="product-details.php?id=<?= (int)$product['id'] ?>">View item</a></div>
    </div>
  </article>
<?php endforeach; ?>
</div>
<section class="benefit-strip"><div>✓ Secure password storage</div><div>✓ Database-backed orders</div><div>✓ Admin product management</div><div>✓ No real payment collected</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
