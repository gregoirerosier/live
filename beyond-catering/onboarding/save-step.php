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
$current_step = (int) ($_POST['current_step'] ?? 1);

$business_data = [
    'business_name' => $_POST['business_name'] ?? '',
    'business_type' => $_POST['business_type'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'address' => $_POST['address'] ?? ''
];

$branding_data = [
    'logo_url' => $_POST['logo_url'] ?? '',
    'cover_url' => $_POST['cover_url'] ?? '',
    'gallery_urls' => $_POST['gallery_urls'] ?? ''
];

$theme_data = [
    'theme' => $_POST['theme'] ?? 'modern',
    'brand_color' => $_POST['brand_color'] ?? '#ff6a00'
];

$menu_data = [
    'category' => $_POST['menu_category'] ?? '',
    'item_name' => $_POST['item_name'] ?? '',
    'item_description' => $_POST['item_description'] ?? '',
    'item_price' => $_POST['item_price'] ?? ''
];

$payment_data = [
    'stripe_status' => $_POST['stripe_status'] ?? 'skipped'
];

$domain_data = [
    'subdomain' => $_POST['subdomain'] ?? '',
    'custom_domain' => $_POST['custom_domain'] ?? ''
];

$hours_data = [
    'business_hours' => $_POST['business_hours'] ?? ''
];

$delivery_data = [
    'pickup' => isset($_POST['pickup']) ? 1 : 0,
    'delivery' => isset($_POST['delivery']) ? 1 : 0,
    'dine_in' => isset($_POST['dine_in']) ? 1 : 0,
    'delivery_radius' => $_POST['delivery_radius'] ?? '',
    'delivery_fee' => $_POST['delivery_fee'] ?? ''
];

try {
    $stmt = $pdo->prepare("
        INSERT INTO onboarding_progress 
        (
            vendor_id,
            current_step,
            completed_steps,
            business_data,
            branding_data,
            theme_data,
            menu_data,
            payment_data,
            domain_data,
            hours_data,
            delivery_data
        )
        VALUES (?, ?, JSON_ARRAY(?), ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            current_step = VALUES(current_step),
            completed_steps = JSON_ARRAY(?),
            business_data = VALUES(business_data),
            branding_data = VALUES(branding_data),
            theme_data = VALUES(theme_data),
            menu_data = VALUES(menu_data),
            payment_data = VALUES(payment_data),
            domain_data = VALUES(domain_data),
            hours_data = VALUES(hours_data),
            delivery_data = VALUES(delivery_data)
    ");

    $stmt->execute([
        $vendor_id,
        $current_step,
        $current_step,
        json_encode($business_data),
        json_encode($branding_data),
        json_encode($theme_data),
        json_encode($menu_data),
        json_encode($payment_data),
        json_encode($domain_data),
        json_encode($hours_data),
        json_encode($delivery_data),
        $current_step
    ]);

    echo json_encode(['success' => true]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}