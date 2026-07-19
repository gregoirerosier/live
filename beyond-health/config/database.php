<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
try{
    $path=beyond_private_root().'/beyond-health.sqlite';
    $pdo=new PDO('sqlite:'.$path,null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $pdo->exec('PRAGMA busy_timeout=5000; PRAGMA journal_mode=WAL;');
    $pdo->exec("CREATE TABLE IF NOT EXISTS early_access_subscribers(id INTEGER PRIMARY KEY AUTOINCREMENT,email TEXT NOT NULL UNIQUE COLLATE NOCASE,product TEXT NOT NULL DEFAULT 'Beyond Health',source TEXT NOT NULL DEFAULT 'landing_page',status TEXT NOT NULL DEFAULT 'active',ip_address TEXT NULL,user_agent TEXT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");

    // --- Beyond Health V1 tracking tables ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS health_water_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,glasses INTEGER NOT NULL,logged_date TEXT NOT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_water_user_date ON health_water_logs(user_id,logged_date)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS health_sleep_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,hours REAL NOT NULL,quality INTEGER NOT NULL,logged_date TEXT NOT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_sleep_user_date ON health_sleep_logs(user_id,logged_date)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS health_weight_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,weight_kg REAL NOT NULL,logged_date TEXT NOT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_weight_user_date ON health_weight_logs(user_id,logged_date)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS health_mood_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,mood INTEGER NOT NULL,note TEXT NULL,logged_date TEXT NOT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_mood_user_date ON health_mood_logs(user_id,logged_date)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS health_medications(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,name TEXT NOT NULL,dose TEXT NULL,schedule TEXT NULL,active INTEGER NOT NULL DEFAULT 1,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_meds_user ON health_medications(user_id,active)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS health_medication_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,medication_id INTEGER NOT NULL,taken_date TEXT NOT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_med_logs_user_date ON health_medication_logs(user_id,taken_date)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS health_workout_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,activity TEXT NOT NULL,minutes INTEGER NOT NULL,intensity TEXT NOT NULL,logged_date TEXT NOT NULL,created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_workout_user_date ON health_workout_logs(user_id,logged_date)");
}catch(PDOException $e){error_log('Health DB: '.$e->getMessage());http_response_code(503);exit('The service is temporarily unavailable.');}
