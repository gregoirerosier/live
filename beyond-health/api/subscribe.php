<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$product = trim($_POST['product'] ?? 'Beyond Health');
$source = trim($_POST['source'] ?? 'landing_page');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO early_access_subscribers (email, product, source, ip_address, user_agent)
        VALUES (:email, :product, :source, :ip, :ua)
        ON CONFLICT(email) DO UPDATE SET product=excluded.product,source=excluded.source,status='active',updated_at=CURRENT_TIMESTAMP");
    $stmt->execute([
        ':email' => $email,
        ':product' => $product,
        ':source' => $source,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);

    $subject = 'Welcome to Beyond Health Early Access';
    $body = "Thanks for joining the Beyond Health early access list.\n\nYou’ll receive product updates, UI previews, and beta launch news.\n\nBuilt in Canada by Beyond Imagination Technology.";
    $headers = "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
    @mail($email, $subject, $body, $headers);

    @mail(ADMIN_EMAIL, 'New Beyond Health early access signup', "New signup: $email\nProduct: $product\nSource: $source", $headers);

    echo json_encode(['ok' => true, 'message' => 'Thanks! You are on the Beyond Health Early Access list.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Signup failed. Please try again.']);
}
