<?php
/** Shared helpers. Keep output escaped and use prepared SQL statements. */
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Invalid or expired form token. Go back and try again.');
    }
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        $_SESSION['flash'] = 'Please sign in to continue.';
        redirect('login.php');
    }
}

function require_admin(): void {
    if (!current_user() || current_user()['role'] !== 'admin') {
        http_response_code(403);
        exit('Access denied. Sign in with an administrator account.');
    }
}

function set_flash(string $message): void {
    $_SESSION['flash'] = $message;
}

function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        echo '<div class="flash">' . e($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}

function money(float|int|string $amount): string {
    return '₹' . number_format((float)$amount, 2);
}

function cart_count(): int {
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}

function cart_items(PDO $pdo): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_values(array_filter(array_map('intval', array_keys($cart))));
    if (!$ids) return [];
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, image, stock FROM products WHERE active = 1 AND id IN ($marks)");
    $stmt->execute($ids);
    $rows = [];
    foreach ($stmt->fetchAll() as $product) {
        $qty = max(1, (int)($cart[$product['id']] ?? 1));
        $product['quantity'] = min($qty, (int)$product['stock']);
        $product['line_total'] = (float)$product['price'] * $product['quantity'];
        $rows[] = $product;
    }
    return $rows;
}
