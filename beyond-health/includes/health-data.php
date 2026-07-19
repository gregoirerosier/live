<?php
declare(strict_types=1);

/** Beyond Health V1 data access. All functions are scoped to a user_id and use the shared beyond-health.sqlite. */

function hd_today(): string { return date('Y-m-d'); }

function hd_log_water(PDO $pdo, int $userId, int $glasses, string $date): void {
    $glasses = max(0, min(30, $glasses));
    $pdo->prepare('INSERT INTO health_water_logs(user_id,glasses,logged_date) VALUES(?,?,?)')->execute([$userId, $glasses, $date]);
}
function hd_water_today(PDO $pdo, int $userId, string $date): int {
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(glasses),0) c FROM health_water_logs WHERE user_id=? AND logged_date=?');
    $stmt->execute([$userId, $date]);
    return (int)$stmt->fetch()['c'];
}

function hd_log_sleep(PDO $pdo, int $userId, float $hours, int $quality, string $date): void {
    $hours = max(0, min(24, $hours));
    $quality = max(1, min(5, $quality));
    $pdo->prepare('DELETE FROM health_sleep_logs WHERE user_id=? AND logged_date=?')->execute([$userId, $date]);
    $pdo->prepare('INSERT INTO health_sleep_logs(user_id,hours,quality,logged_date) VALUES(?,?,?,?)')->execute([$userId, $hours, $quality, $date]);
}
function hd_sleep_today(PDO $pdo, int $userId, string $date): ?array {
    $stmt = $pdo->prepare('SELECT hours,quality FROM health_sleep_logs WHERE user_id=? AND logged_date=? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId, $date]);
    return $stmt->fetch() ?: null;
}

function hd_log_weight(PDO $pdo, int $userId, float $kg, string $date): void {
    $kg = max(1, min(400, $kg));
    $pdo->prepare('DELETE FROM health_weight_logs WHERE user_id=? AND logged_date=?')->execute([$userId, $date]);
    $pdo->prepare('INSERT INTO health_weight_logs(user_id,weight_kg,logged_date) VALUES(?,?,?)')->execute([$userId, $kg, $date]);
}
function hd_weight_history(PDO $pdo, int $userId, int $limit = 10): array {
    $stmt = $pdo->prepare('SELECT weight_kg,logged_date FROM health_weight_logs WHERE user_id=? ORDER BY logged_date DESC LIMIT ?');
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function hd_log_mood(PDO $pdo, int $userId, int $mood, string $note, string $date): void {
    $mood = max(1, min(5, $mood));
    $note = mb_substr(trim($note), 0, 280);
    $pdo->prepare('DELETE FROM health_mood_logs WHERE user_id=? AND logged_date=?')->execute([$userId, $date]);
    $pdo->prepare('INSERT INTO health_mood_logs(user_id,mood,note,logged_date) VALUES(?,?,?,?)')->execute([$userId, $mood, $note ?: null, $date]);
}
function hd_mood_today(PDO $pdo, int $userId, string $date): ?array {
    $stmt = $pdo->prepare('SELECT mood,note FROM health_mood_logs WHERE user_id=? AND logged_date=? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId, $date]);
    return $stmt->fetch() ?: null;
}

function hd_add_medication(PDO $pdo, int $userId, string $name, string $dose, string $schedule): void {
    $name = mb_substr(trim($name), 0, 120);
    if ($name === '') return;
    $pdo->prepare('INSERT INTO health_medications(user_id,name,dose,schedule) VALUES(?,?,?,?)')
        ->execute([$userId, $name, mb_substr(trim($dose), 0, 60) ?: null, mb_substr(trim($schedule), 0, 60) ?: null]);
}
function hd_medications(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare('SELECT id,name,dose,schedule FROM health_medications WHERE user_id=? AND active=1 ORDER BY name');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}
function hd_mark_medication_taken(PDO $pdo, int $userId, int $medicationId, string $date): void {
    $owned = $pdo->prepare('SELECT id FROM health_medications WHERE id=? AND user_id=?');
    $owned->execute([$medicationId, $userId]);
    if (!$owned->fetch()) return;
    $exists = $pdo->prepare('SELECT id FROM health_medication_logs WHERE user_id=? AND medication_id=? AND taken_date=?');
    $exists->execute([$userId, $medicationId, $date]);
    if ($exists->fetch()) return;
    $pdo->prepare('INSERT INTO health_medication_logs(user_id,medication_id,taken_date) VALUES(?,?,?)')->execute([$userId, $medicationId, $date]);
}
function hd_medications_taken_today(PDO $pdo, int $userId, string $date): array {
    $stmt = $pdo->prepare('SELECT medication_id FROM health_medication_logs WHERE user_id=? AND taken_date=?');
    $stmt->execute([$userId, $date]);
    return array_map('intval', array_column($stmt->fetchAll(), 'medication_id'));
}

function hd_log_workout(PDO $pdo, int $userId, string $activity, int $minutes, string $intensity, string $date): void {
    $activity = mb_substr(trim($activity), 0, 80) ?: 'Activity';
    $minutes = max(1, min(600, $minutes));
    $intensity = in_array($intensity, ['light', 'moderate', 'intense'], true) ? $intensity : 'moderate';
    $pdo->prepare('INSERT INTO health_workout_logs(user_id,activity,minutes,intensity,logged_date) VALUES(?,?,?,?,?)')
        ->execute([$userId, $activity, $minutes, $intensity, $date]);
}
function hd_workouts_today(PDO $pdo, int $userId, string $date): array {
    $stmt = $pdo->prepare('SELECT activity,minutes,intensity FROM health_workout_logs WHERE user_id=? AND logged_date=? ORDER BY id DESC');
    $stmt->execute([$userId, $date]);
    return $stmt->fetchAll();
}

/**
 * Daily health score out of 100, built entirely from what the user actually logged today.
 * Each tracked pillar contributes points only when there's real data for it; nothing is invented.
 */
function hd_daily_score(PDO $pdo, int $userId, string $date): array {
    $breakdown = [];
    $score = 0;
    $maxPossible = 0;

    $water = hd_water_today($pdo, $userId, $date);
    $maxPossible += 25;
    if ($water > 0) { $score += (int)round(min(1, $water / 8) * 25); $breakdown[] = ['label' => "$water of 8 glasses", 'icon' => '💧']; }

    $sleep = hd_sleep_today($pdo, $userId, $date);
    $maxPossible += 25;
    if ($sleep) {
        $hoursScore = min(1, $sleep['hours'] / 8) * 15;
        $qualityScore = ($sleep['quality'] / 5) * 10;
        $score += (int)round($hoursScore + $qualityScore);
        $breakdown[] = ['label' => round($sleep['hours'], 1) . 'h sleep', 'icon' => '😴'];
    }

    $workouts = hd_workouts_today($pdo, $userId, $date);
    $maxPossible += 25;
    if ($workouts) {
        $minutes = array_sum(array_column($workouts, 'minutes'));
        $score += (int)round(min(1, $minutes / 30) * 25);
        $breakdown[] = ['label' => "$minutes min activity", 'icon' => '🏃'];
    }

    $mood = hd_mood_today($pdo, $userId, $date);
    $maxPossible += 25;
    if ($mood) { $score += (int)round(($mood['mood'] / 5) * 25); $breakdown[] = ['label' => 'Mood logged', 'icon' => '🙂']; }

    $hasAnyData = $maxPossible > 0 && !empty($breakdown);
    return [
        'score' => $hasAnyData ? $score : null,
        'has_data' => $hasAnyData,
        'breakdown' => $breakdown,
    ];
}

function hd_score_label(?int $score): string {
    if ($score === null) return 'No entries yet today';
    if ($score >= 80) return 'Balanced day';
    if ($score >= 55) return 'Good progress';
    if ($score >= 30) return 'Getting started';
    return 'Just beginning';
}
