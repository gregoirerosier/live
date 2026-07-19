<?php
require __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
$title = 'Database';
require __DIR__ . '/../includes/admin-header.php';
require __DIR__ . '/../includes/admin-sidebar.php';
?>
<section class="content">

<h1>Database Explorer</h1>
<?php $tables=$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM); ?>
<div class="card"><table><tr><th>Table</th><th>Actions</th></tr><?php foreach($tables as $t): ?><tr><td><?= e($t[0]) ?></td><td><a href="sql.php">Open in SQL Console</a></td></tr><?php endforeach; ?></table></div>
</section><?php require __DIR__ . '/../includes/admin-footer.php'; ?>
