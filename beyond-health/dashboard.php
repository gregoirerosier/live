<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/ecosystem.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/health-data.php';

$wallet = beyond_app_bootstrap('Beyond Health');
$userId = (int)$_SESSION['user_id'];
$today = hd_today();

if (empty($_SESSION['health_csrf'])) $_SESSION['health_csrf'] = bin2hex(random_bytes(32));
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals((string)$_SESSION['health_csrf'], (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403);
        exit('Invalid security token.');
    }
    $action = (string)($_POST['action'] ?? '');
    switch ($action) {
        case 'water':
            hd_log_water($pdo, $userId, (int)($_POST['glasses'] ?? 0), $today);
            $notice = 'Water logged.';
            break;
        case 'sleep':
            hd_log_sleep($pdo, $userId, (float)($_POST['hours'] ?? 0), (int)($_POST['quality'] ?? 3), $today);
            $notice = 'Sleep logged.';
            break;
        case 'weight':
            $kg = (float)($_POST['weight_kg'] ?? 0);
            if ($kg > 0) { hd_log_weight($pdo, $userId, $kg, $today); $notice = 'Weight logged.'; }
            break;
        case 'mood':
            hd_log_mood($pdo, $userId, (int)($_POST['mood'] ?? 3), (string)($_POST['note'] ?? ''), $today);
            $notice = 'Mood logged.';
            break;
        case 'medication_add':
            hd_add_medication($pdo, $userId, (string)($_POST['med_name'] ?? ''), (string)($_POST['med_dose'] ?? ''), (string)($_POST['med_schedule'] ?? ''));
            $notice = 'Medication added.';
            break;
        case 'medication_taken':
            hd_mark_medication_taken($pdo, $userId, (int)($_POST['medication_id'] ?? 0), $today);
            $notice = 'Marked as taken.';
            break;
        case 'workout':
            hd_log_workout($pdo, $userId, (string)($_POST['activity'] ?? ''), (int)($_POST['minutes'] ?? 0), (string)($_POST['intensity'] ?? 'moderate'), $today);
            $notice = 'Workout logged.';
            break;
    }
}

$water = hd_water_today($pdo, $userId, $today);
$sleep = hd_sleep_today($pdo, $userId, $today);
$weightHistory = hd_weight_history($pdo, $userId, 6);
$mood = hd_mood_today($pdo, $userId, $today);
$medications = hd_medications($pdo, $userId);
$medsTaken = hd_medications_taken_today($pdo, $userId, $today);
$workouts = hd_workouts_today($pdo, $userId, $today);
$scoreData = hd_daily_score($pdo, $userId, $today);
$csrf = htmlspecialchars((string)$_SESSION['health_csrf']);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Beyond Health — Dashboard</title><link rel="stylesheet" href="/beyond-health/health-v1.css"><link rel="stylesheet" href="/beyond-health/dashboard.css"></head><body>
<header class="health-nav"><a class="health-brand" href="/beyond-health/"><span>♡</span><strong>Beyond Health</strong></a><nav><a href="/beyond-health/dashboard.php">Dashboard</a><a href="/academy/">Beyond Academy</a><a href="/dailybreath/">DailyBreath</a></nav></header>
<main class="dash-wrap">
<?php if ($notice): ?><div class="dash-notice"><?= e($notice) ?></div><?php endif; ?>

<section class="dash-score">
  <div>
    <span class="health-kicker">TODAY'S HEALTH SCORE</span>
    <strong class="score-number"><?= $scoreData['score'] !== null ? (int)$scoreData['score'] : '—' ?></strong>
    <span class="score-label"><?= e(hd_score_label($scoreData['score'])) ?></span>
  </div>
  <ul class="score-pillars">
    <?php if ($scoreData['breakdown']): foreach ($scoreData['breakdown'] as $item): ?>
      <li><?= $item['icon'] ?> <?= e($item['label']) ?></li>
    <?php endforeach; else: ?>
      <li class="empty">Log water, sleep, a workout, or your mood to build today's score.</li>
    <?php endif; ?>
  </ul>
</section>

