<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/../data/names.json';
if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Names data not found']);
    exit;
}

$names = json_decode(file_get_contents($file), true);
if (!is_array($names)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Invalid names data']);
    exit;
}

$search = mb_strtolower(trim($_GET['search'] ?? ''));
$gender = mb_strtolower(trim($_GET['gender'] ?? 'all'));
$origin = mb_strtolower(trim($_GET['origin'] ?? 'all'));
$category = mb_strtolower(trim($_GET['category'] ?? 'all'));
$letter = mb_strtoupper(trim($_GET['letter'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(1, min(100, (int)($_GET['limit'] ?? 24)));

$filtered = array_values(array_filter($names, function($item) use ($search,$gender,$origin,$category,$letter) {
    $name = mb_strtolower($item['name'] ?? '');
    $meaning = mb_strtolower($item['meaning'] ?? '');
    $itemOrigin = mb_strtolower($item['origin'] ?? '');
    $categories = array_map('mb_strtolower', $item['categories'] ?? []);
    if ($search !== '' && mb_strpos($name,$search) === false && mb_strpos($meaning,$search) === false && mb_strpos($itemOrigin,$search) === false) return false;
    if ($gender !== 'all' && ($item['gender'] ?? '') !== $gender) return false;
    if ($origin !== 'all' && $itemOrigin !== $origin) return false;
    if ($category !== 'all' && !in_array($category,$categories,true)) return false;
    if ($letter !== '' && mb_strtoupper($item['first_letter'] ?? '') !== $letter) return false;
    return true;
}));

$total = count($filtered);
$offset = ($page - 1) * $limit;
$items = array_slice($filtered, $offset, $limit);

echo json_encode([
    'success' => true,
    'total' => $total,
    'page' => $page,
    'pages' => max(1,(int)ceil($total/$limit)),
    'items' => $items
], JSON_UNESCAPED_UNICODE);
