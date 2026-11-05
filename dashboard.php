<?php
require __DIR__.'/_guard.php';
$stats=[
 'products'=>(int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
 'orders'=>(int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
 'customers'=>(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
 'sales'=>(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'Cancelled'")->fetchColumn()
];
$recent=$pdo->query('SELECT id,full_name,total,status,created_at FROM orders ORDER BY created_at DESC LIMIT 8')->fetchAll();
$pageTitle='Admin dashboard';require __DIR__.'/../includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">Store control centre</p><h1>Admin dashboard</h1><p>Manage the catalogue and track demo orders.</p></section>
<div class="stats-grid"><div class="stat-card"><span>Products</span><strong><?= $stats['products'] ?></strong></div><div class="stat-card"><span>Orders</span><strong><?= $stats['orders'] ?></strong></div><div class="stat-card"><span>Customers</span><strong><?= $stats['customers'] ?></strong></div><div class="stat-card"><span>Order value</span><strong><?= money($stats['sales']) ?></strong></div></div>
<div class="admin-links"><a class="button" href="products.php">Manage products</a><a class="button button-outline" href="categories.php">Manage categories</a><a class="button button-outline" href="orders.php">Manage orders</a></div>
<h2>Recent orders</h2><div class="table-wrap"><table><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody><?php foreach($recent as $r): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['full_name']) ?></td><td><?= money($r['total']) ?></td><td><span class="status-pill"><?= e($r['status']) ?></span></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
