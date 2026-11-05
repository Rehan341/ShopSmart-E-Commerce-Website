<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$items = cart_items($pdo);
if (!$items) { set_flash('Your cart is empty.'); redirect('cart.php'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['full_name'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $postal = trim((string)($_POST['postal_code'] ?? ''));
    if (mb_strlen($name) < 2) $errors[]='Enter your full name.';
    if (!preg_match('/^[0-9+() -]{7,20}$/', $phone)) $errors[]='Enter a valid contact number.';
    if (mb_strlen($address) < 8) $errors[]='Enter a complete delivery address.';
    if (mb_strlen($city) < 2) $errors[]='Enter your city.';
    if (!preg_match('/^[A-Za-z0-9 -]{3,12}$/', $postal)) $errors[]='Enter a valid postal code.';
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $freshItems=[]; $total=0.0;
            foreach ($_SESSION['cart'] as $pid=>$qty) {
                $stmt=$pdo->prepare("SELECT id,name,price,stock,active FROM products WHERE id=? FOR UPDATE");
                $stmt->execute([(int)$pid]); $p=$stmt->fetch();
                if (!$p || (int)$p['active']!==1 || (int)$p['stock'] < (int)$qty || (int)$qty < 1) {
                    throw new RuntimeException('Stock changed for one of your items. Please update your cart.');
                }
                $p['quantity']=(int)$qty; $p['line_total']=(float)$p['price']*(int)$qty;
                $freshItems[]=$p; $total += $p['line_total'];
            }
            $stmt=$pdo->prepare("INSERT INTO orders (user_id,full_name,phone,address,city,postal_code,total,payment_method,status) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([current_user()['id'],$name,$phone,$address,$city,$postal,$total,'COD','Placed']);
            $orderId=(int)$pdo->lastInsertId();
            $line=$pdo->prepare("INSERT INTO order_items (order_id,product_id,product_name,unit_price,quantity,line_total) VALUES (?,?,?,?,?,?)");
            $stock=$pdo->prepare("UPDATE products SET stock=stock-? WHERE id=?");
            foreach($freshItems as $p) {
                $line->execute([$orderId,$p['id'],$p['name'],$p['price'],$p['quantity'],$p['line_total']]);
                $stock->execute([$p['quantity'],$p['id']]);
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            set_flash("Order #{$orderId} placed successfully.");
            redirect('orders.php');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $ex instanceof RuntimeException ? $ex->getMessage() : 'We could not place your order. Please try again.';
        }
    }
}
$total=array_sum(array_column($items,'line_total'));
$pageTitle='Checkout'; require __DIR__.'/includes/header.php';
?>
<section class="page-heading"><p class="eyebrow">Delivery details</p><h1>Checkout</h1><p>Cash on Delivery demo checkout.</p></section>
<?php if($errors): ?><div class="error-list"><?php foreach($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<div class="checkout-layout"><form method="post" class="panel form-panel"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Full name<input name="full_name" required minlength="2" value="<?= e($_POST['full_name'] ?? current_user()['name']) ?>"></label>
<label>Phone number<input name="phone" required value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+91 98765 43210"></label>
<label>Delivery address<textarea name="address" rows="3" required><?= e($_POST['address'] ?? '') ?></textarea></label>
<div class="form-row"><label>City<input name="city" required value="<?= e($_POST['city'] ?? '') ?>"></label><label>Postal code<input name="postal_code" required value="<?= e($_POST['postal_code'] ?? '') ?>"></label></div>
<div class="payment-note"><strong>Payment method: Cash on Delivery</strong><p>No online payment or card data is processed by this student demo.</p></div>
<button class="button button-block" type="submit">Place order · <?= money($total) ?></button></form>
<aside class="panel order-summary"><h2>Order summary</h2><?php foreach($items as $item): ?><div class="summary-line"><span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span><strong><?= money($item['line_total']) ?></strong></div><?php endforeach; ?><hr><div class="summary-line summary-total"><span>Total</span><strong><?= money($total) ?></strong></div></aside></div>
<?php require __DIR__.'/includes/footer.php'; ?>
