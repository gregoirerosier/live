<?php
require_once __DIR__ . '/../includes/ecosystem.php';
$beyondWallet = beyond_app_bootstrap('Beyond Baby Names');
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

$coupleDeck = [];
$namesFile = __DIR__ . '/data/names.json';

if (is_file($namesFile)) {
    $allNames = json_decode(file_get_contents($namesFile), true);

    if (is_array($allNames)) {
        usort($allNames, function ($a, $b) {
            return ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0);
        });

        // Keep the initial page payload reasonable while providing plenty of cards.
        $coupleDeck = array_slice($allNames, 0, 500);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Beyond Baby Names v2.0</title>
<meta name="description" content="Explore meaningful baby names, save favorites, and discover twin and couple matches.">
<link rel="stylesheet" href="/beyond-baby-names/assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="shell nav">
    <a class="brand" href="#"><span>Beyond</span> Baby Names</a>
    <nav class="navlinks">
      <a href="#explore">Explore</a>
      <a href="#modes">Modes</a>
      <a href="#explore">Favorites <strong id="favCount">0</strong></a>
    </nav>
  </div>
</header>

<main>
<section class="hero shell">
  <span class="hero-badge">Version 2.0</span>
  <h1>Find the name that feels meant to be.</h1>
  <p>Search more than 1,915 names by meaning, origin, style, gender, and first letter. Save favorites and discover pairings together.</p>
  <div class="stats">
    <div class="stat"><strong>1,915+</strong><span>baby names</span></div>
    <div class="stat"><strong>23</strong><span>origins</span></div>
    <div class="stat"><strong>2</strong><span>special modes</span></div>
  </div>
</section>

<section id="explore" class="shell">
  <div class="panel filters">
    <input id="search" type="search" placeholder="Search name, meaning, or origin">
    <select id="gender"><option value="all">All genders</option><option value="girl">Girl</option><option value="boy">Boy</option><option value="unisex">Unisex</option></select>
    <select id="origin"><option value="all">All origins</option><option value="african">African</option>
<option value="arabic">Arabic</option>
<option value="british">British</option>
<option value="czech">Czech</option>
<option value="danish">Danish</option>
<option value="dutch">Dutch</option>
<option value="english">English</option>
<option value="finnish">Finnish</option>
<option value="french">French</option>
<option value="german">German</option>
<option value="greek">Greek</option>
<option value="hawaiian">Hawaiian</option>
<option value="hebrew">Hebrew</option>
<option value="italian">Italian</option>
<option value="latin">Latin</option>
<option value="norwegian">Norwegian</option>
<option value="polish">Polish</option>
<option value="portuguese">Portuguese</option>
<option value="romanian">Romanian</option>
<option value="slavic">Slavic</option>
<option value="spanish">Spanish</option>
<option value="swedish">Swedish</option>
<option value="turkish">Turkish</option></select>
    <select id="category"><option value="all">All styles</option><option value="popular">Popular</option><option value="rare">Rare</option><option value="modern">Modern</option><option value="classic">Classic</option><option value="biblical">Biblical</option><option value="nature">Nature</option><option value="royal">Royal</option><option value="vintage">Vintage</option></select>
    <select id="letter"><option value="">Any letter</option><option value="A">A</option>
<option value="B">B</option>
<option value="C">C</option>
<option value="D">D</option>
<option value="E">E</option>
<option value="F">F</option>
<option value="G">G</option>
<option value="H">H</option>
<option value="I">I</option>
<option value="J">J</option>
<option value="K">K</option>
<option value="L">L</option>
<option value="M">M</option>
<option value="N">N</option>
<option value="O">O</option>
<option value="P">P</option>
<option value="Q">Q</option>
<option value="R">R</option>
<option value="S">S</option>
<option value="T">T</option>
<option value="U">U</option>
<option value="V">V</option>
<option value="W">W</option>
<option value="X">X</option>
<option value="Y">Y</option>
<option value="Z">Z</option></select>
  </div>

  <div class="section-head">
    <div><h2>Explore names</h2><p id="resultCount">Loading…</p></div>
  </div>
  <div id="namesGrid" class="grid"></div>
  <div class="pagination">
    <button id="prev">Previous</button>
    <span id="pageLabel">Page 1</span>
    <button id="next">Next</button>
  </div>
</section>

<section id="modes" class="shell">
  <div class="section-head"><div><h2>Special modes</h2><p>Built for couples and growing families.</p></div></div>
  <div class="mode-grid">
    <article class="panel mode-card couple-mode-card">
      <div class="couple-heading">
        <div>
          <h3>💞 Couple Mode 2.0</h3>
          <p>Rate names separately. A match appears when both partners choose Love.</p>
        </div>
        <span class="match-badge">Matches <strong id="matchCount">0</strong></span>
      </div>

      <div class="partner-switch" role="group" aria-label="Select partner">
        <button type="button" class="partner-tab active" data-partner="partner1">Partner 1</button>
        <button type="button" class="partner-tab" data-partner="partner2">Partner 2</button>
      </div>

      <div id="coupleNameCard" class="couple-name-card">
        <span class="couple-label">Now rating as <strong id="activePartnerLabel">Partner 1</strong></span>
        <h4 id="coupleName">Loading…</h4>
        <p id="coupleMeaning">Preparing your name deck.</p>
        <div id="coupleMeta" class="meta"></div>
      </div>

      <div class="rating-actions">
        <button type="button" class="rate-button pass" data-rating="pass">✕ Pass</button>
        <button type="button" class="rate-button maybe" data-rating="maybe">☆ Maybe</button>
        <button type="button" class="rate-button love" data-rating="love">♥ Love</button>
      </div>

      <div id="matchCelebration" class="match-celebration" hidden></div>

      <div class="match-list-wrap">
        <div class="match-list-heading">
          <strong>Your mutual matches</strong>
          <button id="resetCoupleMode" type="button" class="text-button">Reset</button>
        </div>
        <div id="coupleMatches" class="match-list">
          <p class="empty-state">No mutual matches yet. Keep rating together.</p>
        </div>
      </div>
    </article>
    <article class="panel mode-card">
      <h3>👶 Twin Mode</h3>
      <p>Discover twin-name combinations for girls, boys, or mixed pairs.</p>
      <select id="twinType">
        <option value="mixed">Mixed pair</option>
        <option value="girl">Girl / Girl</option>
        <option value="boy">Boy / Boy</option>
      </select>
      <button id="twinBtn">Generate twin names</button>
      <div id="twinResult" class="result-box">Twin suggestions will appear here.</div>
    </article>
  </div>
</section>
</main>

<footer><div class="shell">Beyond Baby Names v2.0 • Built for the Beyond ecosystem</div></footer>
<script>
window.BBN_COUPLE_NAMES = <?= json_encode(
    $coupleDeck,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
) ?>;
</script>
<script src="/beyond-baby-names/assets/js/app.js?v=1.2.2"></script>
</body>
</html>
