<?php
require __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
$title = 'System';
require __DIR__ . '/../includes/admin-header.php';
require __DIR__ . '/../includes/admin-sidebar.php';
?>
<section class="content">
<h1>System Health</h1><div class='grid'><div class='tile'><h3>Database</h3><span class='badge ok'>Healthy</span></div><div class='tile'><h3>Authentication</h3><span class='badge ok'>Healthy</span></div><div class='tile'><h3>Email</h3><span class='badge warn'>Configure SMTP</span></div><div class='tile'><h3>Storage</h3><span class='badge ok'>Ready</span></div></div></section><?php require __DIR__ . '/../includes/admin-footer.php'; ?>
