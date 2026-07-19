<?php $page_title='Create Beyond ID'; require_once __DIR__.'/../includes/header.php'; require_once __DIR__.'/../config/admin-alerts.php'; require_once __DIR__.'/../config/roles.php';
if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf();
  $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $pass=$_POST['password']??'';
  if(!$name||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pass)<8){ flash('error','Use a valid name, email, and password with 8+ characters.'); }
  else { try { $verify=token(); $role=beyond_signup_role($email); $stmt=db()->prepare('INSERT INTO users (name,email,password_hash,role,email_verify_token,created_at,updated_at) VALUES (?,?,?,?,?,?,?)'); $stmt->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$role,$verify,now(),now()]);
    $uid=(int)db()->lastInsertId(); log_activity($uid,'registered','Created Beyond ID');
    send_beyond_id_admin_signup_alert(['id'=>$uid,'name'=>$name,'email'=>$email,'created_at'=>now()], 'Legacy Beyond ID signup');
    $link=url('auth/verify.php?token='.$verify); send_app_mail($email,'Verify your Beyond ID',"Welcome to Beyond OS. Verify your email:\n$link");
    flash('success','Beyond ID created. You can now sign in.'); redirect('auth/login.php');
  } catch(PDOException $e){ flash('error','That email is already registered.'); }}
}
?>
<section class="auth-page"><div class="card auth-card"><h1>Create Beyond ID</h1><p class="muted">One account for every Beyond product.</p><form class="form" method="post"><?= csrf_field() ?><input name="name" placeholder="Full name" required><input type="email" name="email" placeholder="Email" required><input type="password" name="password" placeholder="Password, 8+ characters" required minlength="8"><button class="btn full" type="submit">Create Account</button></form><p class="muted">Already have an account? <a href="<?= url('auth/login.php') ?>">Sign in</a></p></div></section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
