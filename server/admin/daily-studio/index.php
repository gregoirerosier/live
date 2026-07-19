<?php
require __DIR__.'/bootstrap.php';
require dirname(__DIR__).'/_header.php';
?>
<link rel="stylesheet" href="/server/admin/daily-studio/studio.css">
<div class="studio-head"><div><h1>Beyond Studio</h1><p class="muted">Create and manage Daily Breath and Beyond French content.</p></div></div>
<section class="studio-launch" aria-labelledby="studio-launch-title">
  <div class="studio-launch-head">
    <div>
      <p class="studio-eyebrow">Create</p>
      <h2 id="studio-launch-title">Studio tools</h2>
    </div>
    <p>Choose a generator to start creating.</p>
  </div>
  <div class="studio-tools" aria-label="Content generators">
    <a class="studio-tool studio-tool-breath" href="/server/admin/daily-studio/dailybreath-content.php">
      <span class="studio-tool-icon" aria-hidden="true">✦</span>
      <span class="studio-tool-copy"><strong>DailyBreath App Content</strong><small>Edit today’s verse, generate devotionals and challenges, and build Academy lessons</small></span>
      <span class="studio-tool-arrow" aria-hidden="true">→</span>
    </a>
    <a class="studio-tool studio-tool-breath" href="/server/admin/daily-studio/breath-generator.php">
      <span class="studio-tool-icon" aria-hidden="true">🙏</span>
      <span class="studio-tool-copy"><strong>Daily Breath Generator</strong><small>Create Bible verse posts, narration and exports</small></span>
      <span class="studio-tool-arrow" aria-hidden="true">→</span>
    </a>
    <a class="studio-tool studio-tool-french" href="/server/admin/daily-studio/french-generator.php">
      <span class="studio-tool-icon" aria-hidden="true">🇫🇷</span>
      <span class="studio-tool-copy"><strong>Beyond French Generator</strong><small>Create Français du Jour visuals and MP3 audio</small></span>
      <span class="studio-tool-arrow" aria-hidden="true">→</span>
    </a>
    <a class="studio-tool studio-tool-tattoo" href="/server/admin/daily-studio/tattoo-generator.php">
      <span class="studio-tool-icon" aria-hidden="true">✒️</span>
      <span class="studio-tool-copy"><strong>Stencil Library Manager</strong><small>Upload finished stencil packs, assign collections and publish the daily release</small></span>
      <span class="studio-tool-arrow" aria-hidden="true">→</span>
    </a>
    <a class="studio-tool studio-tool-tattoo" href="/admin/stencil-pack-generator.php">
      <span class="studio-tool-icon" aria-hidden="true">🎬</span>
      <span class="studio-tool-copy"><strong>Stencil Pack Generator</strong><small>Upload a stencil, fill out the collection information and export the finished package</small></span>
      <span class="studio-tool-arrow" aria-hidden="true">→</span>
    </a>
    <a class="studio-tool studio-tool-voices" href="/server/admin/daily-studio/voice-settings.php">
      <span class="studio-tool-icon" aria-hidden="true">🎙️</span>
      <span class="studio-tool-copy"><strong>Premium Voices</strong><small>Manage provider, API key and voice IDs</small></span>
      <span class="studio-tool-arrow" aria-hidden="true">→</span>
    </a>
  </div>
</section>
<?php require dirname(__DIR__).'/_footer.php'; ?>
