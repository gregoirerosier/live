<?php $page_title='Sign In'; require_once __DIR__.'/../includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf();
  $email=strtolower(trim($_POST['email']??'')); $pass=$_POST['password']??'';
  $stmt=db()->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $stmt->execute([$email]); $user=$stmt->fetch();
  if(!$user || !password_verify($pass,$user['password_hash'])) flash('error','Invalid email or password.');
  elseif(REQUIRE_EMAIL_VERIFICATION && empty($user['email_verified_at'])) flash('error','Please verify your email first.');
  else { login_user($user); log_activity((int)$user['id'],'login','Signed in'); redirect('dashboard/'); }
}
?>
<section class="auth-page"><div class="card auth-card"><a class="btn" href="/" style="margin-bottom:18px">← Back to Home</a><h1>Sign In</h1><p class="muted">Access Beyond OS.</p><form class="form" method="post"><?= csrf_field() ?><input type="email" name="email" placeholder="Email" required><input type="password" name="password" placeholder="Password" required><button class="btn full" type="submit">Sign In</button></form><p class="muted"><a href="<?= url('auth/forgot-password.php') ?>">Forgot password?</a> · <a href="<?= url('auth/register.php') ?>">Create Beyond ID</a></p></div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
