<?php
require_once __DIR__ . '/../config/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../login.php');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
$stored = $user['password_hash'] ?? $user['password'] ?? '';

if (!$user || !password_verify($password, $stored)) { $_SESSION['error'] = 'Invalid login.'; redirect('../login.php'); }
if ((int)$user['email_verified'] !== 1) { $_SESSION['pending_email'] = $email; $_SESSION['error'] = 'Please verify your email before signing in.'; redirect('verify-email-sent.php'); }

$_SESSION['user_id'] = $user['id']; $_SESSION['role'] = $user['role'];
db()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);
redirect('../onboarding/index.php');
