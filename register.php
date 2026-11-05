<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/functions.php';
if (current_user()) redirect('index.php');
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $name=trim((string)($_POST['name']??'')); $email=strtolower(trim((string)($_POST['email']??''))); $password=(string)($_POST['password']??'');
 if(mb_strlen($name)<2) $errors[]='Name must contain at least 2 characters.';
 if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
 if(strlen($password)<8) $errors[]='Password must be at least 8 characters.';
 if(!$errors){$check=$pdo->prepare('SELECT id FROM users WHERE email=?');$check->execute([$email]);if($check->fetch())$errors[]='An account with that email already exists.';}
 if(!$errors){$stmt=$pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)');$stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),'customer']);session_regenerate_id(true);$_SESSION['user']=['id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email,'role'=>'customer'];set_flash('Welcome! Your account has been created.');redirect('index.php');}
}
$pageTitle='Create account';require __DIR__.'/includes/header.php';
?>
<section class="auth-wrap"><div class="auth-intro"><p class="eyebrow">Join ShopSmart</p><h1>Your next great find is waiting.</h1><p>Create an account to place demo orders and review your order history.</p></div><form method="post" class="panel form-panel auth-form"><h2>Create account</h2><?php if($errors): ?><div class="error-list"><?php foreach($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Full name<input name="name" required value="<?= e($_POST['name']??'') ?>"></label><label>Email address<input type="email" name="email" required value="<?= e($_POST['email']??'') ?>"></label><label>Password<input type="password" name="password" minlength="8" required autocomplete="new-password"></label><button class="button button-block">Create account</button><p class="muted">Already registered? <a href="login.php">Sign in</a>.</p></form></section>
<?php require __DIR__.'/includes/footer.php'; ?>
