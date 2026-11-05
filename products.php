<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Products';
$q = trim((string)($_GET['q'] ?? ''));
$category = (int)($_GET['category'] ?? 0);
$categories = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll();
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.active=1";
$params = [];
if ($q !== '') { $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($category > 0) { $sql .= " AND p.category_id=?"; $params[] = $category; }
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">The catalogue</p><h1>Shop products</h1><p>Search the sample catalogue or filter by category.</p></section>
<form class="filter-bar" method="get">
  <input type="search" name="q" placeholder="Search products..." value="<?= e($q) ?>">
  <select name="category"><option value="0">All categories</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $category===(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
  <button class="button" type="submit">Search</button>
  <a class="button button-outline" href="products.php">Reset</a>
</form>
<p class="muted"><?= count($products) ?> product(s) found</p>
<div class="product-grid">
<?php foreach ($products as $product): ?>
  <article class="product-card">
    <a href="product-details.php?id=<?= (int)$product['id'] ?>" class="product-image"><img src="<?= e($product['image'] ?: 'assets/images/product-placeholder.svg') ?>" alt="<?= e($product['name']) ?>" onerror="this.src='assets/images/product-placeholder.svg'"></a>
    <div class="product-info"><span class="category"><?= e($product['category_name'] ?? 'General') ?></span><h3><a href="product-details.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a></h3><p class="muted clamp"><?= e($product['description']) ?></p><div class="product-bottom"><strong><?= money($product['price']) ?></strong><a class="small-link" href="product-details.php?id=<?= (int)$product['id'] ?>">Details →</a></div></div>
  </article>
<?php endforeach; ?>
</div>
<?php if (!$products): ?><div class="empty-state"><h2>No products found</h2><p>Try another search term or reset the filters.</p></div><?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
