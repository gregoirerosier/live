<?php
require_once __DIR__ . '/../config/config.php';
$token = trim($_GET['token'] ?? '');
if ($token === '') { $_SESSION['error'] = 'Missing verification token.'; redirect('verify-email-sent.php'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT id, email, verification_sent_at FROM users WHERE verification_token = ? LIMIT 1");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) { $_SESSION['error'] = 'Invalid or already used verification link.'; redirect('verify-email-sent.php'); }

$sentAt = strtotime($user['verification_sent_at'] ?? '');
if (!$sentAt || $sentAt < time() - 86400) {
    $_SESSION['pending_email'] = $user['email'];
    $_SESSION['error'] = 'Verification link expired. Please resend verification email.';
    redirect('verify-email-sent.php');
}

$update = $pdo->prepare("UPDATE users SET email_verified = 1, email_verified_at = NOW(), verification_token = NULL, status = 'active', updated_at = NOW() WHERE id = ?");
$update->execute([$user['id']]);

$_SESSION['success'] = 'Email verified. You can now sign in and continue onboarding.';
redirect('../login.php');
