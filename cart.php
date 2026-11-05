<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: 0;
    if ($action === 'add' && $id > 0) {
        $stmt = $pdo->prepare("SELECT id,stock,active FROM products WHERE id=?"); $stmt->execute([$id]); $p=$stmt->fetch();
        if ($p && (int)$p['active'] === 1 && (int)$p['stock'] > 0) {
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            $_SESSION['cart'][$id] = min((int)$p['stock'], (int)($_SESSION['cart'][$id] ?? 0) + $qty);
            set_flash('Product added to your cart.');
        } else set_flash('That product is not currently available.');
        redirect('cart.php');
    }
    if ($action === 'update') {
        foreach (($_POST['quantities'] ?? []) as $key => $value) {
            $pid = (int)$key; $qty = (int)$value;
            if ($qty <= 0) { unset($_SESSION['cart'][$pid]); continue; }
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=? AND active=1"); $stmt->execute([$pid]); $p=$stmt->fetch();
            if ($p) $_SESSION['cart'][$pid] = min($qty, (int)$p['stock']); else unset($_SESSION['cart'][$pid]);
        }
        set_flash('Cart updated.'); redirect('cart.php');
    }
    if ($action === 'remove' && $id > 0) { unset($_SESSION['cart'][$id]); set_flash('Item removed.'); redirect('cart.php'); }
}
$items = cart_items($pdo); $total = array_sum(array_column($items, 'line_total'));
$pageTitle='Your cart'; require __DIR__.'/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">Almost there</p><h1>Your shopping cart</h1><p>Review quantities before checking out.</p></section>
<?php if (!$items): ?><div class="empty-state"><div class="empty-icon">🛒</div><h2>Your cart is empty</h2><p>Find something you like in our sample catalogue.</p><a class="button" href="products.php">Browse products</a></div>
<?php else: ?>
<form method="post" class="cart-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update">
<div class="table-wrap"><table><thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead><tbody>
<?php foreach($items as $item): ?><tr><td><div class="cart-product"><img src="<?= e($item['image'] ?: 'assets/images/product-placeholder.svg') ?>" alt=""><strong><?= e($item['name']) ?></strong></div></td><td><?= money($item['price']) ?></td><td><input class="qty-input" type="number" name="quantities[<?= (int)$item['id'] ?>]" min="0" max="<?= (int)$item['stock'] ?>" value="<?= (int)$item['quantity'] ?>"></td><td><?= money($item['line_total']) ?></td><td><button class="link-button danger" form="remove-<?= (int)$item['id'] ?>">Remove</button></td></tr><?php endforeach; ?>
</tbody></table></div><button class="button button-outline" type="submit">Update cart</button></form>
<div class="cart-summary"><div><span>Subtotal</span><strong><?= money($total) ?></strong></div><div><span>Shipping</span><strong>Free</strong></div><div class="summary-total"><span>Total</span><strong><?= money($total) ?></strong></div><a class="button button-block" href="checkout.php">Proceed to checkout</a><p class="muted tiny">Demo checkout uses Cash on Delivery; no payment details are collected.</p></div>
<?php foreach($items as $item): ?><form id="remove-<?= (int)$item['id'] ?>" method="post" class="hidden"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>"></form><?php endforeach; ?>
<?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
