<?php
require_once __DIR__ . '/../config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/mail.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/roles.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../index.php');

$name = trim($_POST['owner_name'] ?? $_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$plan = $_POST['plan'] ?? 'premium_trial';
$terms = isset($_POST['terms']);

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || $password !== $confirm || !$terms) {
    $_SESSION['error'] = 'Please complete all fields, use a valid email, and make sure passwords match.';
    redirect('../register.php');
}

$pdo = db();
$exists = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$exists->execute([$email]);
if ($exists->fetch()) {
    $_SESSION['error'] = 'An account already exists with that email.';
    redirect('../register.php');
}

$token = bin2hex(random_bytes(32));
$hash = password_hash($password, PASSWORD_DEFAULT);
$role = beyond_signup_role($email, 'vendor');

$stmt = $pdo->prepare("INSERT INTO users
    (name, email, phone, password, password_hash, role, status, email_verified, verification_token, verification_sent_at, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, 'pending_verification', 0, ?, NOW(), NOW(), NOW())");
$stmt->execute([$name, $email, $phone, $hash, $hash, $role, $token]);

$sent = send_verification_email($email, $token, 'catering', $name);
if (!$sent) { $_SESSION['error'] = 'Account created, but email could not be sent. Check /config/smtp.php.'; }

$_SESSION['pending_email'] = $email;
redirect('verify-email-sent.php');
