<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.active=1");
$stmt->execute([$id]); $product = $stmt->fetch();
if (!$product) { http_response_code(404); $pageTitle='Product not found'; require __DIR__.'/includes/header.php'; echo '<div class="empty-state"><h1>Product not found</h1><a href="products.php">Back to products</a></div>'; require __DIR__.'/includes/footer.php'; exit; }
$pageTitle = $product['name'];
require __DIR__ . '/includes/header.php';
?>
<div class="detail-layout">
  <div class="detail-image"><img src="<?= e($product['image'] ?: 'assets/images/product-placeholder.svg') ?>" alt="<?= e($product['name']) ?>" onerror="this.src='assets/images/product-placeholder.svg'"></div>
  <div class="detail-copy"><p class="eyebrow"><?= e($product['category_name'] ?? 'General') ?></p><h1><?= e($product['name']) ?></h1><p class="detail-price"><?= money($product['price']) ?></p><p><?= nl2br(e($product['description'])) ?></p><p class="stock <?= (int)$product['stock']>0?'in-stock':'out-stock' ?>"><?= (int)$product['stock']>0 ? (int)$product['stock'].' in stock' : 'Out of stock' ?></p>
  <?php if ((int)$product['stock']>0): ?><form method="post" action="cart.php" class="add-cart-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>"><label>Quantity <input type="number" name="quantity" min="1" max="<?= (int)$product['stock'] ?>" value="1" required></label><button class="button" type="submit">Add to cart</button></form><?php endif; ?>
  <a class="text-link" href="products.php">← Continue shopping</a></div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
