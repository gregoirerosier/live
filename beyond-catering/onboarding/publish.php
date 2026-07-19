<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['vendor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$vendor_id = (int) $_SESSION['vendor_id'];

try {
    $stmt = $pdo->prepare("
        UPDATE onboarding_progress
        SET is_published = 1,
            completed = 1,
            current_step = 10
        WHERE vendor_id = ?
    ");
    $stmt->execute([$vendor_id]);

    $stmt = $pdo->prepare("
        UPDATE vendors
        SET onboarding_completed = 1,
            status = 'published'
        WHERE id = ?
    ");
    $stmt->execute([$vendor_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Website published'
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}