<?php require_once __DIR__ . '/config/config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Create Vendor Account — Beyond Catering</title>
  <link rel="stylesheet" href="/beyond-catering/assets/css/style.css">
</head>
<body>
<main class="page-shell">
  <section class="form-card">
    <p class="eyebrow">Premium Trial</p>
    <h1>Create vendor account</h1>
    <p>Start your 30-day trial, verify your email, then continue onboarding.</p>

    <?php if(!empty($_SESSION['error'])): ?>
      <div class="alert error"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="post" action="auth/register_submit.php">
      <div class="field"><label>Owner Name</label><input name="owner_name" autocomplete="name" required></div>
      <div class="field"><label>Email Address</label><input name="email" type="email" autocomplete="email" required></div>
      <div class="field"><label>Phone</label><input name="phone" type="tel" autocomplete="tel"></div>
      <div class="field"><label>Password</label><input name="password" type="password" minlength="8" autocomplete="new-password" required></div>
      <div class="field"><label>Confirm Password</label><input name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
      <div class="field"><label>Plan</label><select name="plan"><option value="premium_trial">Premium Trial - Free 30 Days</option><option value="starter">Starter</option></select></div>
      <label class="check"><input type="checkbox" name="terms" required><span>I agree to the Terms and Privacy Policy.</span></label>
      <button class="btn primary full" type="submit">Continue Onboarding →</button>
    </form>
    <a class="small-link" href="login.php">Already registered? Sign in</a>
  </section>
</main>
</body>
</html>
