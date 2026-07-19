<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
try{
    $path=beyond_private_root().'/beyond-health.sqlite';
    $pdo=new PDO('sqlite:'.$path,null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $pdo->exec('PRAGMA busy_timeout=5000; PRAGMA journal_mode=WAL;');
    $pdo->exec("CREATE TABLE IF NOT EXISTS early_access_subscribers(id INTEGER PRIMARY KEY AUTOINCREMENT,email TEXT NOT NULL UNIQUE COLLATE NOCASE,product TEXT NOT NULL DEFAULT 'Beyond Health',source TEXT NOT NULL DEFAULT 'landing_page',status TEXT NOT NULL DEFAULT 'active',ip_address TEXT NULL,user_agent TEXT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
}catch(PDOException $e){error_log('Health DB: '.$e->getMessage());http_response_code(503);exit('The service is temporarily unavailable.');}
