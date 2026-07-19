<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
define('MARKET_ROOT', dirname(__DIR__));
define('MARKET_DATA', MARKET_ROOT . '/data');
define('MARKET_UPLOADS', MARKET_ROOT . '/uploads');
function me(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function mread(string $name): array { $f=MARKET_DATA.'/'.$name; if(!is_file($f)) return []; $d=json_decode((string)file_get_contents($f),true); return is_array($d)?$d:[]; }
function mwrite(string $name,array $data): bool { return file_put_contents(MARKET_DATA.'/'.$name,json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),LOCK_EX)!==false; }
function csrf_token(): string { if(empty($_SESSION['market_csrf'])) $_SESSION['market_csrf']=bin2hex(random_bytes(24)); return $_SESSION['market_csrf']; }
function csrf_ok(): bool { return isset($_POST['csrf'],$_SESSION['market_csrf']) && hash_equals($_SESSION['market_csrf'],(string)$_POST['csrf']); }
function market_user(): string { return (string)($_SESSION['user_name'] ?? 'Beyond Creator'); }
function market_logged_in(): bool { return !empty($_SESSION['user_id']) || !empty($_SESSION['user_email']); }
function slugify(string $value): string { $value=strtolower(trim($value)); $value=preg_replace('/[^a-z0-9]+/','-',$value)??''; return trim($value,'-') ?: 'item-'.time(); }
function market_flash(string $key, ?string $value=null): ?string { if($value!==null){$_SESSION['_market_flash'][$key]=$value;return null;} $v=$_SESSION['_market_flash'][$key]??null; unset($_SESSION['_market_flash'][$key]); return is_string($v)?$v:null; }
?>