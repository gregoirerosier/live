<?php
declare(strict_types=1);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/ecosystem.php';
beyond_nav_bootstrap('Beyond Catering');
define('APP_NAME', 'Beyond Catering');
define('APP_URL', 'https://beyondimagination.co.technology/beyond-catering');
define('DB_HOST', (string)beyond_config('database.host', ''));
define('DB_NAME', (string)beyond_config('database.name', ''));
define('DB_USER', (string)beyond_config('database.user', ''));
define('DB_PASS', (string)beyond_config('database.pass', ''));
define('STRIPE_PUBLIC_KEY', (string)beyond_config('stripe.public_key', ''));
define('STRIPE_SECRET_KEY', (string)beyond_config('stripe.secret_key', ''));
define('STRIPE_WEBHOOK_SECRET', (string)beyond_config('stripe.webhook_secret', ''));
function db(): PDO { static $pdo=null; if($pdo instanceof PDO)return $pdo; try{$pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);return $pdo;}catch(PDOException $e){error_log('Catering DB: '.$e->getMessage());throw new RuntimeException('Database unavailable.');}}
function redirect(string $path): never { header('Location: '.$path); exit; }
try { $pdo=db(); } catch (RuntimeException $e) { http_response_code(503); exit('The service is temporarily unavailable.'); }
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
