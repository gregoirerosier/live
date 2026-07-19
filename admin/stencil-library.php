<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
require_once __DIR__ . '/../beyond-tattoo/includes/stencil-content.php';
bos_require_admin();
$wallet = bos_page_start('Admin', 'Stencil Library Manager', 'Upload and publish finished, hand-designed stencil releases.');

$current = bt_stencil_content();
$notice = '';
$error = '';

function bt_admin_upload(string $field, string $destinationDir, array $allowed, string $prefix): ?string
{
    if (!isset($_FILES[$field]) || !is_array($_FILES[$field]) || (int)($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed for ' . $field . '.');
    }
    if ((int)$file['size'] > 25 * 1024 * 1024) {
        throw new RuntimeException('Each upload must be 25 MB or smaller.');
    }
    $original = (string)$file['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Unsupported file type for ' . $field . '.');
    }
    if (!is_dir($destinationDir) && !mkdir($destinationDir, 0775, true) && !is_dir($destinationDir)) {
        throw new RuntimeException('Could not create the upload directory.');
    }
    $name = $prefix . '-' . gmdate('Ymd-His') . '.' . $ext;
    $target = rtrim($destinationDir, '/') . '/' . $name;
    if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
        throw new RuntimeException('Could not store ' . $field . '.');
    }
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim((string)($_POST['title'] ?? ''));
        $collection = trim((string)($_POST['collection'] ?? ''));
        $displayDate = trim((string)($_POST['display_date'] ?? ''));
        $isoDate = trim((string)($_POST['iso_date'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $placement = trim((string)($_POST['placement'] ?? ''));
        if ($title === '' || $collection === '' || $displayDate === '') {
            throw new RuntimeException('Title, collection and display date are required.');
        }

        $publicRoot = dirname(__DIR__) . '/beyond-tattoo';
        $previewName = bt_admin_upload('preview', $publicRoot . '/uploads/stencil-day', ['png','jpg','jpeg','webp'], 'preview');
        $packageName = bt_admin_upload('package', $publicRoot . '/uploads/stencil-day', ['zip'], 'stencil-pack');
        $igName = bt_admin_upload('ig_post', $publicRoot . '/uploads/stencil-day', ['png','jpg','jpeg','webp'], 'instagram-post');

        $next = [
            'title' => $title,
            'collection' => $collection,
            'display_date' => $displayDate,
            'iso_date' => $isoDate,
            'description' => $description,
            'placement' => $placement,
            'preview_url' => $previewName ? 'uploads/stencil-day/' . $previewName : $current['preview_url'],
            'package_url' => $packageName ? 'uploads/stencil-day/' . $packageName : $current['package_url'],
            'ig_post_url' => $igName ? 'uploads/stencil-day/' . $igName : $current['ig_post_url'],
        ];
        bt_stencil_save($next);
        $current = bt_stencil_content();
        $notice = 'Stencil of the Day published. The storefront and download buttons now use these files.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<main class="bos-main">
<section class="bos-hero"><span class="bos-kicker">Beyond Tattoo Admin</span><h1>Stencil Library Manager</h1><p>Upload finished artwork, assign its collection and release details, then publish it to the storefront and daily download buttons. No procedural artwork generation.</p><div class="bos-actions"><a class="bos-btn" href="../beyond-tattoo/" target="_blank">Open live storefront</a><a class="bos-btn secondary" href="stencil-downloads.php">View analytics</a></div></section>
<?php if ($notice): ?><section class="bos-section"><div class="bos-card"><strong><?=htmlspecialchars($notice, ENT_QUOTES, 'UTF-8')?></strong></div></section><?php endif; ?>
<?php if ($error): ?><section class="bos-section"><div class="bos-card"><strong style="color:#ff9ca8"><?=htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?></strong></div></section><?php endif; ?>
<section class="bos-section"><div class="bos-grid" style="grid-template-columns:minmax(0,1.25fr) minmax(280px,.75fr)">
<form class="bos-card" method="post" enctype="multipart/form-data" style="display:grid;gap:14px">
<label><strong>Stencil title</strong><input class="bos-input" name="title" required value="<?=htmlspecialchars($current['title'], ENT_QUOTES, 'UTF-8')?>"></label>
<label><strong>Collection</strong><select class="bos-input" name="collection"><option <?=str_starts_with($current['collection'],'Beyond Ancient')?'selected':''?>>Beyond Ancient Collection</option><option <?=str_starts_with($current['collection'],'Dark Realism')?'selected':''?>>Dark Realism Collection</option><option <?=str_starts_with($current['collection'],'Japanese Legends')?'selected':''?>>Japanese Legends Collection</option><option <?=str_starts_with($current['collection'],'Divine Realism')?'selected':''?>>Divine Realism Collection</option></select></label>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px"><label><strong>Display date</strong><input class="bos-input" name="display_date" required value="<?=htmlspecialchars($current['display_date'], ENT_QUOTES, 'UTF-8')?>"></label><label><strong>ISO date</strong><input class="bos-input" type="date" name="iso_date" value="<?=htmlspecialchars($current['iso_date'], ENT_QUOTES, 'UTF-8')?>"></label></div>
<label><strong>Short description</strong><textarea class="bos-input" name="description" rows="3"><?=htmlspecialchars($current['description'], ENT_QUOTES, 'UTF-8')?></textarea></label>
<label><strong>Suggested placement</strong><input class="bos-input" name="placement" value="<?=htmlspecialchars($current['placement'], ENT_QUOTES, 'UTF-8')?>"></label>
<label><strong>Storefront preview</strong><small> PNG, JPG or WebP</small><input class="bos-input" type="file" name="preview" accept="image/png,image/jpeg,image/webp"></label>
<label><strong>Studio transfer pack</strong><small> ZIP containing stencil, transfer and placement files</small><input class="bos-input" type="file" name="package" accept="application/zip,.zip"></label>
<label><strong>Watermarked preview / promo asset</strong><small> 1080×1350 recommended</small><input class="bos-input" type="file" name="ig_post" accept="image/png,image/jpeg,image/webp"></label>
<button class="bos-btn" type="submit">Publish finished stencil</button>
</form>
<aside class="bos-card"><span class="bos-kicker">Current live drop</span><h2><?=htmlspecialchars($current['title'], ENT_QUOTES, 'UTF-8')?></h2><p><?=htmlspecialchars($current['collection'], ENT_QUOTES, 'UTF-8')?></p><img src="../beyond-tattoo/<?=htmlspecialchars($current['preview_url'], ENT_QUOTES, 'UTF-8')?>?v=<?=time()?>" alt="Current stencil" style="width:100%;max-height:430px;object-fit:contain;border-radius:18px;background:#fff"><p><strong>Placement:</strong><br><?=htmlspecialchars($current['placement'], ENT_QUOTES, 'UTF-8')?></p><div class="bos-actions"><a class="bos-btn" href="../beyond-tattoo/<?=htmlspecialchars($current['package_url'], ENT_QUOTES, 'UTF-8')?>" download>Download pack</a><a class="bos-btn secondary" href="../beyond-tattoo/<?=htmlspecialchars($current['ig_post_url'], ENT_QUOTES, 'UTF-8')?>" download>Download promo asset</a></div></aside>
</div></section></main>
<?php bos_page_end(); ?>
