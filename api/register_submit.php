<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/admin-alerts.php';
require_once __DIR__ . '/../config/roles.php';

$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    http_response_code(422);
    exit('Invalid signup details.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$role = beyond_signup_role($email);

$stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())');
$stmt->execute([$name, $email, $hash, $role]);
$uid = (int)db()->lastInsertId();
send_beyond_id_admin_signup_alert(['id'=>$uid,'name'=>$name,'email'=>$email,'created_at'=>date('Y-m-d H:i:s')], 'Legacy API signup');

header('Location: ../public/login.php?created=1');
exit;
