<?php
require_once __DIR__ . '/../includes/ecosystem.php';
$beyondWallet = beyond_app_bootstrap('Beyond Catering');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Beyond Catering — Build a Restaurant People Can Order From</title>
  <meta name="theme-color" content="#081120">
  <link rel="stylesheet" href="/beyond-catering/assets/css/style.css">
</head>
<body>
  <main class="page-shell">
    <section class="hero-card">
      <div class="brand-row">
        <div class="brand-mark">🍽️</div>
        <div>
          <p class="eyebrow">Beyond Imagination Technology</p>
          <h1>Beyond Catering</h1>
        </div>
      </div>

      <h2>Build a Restaurant<br>People Can Order From.</h2>
      <p class="lead">Create your website. Manage your menu. Accept online orders. Grow your business from one mobile-first dashboard.</p>

      <div class="feature-pills" aria-label="Platform features">
        <span>⚡ Online Ordering</span>
        <span>💳 Stripe Payments</span>
        <span>🌐 Custom Domain</span>
      </div>

      <div class="cta-row">
        <a class="btn primary" href="register.php">Start Premium Trial</a>
        <a class="btn link" href="login.php">Sign in</a>
      </div>

      <div class="trust-row">
        <span>⭐⭐⭐⭐⭐</span>
        <p>Built for restaurants, caterers, food trucks, and bakeries.</p>
      </div>
    </section>
  </main>
</body>
</html>
