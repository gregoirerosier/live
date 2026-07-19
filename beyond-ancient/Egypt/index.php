<?php
require_once __DIR__ . '/../../includes/ecosystem.php';
$beyondWallet = beyond_app_bootstrap('Beyond Ancient');
$featured = [
  ['title'=>'The Great Pyramid','eyebrow'=>'Explore','icon'=>'△','copy'=>'Step inside the last surviving wonder of the ancient world.'],
  ['title'=>'Eye of Horus','eyebrow'=>'Symbol','icon'=>'𓂀','copy'=>'Discover protection, healing, royal power, and the story behind the symbol.'],
  ['title'=>'Pharaohs','eyebrow'=>'Timeline','icon'=>'♛','copy'=>'Meet the rulers who shaped more than three thousand years of history.'],
  ['title'=>'Hieroglyph Lab','eyebrow'=>'Interactive','icon'=>'𓏞','copy'=>'Tap symbols, hear pronunciations, and build your own royal name.'],
];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#07101d">
  <title>Beyond Ancient — Egypt Beta</title>
  <meta name="description" content="An interactive journey through Ancient Egypt.">
  <link rel="icon" href="/beyond-ancient/Egypt/assets/img/beyond-ancient-logo.webp">
  <link rel="stylesheet" href="/beyond-ancient/Egypt/assets/css/app.css?v=1.0.0">
</head>
<body>
<div class="noise"></div>
<header class="topbar">
  <a class="brand" href="#top" aria-label="Beyond Ancient home">
    <img src="/beyond-ancient/Egypt/assets/img/beyond-ancient-logo.webp" alt="Beyond Ancient logo">
    <span><b>Beyond Ancient</b><small>Egypt • Version 2.0</small></span>
  </a>
  <button class="menu" id="menuBtn" aria-label="Open menu">☰</button>
  <nav id="nav">
    <a href="#explore">Explore</a><a href="#timeline">Timeline</a><a href="#quiz">Quiz</a><a href="#about">About</a>
  </nav>
</header>

<main id="top">
<section class="hero">
  <div class="sun"></div><div class="pyramid p1"></div><div class="pyramid p2"></div><div class="pyramid p3"></div>
  <div class="stars" aria-hidden="true"></div>
  <div class="hero-copy reveal">
    <span class="kicker">𓂀 Enter the ancient world</span>
    <h1>History you can<br><em>step inside.</em></h1>
    <p>Explore Ancient Egypt through cinematic stories, interactive artifacts, animated timelines, and bite-sized challenges.</p>
    <div class="actions"><a class="btn primary" href="#explore">Begin the journey</a><button class="btn ghost" id="watchIntro">▶ Watch intro</button></div>
    <div class="stats"><span><b>6</b> experiences</span><span><b>12</b> artifacts</span><span><b>1</b> beta journey</span></div>
  </div>
  <button class="scroll-cue" aria-label="Scroll to explore" onclick="document.querySelector('#explore').scrollIntoView({behavior:'smooth'})">⌄</button>
</section>

<section class="section" id="explore">
  <div class="section-head reveal"><span>Choose your path</span><h2>Explore Ancient Egypt</h2><p>Each experience is designed as a visual story—not a wall of text.</p></div>
  <div class="cards">
    <?php foreach($featured as $i=>$item): ?>
    <button class="card reveal" data-story="<?= $i ?>">
      <span class="card-icon"><?= htmlspecialchars($item['icon']) ?></span>
      <small><?= htmlspecialchars($item['eyebrow']) ?></small>
      <h3><?= htmlspecialchars($item['title']) ?></h3>
      <p><?= htmlspecialchars($item['copy']) ?></p>
      <b>Open experience →</b>
    </button>
    <?php endforeach; ?>
  </div>
</section>

<section class="split section" id="timeline">
  <div class="artifact reveal"><div class="eye">𓂀</div><div class="orbit"></div><span>Tap to reveal</span></div>
  <div class="reveal"><span class="kicker">Artifact spotlight</span><h2>The Eye of Horus</h2><p>A symbol connected with protection, restoration, kingship, and well-being. In the beta, artifact cards combine short explanations, visual motion, pronunciation, and quick checks.</p>
    <div class="fact" id="factBox"><b>Quick fact</b><span>The symbol is also known as the wedjat eye.</span></div>
    <button class="btn primary" id="nextFact">Reveal another fact</button>
  </div>
</section>

<section class="section timeline-wrap">
  <div class="section-head reveal"><span>Scroll through time</span><h2>Dynasties at a glance</h2></div>
  <div class="timeline" role="list">
    <article class="era reveal"><b>c. 3100 BCE</b><h3>Early Dynastic</h3><p>Upper and Lower Egypt unite under a centralized kingship.</p></article>
    <article class="era reveal"><b>c. 2686 BCE</b><h3>Old Kingdom</h3><p>The great pyramid-building age transforms the Giza plateau.</p></article>
    <article class="era reveal"><b>c. 2055 BCE</b><h3>Middle Kingdom</h3><p>Political renewal, literature, trade, and monumental building flourish.</p></article>
    <article class="era reveal"><b>c. 1550 BCE</b><h3>New Kingdom</h3><p>Egypt reaches imperial power under rulers including Hatshepsut and Ramesses II.</p></article>
  </div>
</section>

<section class="section quiz" id="quiz">
  <div class="section-head reveal"><span>One-minute challenge</span><h2>Test your knowledge</h2></div>
  <div class="quiz-box reveal">
    <p id="question">Which river was central to Ancient Egyptian life?</p>
    <div id="answers"></div><div id="feedback" aria-live="polite"></div>
    <button class="btn ghost hidden" id="nextQuestion">Next question</button>
  </div>
</section>

<section class="section beta" id="about">
  <div class="reveal"><span class="kicker">Built for Beyond Learn</span><h2>A living history classroom.</h2><p>Version 2.0 delivers responsive navigation, interactive cards, an animated artifact spotlight, timeline, quiz engine, and expandable PHP structure.</p></div>
  <div class="beta-list reveal"><span>✓ Mobile-first responsive UI</span><span>✓ No database required</span><span>✓ PHP shared-hosting ready</span><span>✓ Easy content expansion</span><span>✓ Motion-reduction support</span></div>
</section>
</main>

<footer><img src="/beyond-ancient/Egypt/assets/img/beyond-ancient-logo.webp" alt=""><p>Beyond Ancient • Part of Beyond Learn</p><small>Version 2.0 — educational experience</small></footer>

<div class="modal" id="modal" aria-hidden="true"><div class="modal-card"><button class="close" aria-label="Close">×</button><span id="modalIcon">𓂀</span><small id="modalEyebrow"></small><h2 id="modalTitle"></h2><p id="modalCopy"></p><div class="progress"><i></i></div><p class="coming">Interactive chapter preview • Full lesson coming in the next beta.</p></div></div>
<div class="modal" id="videoModal" aria-hidden="true"><div class="modal-card video-card"><button class="close" aria-label="Close">×</button><div class="cinema"><div class="cinema-sun"></div><div class="cinema-pyramid"></div><div class="cinema-eye">𓂀</div></div><h2>Welcome to Ancient Egypt</h2><p>A lightweight cinematic intro placeholder ready to be replaced by an MP4/WebM video.</p></div></div>
<script>window.BA_STORIES = <?= json_encode($featured, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="/beyond-ancient/Egypt/assets/js/app.js?v=1.0.0"></script>
</body></html>
