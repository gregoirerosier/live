<?php require_once __DIR__ . '/config/config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Sign In — Beyond Catering</title>
  <link rel="stylesheet" href="/beyond-catering/assets/css/style.css">
</head>
<body>
<main class="page-shell">
  <section class="form-card">
    <a class="small-link" href="/">← Back to Home</a>
    <p class="eyebrow">Vendor Portal</p>
    <h1>Sign in</h1>
    <p>Access your Beyond Catering dashboard.</p>

    <?php if(!empty($_SESSION['error'])): ?>
      <div class="alert error"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if(!empty($_SESSION['success'])): ?>
      <div class="alert success"><?= e($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="post" action="auth/login_check.php">
      <div class="field"><label>Email</label><input name="email" type="email" autocomplete="email" required></div>
      <div class="field"><label>Password</label><input name="password" type="password" autocomplete="current-password" required></div>
      <button class="btn primary full" type="submit">Sign in</button>
    </form>
    <a class="small-link" href="register.php">Create a vendor account</a>
  </section>
</main>
</body>
</html>
