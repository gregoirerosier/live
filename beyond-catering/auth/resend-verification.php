<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/beyond-catering/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/mail.php';

$email = strtolower(trim($_POST['email'] ?? $_SESSION['pending_email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Enter a valid email.';
    redirect('verify-email-sent.php');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id, name, email_verified, verification_sent_at FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'No account found.';
    redirect('verify-email-sent.php');
}

if ((int)$user['email_verified'] === 1) {
    $_SESSION['success'] = 'Email is already verified. Please sign in.';
    redirect('../login.php');
}

$last = strtotime($user['verification_sent_at'] ?? '');
if ($last && $last > time() - 120) {
    $_SESSION['error'] = 'Please wait 2 minutes before resending.';
    redirect('verify-email-sent.php');
}

$token = bin2hex(random_bytes(32));
$pdo->prepare('UPDATE users SET verification_token = ?, verification_sent_at = NOW(), updated_at = NOW() WHERE id = ?')
    ->execute([$token, (int)$user['id']]);

$sent = send_verification_email($email, $token, 'catering', $user['name'] ?? '');

$_SESSION['pending_email'] = $email;
if ($sent) {
    $_SESSION['success'] = 'Verification email sent. Please check your inbox.';
} else {
    $_SESSION['error'] = 'Email could not be sent. Check /config/smtp.php password and SMTP settings.';
}

redirect('verify-email-sent.php');
