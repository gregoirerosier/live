<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
require dirname(__DIR__).'/_header.php';
$tabs=['content'=>['DailyBreath Content','dailybreath-content.php'],'breath'=>['DailyBreath Generator','breath-generator.php'],'french'=>['Beyond French Generator','french-generator.php'],'french-options'=>['Beyond French Options','french-options.php'],'voices'=>['Premium Voices','voice-settings.php']];
?>
<link rel="stylesheet" href="/server/admin/daily-studio/studio.css"><link rel="stylesheet" href="/server/admin/daily-studio/studio-sunset.css">
<div class="studio-workspace"><div class="studio-head"><div><p class="studio-eyebrow">Sunset workspace</p><h1>Beyond Studio</h1><p class="muted">Create, configure, preview, and publish DailyBreath and Beyond French content in one place.</p></div><a class="btn" id="open-studio-page" href="dailybreath-content.php" target="_blank" rel="noopener">Open page ↗</a></div>
<nav class="studio-tabs" role="tablist" aria-label="Studio pages"><?php foreach($tabs as $key=>[$label,$url]): ?><button type="button" role="tab" data-studio-tab="<?=DailyStudio::esc($key)?>" data-src="<?=DailyStudio::esc($url)?>" aria-selected="false"><?=DailyStudio::esc($label)?></button><?php endforeach;?></nav>
<div class="studio-frame-shell"><iframe id="studio-frame" title="Studio page" loading="eager"></iframe></div></div>
<script>(function(){const buttons=[...document.querySelectorAll('[data-studio-tab]')],frame=document.getElementById('studio-frame'),open=document.getElementById('open-studio-page');function select(key){const button=buttons.find(item=>item.dataset.studioTab===key)||buttons[0];buttons.forEach(item=>{const active=item===button;item.setAttribute('aria-selected',active?'true':'false');item.classList.toggle('active',active);});frame.src=button.dataset.src;open.href=button.dataset.src;try{localStorage.setItem('beyond-studio-tab',button.dataset.studioTab)}catch(e){}}let initial='content';try{initial=localStorage.getItem('beyond-studio-tab')||'content'}catch(e){}buttons.forEach(button=>button.addEventListener('click',()=>select(button.dataset.studioTab)));select(initial);})();</script>
<?php require dirname(__DIR__).'/_footer.php'; ?>
