<?php
declare(strict_types=1);
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
$_SESSION['beyond_return_to']='/beyond-health/admin/';
header('Location: /beyond-id/auth/login.php?required=1');
exit;