<div class="dash-grid">

  <article class="dash-card">
    <h3>💧 Water</h3>
    <p class="dash-metric"><?= $water ?> <span>of 8 glasses</span></p>
    <form method="post" class="dash-inline-form">
      <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="water">
      <input type="hidden" name="glasses" value="<?= $water + 1 ?>">
      <button class="health-button secondary" type="submit">+ Add a glass</button>
    </form>
  </article>

  <article class="dash-card">
    <h3>😴 Sleep</h3>
    <?php if ($sleep): ?>
      <p class="dash-metric"><?= e((string)round($sleep['hours'], 1)) ?>h <span>quality <?= (int)$sleep['quality'] ?>/5</span></p>
    <?php else: ?>
      <p class="dash-empty">Not logged today.</p>
    <?php endif; ?>
    <form method="post" class="dash-inline-form dash-form-row">
      <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="sleep">
      <input type="number" name="hours" step="0.5" min="0" max="24" placeholder="Hours" required>
      <select name="quality"><option value="1">Poor</option><option value="2">Fair</option><option value="3" selected>OK</option><option value="4">Good</option><option value="5">Great</option></select>
      <button class="health-button secondary" type="submit">Save</button>
    </form>
  </article>

  <article class="dash-card">
    <h3>⚖️ Weight</h3>
    <?php if ($weightHistory): ?>
      <p class="dash-metric"><?= e((string)$weightHistory[0]['weight_kg']) ?> <span>kg · <?= e($weightHistory[0]['logged_date']) ?></span></p>
      <ul class="dash-history">
        <?php foreach (array_slice($weightHistory, 1, 4) as $w): ?><li><?= e($w['logged_date']) ?> — <?= e((string)$w['weight_kg']) ?> kg</li><?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="dash-empty">No entries yet.</p>
    <?php endif; ?>
    <form method="post" class="dash-inline-form dash-form-row">
      <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="weight">
      <input type="number" name="weight_kg" step="0.1" min="1" max="400" placeholder="kg" required>
      <button class="health-button secondary" type="submit">Save</button>
    </form>
  </article>

  <article class="dash-card">
    <h3>🙂 Mood</h3>
    <?php if ($mood): ?>
      <p class="dash-metric"><?= str_repeat('●', (int)$mood['mood']) . str_repeat('○', 5 - (int)$mood['mood']) ?></p>
      <?php if (!empty($mood['note'])): ?><p class="dash-note">“<?= e($mood['note']) ?>”</p><?php endif; ?>
    <?php else: ?>
      <p class="dash-empty">Not logged today.</p>
    <?php endif; ?>
    <form method="post" class="dash-inline-form dash-form-row">
      <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="mood">
      <select name="mood"><option value="1">😞 Low</option><option value="2">🙁 Rough</option><option value="3" selected>😐 Steady</option><option value="4">🙂 Good</option><option value="5">😄 Great</option></select>
      <input type="text" name="note" maxlength="280" placeholder="Optional note">
      <button class="health-button secondary" type="submit">Save</button>
    </form>
  </article>

  <article class="dash-card dash-card-wide">
    <h3>🏃 Workouts today</h3>
    <?php if ($workouts): ?>
      <ul class="dash-history">
        <?php foreach ($workouts as $w): ?><li><?= e($w['activity']) ?> — <?= (int)$w['minutes'] ?> min · <?= e($w['intensity']) ?></li><?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="dash-empty">Nothing logged yet today.</p>
    <?php endif; ?>
    <form method="post" class="dash-inline-form dash-form-row">
      <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="workout">
      <input type="text" name="activity" maxlength="80" placeholder="Activity (e.g. Run, Gym, Yoga)" required>
      <input type="number" name="minutes" min="1" max="600" placeholder="Minutes" required>
      <select name="intensity"><option value="light">Light</option><option value="moderate" selected>Moderate</option><option value="intense">Intense</option></select>
      <button class="health-button secondary" type="submit">Log workout</button>
    </form>
  </article>

  <article class="dash-card dash-card-wide">
    <h3>💊 Medications</h3>
    <?php if ($medications): ?>
      <ul class="dash-med-list">
        <?php foreach ($medications as $m): $taken = in_array((int)$m['id'], $medsTaken, true); ?>
          <li class="<?= $taken ? 'taken' : '' ?>">
            <span><strong><?= e($m['name']) ?></strong><?= $m['dose'] ? ' — ' . e($m['dose']) : '' ?><?= $m['schedule'] ? ' · ' . e($m['schedule']) : '' ?></span>
            <?php if ($taken): ?><span class="dash-taken-badge">✓ Taken today</span><?php else: ?>
              <form method="post"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="medication_taken"><input type="hidden" name="medication_id" value="<?= (int)$m['id'] ?>"><button class="health-button secondary small" type="submit">Mark taken</button></form>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="dash-empty">No medications added yet.</p>
    <?php endif; ?>
    <form method="post" class="dash-inline-form dash-form-row">
      <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="medication_add">
      <input type="text" name="med_name" maxlength="120" placeholder="Medication name" required>
      <input type="text" name="med_dose" maxlength="60" placeholder="Dose (optional)">
      <input type="text" name="med_schedule" maxlength="60" placeholder="Schedule (optional)">
      <button class="health-button secondary" type="submit">Add medication</button>
    </form>
  </article>

  <article class="dash-card dash-card-wide dash-coming-soon">
    <h3>🍎 Meal Planner &amp; ⌚ Integrations</h3>
    <p class="dash-empty">Not built yet — next up on the roadmap.</p>
  </article>

</div>
</main>
<footer class="health-footer"><strong>Beyond Health</strong><span>A Beyond Imagination Technology product · Built in Canada 🇨🇦</span></footer>
</body></html>
