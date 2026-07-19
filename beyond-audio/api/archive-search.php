<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');
$q = trim((string)($_GET['q'] ?? ''));
if ($q === '' || mb_strlen($q) > 120) { http_response_code(400); echo json_encode(['items'=>[]]); exit; }
$query = '(' . $q . ') AND (mediatype:(audio OR movies))';
$params = http_build_query(['q'=>$query,'fl[]'=>['identifier','title','creator','date','year','mediatype'],'rows'=>12,'page'=>1,'output'=>'json']);
$url = 'https://archive.org/advancedsearch.php?' . $params;
$context = stream_context_create(['http'=>['timeout'=>8,'user_agent'=>'BeyondAudio/1.0','ignore_errors'=>true]]);
$raw = @file_get_contents($url, false, $context);
if ($raw === false) { http_response_code(502); echo json_encode(['items'=>[]]); exit; }
$data = json_decode($raw, true);
$docs = $data['response']['docs'] ?? [];
$items = [];
foreach ($docs as $doc) {
  $id = preg_replace('/[^A-Za-z0-9._-]/','',(string)($doc['identifier'] ?? ''));
  if ($id === '') continue;
  $items[] = [
    'identifier'=>$id,
    'title'=>(string)($doc['title'] ?? $id),
    'creator'=>is_array($doc['creator'] ?? null) ? implode(', ', $doc['creator']) : (string)($doc['creator'] ?? ''),
    'year'=>(string)($doc['year'] ?? substr((string)($doc['date'] ?? ''),0,4)),
    'mediatype'=>(string)($doc['mediatype'] ?? ''),
    'details_url'=>'https://archive.org/details/' . rawurlencode($id),
    'download_url'=>'https://archive.org/download/' . rawurlencode($id) . '/',
  ];
}
echo json_encode(['items'=>$items], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
