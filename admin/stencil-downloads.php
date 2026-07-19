<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
require_once __DIR__ . '/../includes/stencil-tracking.php';
bos_require_admin();
$wallet = bos_page_start('Admin', 'Stencil Downloads', 'Track public and signed-in Stencil of the Day downloads.');

$rows = [];
$stats = ['total'=>0,'today'=>0,'members'=>0,'anonymous'=>0,'unique_sessions'=>0];
$error = '';
try {
    $pdo = beyond_db();
    stencil_download_table($pdo);
    $stats['total'] = (int)$pdo->query('SELECT COUNT(*) FROM stencil_downloads')->fetchColumn();
    $todayStmt = $pdo->prepare('SELECT COUNT(*) FROM stencil_downloads WHERE stencil_date=?');
    $todayStmt->execute([date('Y-m-d')]);
    $stats['today'] = (int)$todayStmt->fetchColumn();
    $stats['members'] = (int)$pdo->query('SELECT COUNT(*) FROM stencil_downloads WHERE user_id IS NOT NULL')->fetchColumn();
    $stats['anonymous'] = (int)$pdo->query('SELECT COUNT(*) FROM stencil_downloads WHERE user_id IS NULL')->fetchColumn();
    $stats['unique_sessions'] = (int)$pdo->query('SELECT COUNT(DISTINCT session_hash) FROM stencil_downloads')->fetchColumn();
    $sql = "SELECT d.id,d.user_id,d.stencil_date,d.file_name,d.source,d.downloaded_at,d.user_agent,u.name,u.email
            FROM stencil_downloads d LEFT JOIN users u ON u.id=d.user_id
            ORDER BY d.downloaded_at DESC LIMIT 250";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $error = 'Download analytics are unavailable until the database migration can run.';
    error_log('Stencil admin analytics failed: ' . $e->getMessage());
}
?>
<main class="bos-main">
<section class="bos-hero"><span class="bos-kicker">Beyond Tattoo Analytics</span><h1>Stencil of the Day downloads</h1><p>Every download is counted, including public visitors who do not use Beyond ID. IP addresses are stored only as one-way hashes.</p><div class="bos-actions"><a class="bos-btn" href="../beyond-tattoo/stencil-of-day.php">Open public stencil</a><a class="bos-btn secondary" href="index.php">Back to command center</a></div></section>
<?php if ($error): ?><section class="bos-section"><div class="bos-stat"><?=htmlspecialchars($error)?></div></section><?php endif; ?>
<section class="bos-section"><div class="bos-stat-grid">
<div class="bos-stat"><b><?=number_format($stats['total'])?></b><span>Total downloads</span></div>
<div class="bos-stat"><b><?=number_format($stats['today'])?></b><span>Today</span></div>
<div class="bos-stat"><b><?=number_format($stats['members'])?></b><span>Signed-in</span></div>
<div class="bos-stat"><b><?=number_format($stats['anonymous'])?></b><span>Public</span></div>
<div class="bos-stat"><b><?=number_format($stats['unique_sessions'])?></b><span>Unique sessions</span></div>
</div></section>
<section class="bos-section"><h2>Recent downloads</h2><div style="overflow:auto;border:1px solid rgba(255,255,255,.12);border-radius:20px"><table style="width:100%;border-collapse:collapse;min-width:850px"><thead><tr><th style="text-align:left;padding:14px">Time</th><th style="text-align:left;padding:14px">Visitor</th><th style="text-align:left;padding:14px">Email</th><th style="text-align:left;padding:14px">Access</th><th style="text-align:left;padding:14px">File</th><th style="text-align:left;padding:14px">Device</th></tr></thead><tbody><?php if(!$rows): ?><tr><td colspan="6" style="padding:22px">No downloads recorded yet.</td></tr><?php endif; ?><?php foreach($rows as $row): ?><tr style="border-top:1px solid rgba(255,255,255,.09)"><td style="padding:14px;white-space:nowrap"><?=htmlspecialchars((string)$row['downloaded_at'])?></td><td style="padding:14px"><?=htmlspecialchars((string)($row['name'] ?: 'Public visitor'))?></td><td style="padding:14px"><?=htmlspecialchars((string)($row['email'] ?: '—'))?></td><td style="padding:14px"><?=$row['user_id'] ? 'Beyond ID' : 'Public'?></td><td style="padding:14px"><?=htmlspecialchars((string)$row['file_name'])?></td><td style="padding:14px;max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars((string)($row['user_agent'] ?: 'Unknown'))?></td></tr><?php endforeach; ?></tbody></table></div></section>
</main>
<?php bos_page_end(); ?>
