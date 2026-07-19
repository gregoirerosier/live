<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="beyond-health-early-access.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','email','product','source','status','ip_address','created_at']);
foreach ($pdo->query('SELECT id,email,product,source,status,ip_address,created_at FROM early_access_subscribers ORDER BY created_at DESC') as $row) {
    fputcsv($out, $row);
}
