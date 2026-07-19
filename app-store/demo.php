<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
beyond_nav_bootstrap('App Demo', ['balance'=>0,'currency'=>'BITS','status'=>'guest']);
$demos = [
  'health'=>['Beyond Health','A calm daily wellness check-in.','♥','Choose a focus','Mindful minute','Daily movement','Health journal','beyond-health/','beyond-health'],
  'tattoo'=>['Beyond Tattoo','Preview the creative flow before saving a design.','✦','Browse daily stencil','Describe an idea','Preview placement','Save a collection','beyond-tattoo/','beyond-tattoo'],
  'baby-names'=>['Beyond Baby Names','Try a guided name discovery session.','◉','Choose an origin','Set name style','Compare meanings','Build a shortlist','beyond-baby-names/','beyond-baby-names'],
  'math'=>['Beyond Math','Try a short adaptive practice round.','∑','Choose a level','Solve one problem','See an explanation','Track mastery','beyond-math/','beyond-math'],
  'ancient'=>['Beyond Ancient','Step into a curated history trail.','𓂀','Choose a civilization','Open an artifact','Follow a timeline','Watch a story','beyond-ancient/','beyond-ancient'],
  'space'=>['Beyond Space','Preview a guided trip through the universe.','★','Choose a destination','Explore a planet','Start a daily mission','Test your knowledge','beyond-space/','beyond-space'],
  'preschool'=>['Beyond Preschool','Try a playful early-learning activity.','ABC','Pick a subject','Play one activity','Hear the prompt','Celebrate progress','beyond-preschool/','beyond-preschool'],
  'wallet'=>['Beyond Wallet','Preview how bit$, purchases and earnings stay separate.','¤','Review bit$ balance','See spend value','Preview activity','View payout status','beyond-finance/','beyond-finance'],
  'investing'=>['Beyond Investing','Preview live BTC context in CAD and USD.','₿','Check BTC / CAD','Check BTC / USD','Compare bit$ value','Refresh the market','beyond-investing/','beyond-investing'],
  'sell'=>['Beyond Sell','Walk through a sample creator listing.','$','Choose item type','Set bit$ price','Preview listing','Review delivery','beyond-sell/','beyond-sell'],
  'careers'=>['Beyond Careers','Try a simple opportunity and readiness flow.','↗','Choose a path','Review a role','Check readiness','Save a goal','beyond-careers/','beyond-careers'],
  'audio'=>['Beyond Audio','Preview a personalized listening session.','♫','Choose a mood','Open a station','Preview a queue','Save a favorite','beyond-audio/','beyond-audio'],
  'canvas'=>['Beyond Canvas','Try a guided creative project flow.','◫','Choose a canvas','Add an idea','Preview the work','Save a draft','beyond-canvas/','beyond-canvas'],
  'skate'=>['Beyond Skate','Preview a skill-building skate session.','◢','Choose a trick','Watch the breakdown','Log an attempt','Track progress','beyond-skate/','beyond-health/beyond-skate'],
];
$key = strtolower((string)($_GET['app'] ?? 'health'));
if (!isset($demos[$key])) $key = 'health';
[$title,$copy,$icon,$one,$two,$three,$four,$route,$loginApp] = $demos[$key];
$login = beyond_url('beyond-id/auth/login.php?app=' . rawurlencode($loginApp) . '&return=' . rawurlencode(beyond_url($route)));
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#050817"><title><?=e($title)?> Demo | Beta Build 2.1.1</title><link rel="stylesheet" href="<?=e(beyond_url('assets/css/bos-21.css'))?>"></head><body class="bos-page">
<main class="bos-main demo-main">
  <section class="bos-hero demo-hero"><span class="bos-kicker">Public demo · Beta Build 2.1.1</span><div class="demo-mark" aria-hidden="true"><?=e($icon)?></div><h1><?=e($title)?></h1><p><?=e($copy)?> No Beyond ID is required for this preview.</p><div class="bos-actions"><a class="bos-btn" href="<?=e($login)?>">Sign in to continue</a><a class="bos-btn secondary" href="<?=e(beyond_url('app-store/'))?>">Back to App Store</a></div></section>
  <section class="bos-section"><h2>What you can try</h2><div class="demo-steps"><button type="button" class="active"><?=e($one)?></button><button type="button"><?=e($two)?></button><button type="button"><?=e($three)?></button><button type="button"><?=e($four)?></button></div><div class="demo-stage"><span class="demo-stage-icon"><?=e($icon)?></span><div><span class="bos-kicker">Demo preview</span><h2 data-demo-title><?=e($one)?></h2><p data-demo-copy>Explore this step in preview mode. Sign in only when you are ready to save progress, purchases or personal data.</p></div></div></section>
</main>
<style>.demo-main{width:min(1220px,calc(100% - 28px))}.demo-hero{position:relative;overflow:hidden}.demo-mark{position:absolute;right:5%;top:50%;transform:translateY(-50%);font-size:clamp(90px,18vw,230px);opacity:.12}.demo-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin:18px 0}.demo-steps button{min-height:58px;padding:12px;border:1px solid var(--bos-line);border-radius:14px;background:#ffffff08;color:#fff;font-weight:850;cursor:pointer}.demo-steps button.active{background:#725cff;border-color:#9c8aff}.demo-stage{min-height:300px;padding:34px;display:flex;align-items:center;gap:28px;border:1px solid var(--bos-line);border-radius:24px;background:radial-gradient(circle at 85% 0,#725cff33,transparent 35%),#ffffff08}.demo-stage-icon{width:112px;height:112px;display:grid;place-items:center;border-radius:30px;background:linear-gradient(135deg,#725cff,#ef47a1);font-size:52px;font-weight:900}.demo-stage h2{font-size:clamp(34px,6vw,64px);margin:10px 0}.demo-stage p{max-width:620px;color:var(--bos-muted)}@media(max-width:700px){.demo-steps{grid-template-columns:repeat(2,1fr)}.demo-stage{align-items:flex-start;flex-direction:column}.demo-mark{display:none}}</style>
<script>document.querySelectorAll('.demo-steps button').forEach(function(button){button.addEventListener('click',function(){document.querySelectorAll('.demo-steps button').forEach(function(item){item.classList.toggle('active',item===button)});document.querySelector('[data-demo-title]').textContent=button.textContent})});</script>
<?php bos_page_end(); ?>
