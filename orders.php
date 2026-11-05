<?php
require_once __DIR__.'/config/database.php';require_once __DIR__.'/includes/functions.php';require_login();
$stmt=$pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC');$stmt->execute([current_user()['id']]);$orders=$stmt->fetchAll();
$pageTitle='My orders';require __DIR__.'/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">Your purchases</p><h1>My orders</h1><p>Order history for your account.</p></section>
<?php if(!$orders): ?><div class="empty-state"><h2>No orders yet</h2><p>Once you place an order, its status will appear here.</p><a class="button" href="products.php">Start shopping</a></div><?php else: ?>
<div class="order-list"><?php foreach($orders as $order): ?><article class="panel order-card"><div class="order-card-head"><div><span class="muted">Order #<?= (int)$order['id'] ?></span><h2><?= date('d M Y, H:i',strtotime($order['created_at'])) ?></h2></div><span class="status-pill"><?= e($order['status']) ?></span></div><div class="order-card-foot"><span><?= e($order['payment_method']) ?> · <?= e($order['city']) ?></span><strong><?= money($order['total']) ?></strong></div><details><summary>View items and delivery address</summary><?php $lines=$pdo->prepare('SELECT * FROM order_items WHERE order_id=?');$lines->execute([$order['id']]);foreach($lines->fetchAll() as $line): ?><p><?= e($line['product_name']) ?> × <?= (int)$line['quantity'] ?> — <?= money($line['line_total']) ?></p><?php endforeach; ?><p><?= e($order['full_name']) ?>, <?= e($order['address']) ?>, <?= e($order['city']) ?> <?= e($order['postal_code']) ?></p></details></article><?php endforeach; ?></div>
<?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
