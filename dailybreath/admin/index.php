<?php
require __DIR__ . '/../config/database.php';
session_start();
if (isset($_POST['password'])) {
    if (hash_equals(ADMIN_PASSWORD, $_POST['password'])) $_SESSION['dailybreath_admin'] = true;
    else $error = 'Wrong password.';
}
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }
if (empty($_SESSION['dailybreath_admin'])):
?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>DailyBreath Admin</title></head><body><main class="page"><form class="newsletter admin-login" method="post"><a href="/" style="display:inline-block;margin-bottom:16px;color:inherit;font-weight:800">← Back to Home</a><h1>DailyBreath Admin</h1><?php if(isset($error)) echo '<div class="alert error">'.$error.'</div>'; ?><input type="password" name="password" placeholder="Admin password" required><button>Login</button></form></main></body></html>
<?php exit; endif;
$rows = db()->query("SELECT id,name,email,status,created_at FROM dailybreath_subscribers ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Subscribers</title></head><body><main class="admin"><h1>DailyBreath Subscribers</h1><p><a href="export.php">Export CSV</a> · <a href="?logout=1">Logout</a></p><table><tr><th>Name</th><th>Email</th><th>Status</th><th>Date</th></tr><?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['name'] ?? '')?></td><td><?=htmlspecialchars($r['email'])?></td><td><?=htmlspecialchars($r['status'])?></td><td><?=htmlspecialchars($r['created_at'])?></td></tr><?php endforeach; ?></table></main></body></html>
