<?php require_once __DIR__ . '/../config/config.php'; $email = $_SESSION['pending_email'] ?? ''; ?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title>Verify Email</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><main class="card"><h1>Check your inbox</h1><p>We sent a verification link to <strong><?= e($email) ?></strong>.</p>
<?php if(!empty($_SESSION['error'])): ?><p class="error"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></p><?php endif; ?>
<?php if(!empty($_SESSION['success'])): ?><p class="success"><?= e($_SESSION['success']); unset($_SESSION['success']); ?></p><?php endif; ?>
<form method="post" action="resend-verification.php"><input name="email" value="<?= e($email) ?>" placeholder="Email address"><button>Resend verification</button></form>
<a href="../login.php">Back to sign in</a></main></body></html>
