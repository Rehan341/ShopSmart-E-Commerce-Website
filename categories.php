<?php
require __DIR__.'/_guard.php';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$name=trim((string)($_POST['name']??''));if(mb_strlen($name)<2)$error='Enter a category name.';else{try{$stmt=$pdo->prepare('INSERT INTO categories(name) VALUES(?)');$stmt->execute([$name]);set_flash('Category created.');redirect('categories.php');}catch(PDOException $e){$error='That category may already exist.';}}}
$categories=$pdo->query('SELECT c.id,c.name,COUNT(p.id) product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id,c.name ORDER BY c.name')->fetchAll();
$pageTitle='Manage categories';require __DIR__.'/../includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">Admin</p><h1>Manage categories</h1><p>Categories help visitors filter the catalogue.</p></section>
<?php if($error): ?><div class="error-list"><p><?= e($error) ?></p></div><?php endif; ?>
<form method="post" class="panel inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>New category<input name="name" required placeholder="e.g. Home & Living"></label><button class="button">Add category</button></form>
<div class="table-wrap"><table><thead><tr><th>Category</th><th>Products</th></tr></thead><tbody><?php foreach($categories as $c): ?><tr><td><?= e($c['name']) ?></td><td><?= (int)$c['product_count'] ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
