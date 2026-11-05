<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/functions.php';
if(current_user()) redirect('index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');
 $stmt=$pdo->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email=?');$stmt->execute([$email]);$user=$stmt->fetch();
 if($user && password_verify($password,$user['password_hash'])){session_regenerate_id(true);$_SESSION['user']=['id'=>(int)$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role']];set_flash('Signed in successfully.');$next=($_SESSION['after_login']??'index.php');unset($_SESSION['after_login']);redirect($next);}
 $error='Email or password is incorrect.';
}
$pageTitle='Login';require __DIR__.'/includes/header.php';
?>
<section class="auth-wrap"><div class="auth-intro"><p class="eyebrow">Welcome back</p><h1>Shopping made simple.</h1><p>Sign in to manage your orders and continue exploring the catalogue.</p></div><form method="post" class="panel form-panel auth-form"><h2>Sign in</h2><?php if($error): ?><div class="error-list"><p><?= e($error) ?></p></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email address<input type="email" name="email" required autocomplete="email" value="<?= e($_POST['email']??'') ?>"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="button button-block">Sign in</button><p class="muted">New here? <a href="register.php">Create an account</a>.</p></form></section>
<?php require __DIR__.'/includes/footer.php'; ?>
