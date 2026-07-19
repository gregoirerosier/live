<?php
require __DIR__ . '/includes/config.php';
require_login();
$message = null;
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['photo']) && $_FILES['photo']['error']===UPLOAD_ERR_OK) {
  $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
  if (in_array($ext,['jpg','jpeg','png','webp'],true) && $_FILES['photo']['size'] <= 10*1024*1024) {
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_DIR.'/'.$name)) $message='Healing photo uploaded.';
  }
}
$pageTitle='Healing Journal — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Healing Journal</span></a></div></header>
<main class="container dashboard"><div class="panel"><h2>Upload today's photo</h2><p class="meta">JPG, PNG or WebP up to 10 MB.</p><?php if($message): ?><div class="notice"><?= e($message) ?></div><?php endif; ?><form class="form-grid" method="post" enctype="multipart/form-data"><input class="input" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required><textarea class="input" name="notes" placeholder="How does it feel today?"></textarea><button class="btn btn-primary" type="submit">Save healing entry</button></form></div></main></div>
<?php require __DIR__ . '/includes/footer.php'; ?>