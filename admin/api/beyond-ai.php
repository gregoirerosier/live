<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/app-layout.php';
require_once __DIR__ . '/../../includes/beyond-ai.php';
bos_require_admin();
header('Content-Type: application/json; charset=utf-8');

function ai_out(int $status, array $payload): never { http_response_code($status); echo json_encode($payload, JSON_UNESCAPED_SLASHES); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') ai_out(405, ['ok'=>false,'error'=>'POST required.']);
if (!function_exists('curl_init')) ai_out(500, ['ok'=>false,'error'=>'PHP cURL is not enabled.']);

$prompt = trim((string)($_POST['prompt'] ?? ''));
$mode = ($_POST['mode'] ?? 'quick') === 'advanced' ? 'advanced' : 'quick';
$historyRaw = (string)($_POST['history'] ?? '[]');
$history = json_decode($historyRaw, true);
if (!is_array($history)) $history = [];
if ($prompt === '' || mb_strlen($prompt) > 12000) ai_out(422, ['ok'=>false,'error'=>'Enter a request up to 12,000 characters.']);

$today = beyond_ai_today_usage();
$limit = max(1, (int)beyond_ai_config('daily_request_limit', 200));
if ((int)$today['requests'] >= $limit) ai_out(429, ['ok'=>false,'error'=>'Daily AI request limit reached.']);

$apiKey = trim((string)beyond_ai_config('api_key', ''));
if ($apiKey === '') ai_out(503, ['ok'=>false,'error'=>'OpenAI API key is not configured. Add OPENAI_API_KEY or ai.openai.api_key to protected configuration.']);
$model = $mode === 'advanced'
    ? (string)beyond_ai_config('advanced_model', 'gpt-5.4')
    : (string)beyond_ai_config('quick_model', 'gpt-5-mini');

$messages = [[
    'role' => 'developer',
    'content' => [[
        'type' => 'input_text',
        'text' => 'You are Beyond AI Assistant inside the private Beyond OS admin console. Help the administrator ask questions, generate content, troubleshoot code, plan releases, and prepare structured data. Be practical and concise. When asked for Stencil Pack Generator data, return a JSON object with keys title, collection, edition, detail, access, badge, subtitle, tagline, and drop_date.'
    ]]
]];
foreach (array_slice($history, -10) as $entry) {
    $role = ($entry['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
    $text = trim((string)($entry['text'] ?? ''));
    if ($text !== '') $messages[] = ['role'=>$role, 'content'=>[['type'=>$role === 'assistant' ? 'output_text' : 'input_text', 'text'=>mb_substr($text,0,8000)]]];
}
$content = [['type'=>'input_text','text'=>$prompt]];
if (isset($_FILES['attachment']) && is_uploaded_file($_FILES['attachment']['tmp_name'])) {
    $file = $_FILES['attachment'];
    if ((int)$file['size'] > 12 * 1024 * 1024) ai_out(413, ['ok'=>false,'error'=>'Attachment must be 12 MB or smaller.']);
    $mime = mime_content_type($file['tmp_name']) ?: ($file['type'] ?? 'application/octet-stream');
    $dataUrl = 'data:' . $mime . ';base64,' . base64_encode((string)file_get_contents($file['tmp_name']));
    if (str_starts_with($mime, 'image/')) {
        $content[] = ['type'=>'input_image','image_url'=>$dataUrl,'detail'=>$mode === 'advanced' ? 'high' : 'auto'];
    } else {
        $content[] = ['type'=>'input_file','filename'=>basename((string)$file['name']),'file_data'=>$dataUrl];
    }
}
$messages[] = ['role'=>'user','content'=>$content];

$payload = [
    'model' => $model,
    'input' => $messages,
    'max_output_tokens' => $mode === 'advanced' ? 3000 : 1400,
    'store' => false,
];
if ($mode === 'advanced') $payload['reasoning'] = ['effort'=>'medium'];

$ch = curl_init('https://api.openai.com/v1/responses');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$apiKey,'Content-Type: application/json'],
    CURLOPT_POSTFIELDS=>json_encode($payload, JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT=>90,
]);
$body = curl_exec($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);
if ($body === false) ai_out(502, ['ok'=>false,'error'=>'OpenAI connection failed: '.$error]);
$json = json_decode((string)$body, true);
if (!is_array($json)) ai_out(502, ['ok'=>false,'error'=>'OpenAI returned an invalid response.']);
if ($status < 200 || $status >= 300) ai_out($status >= 400 && $status < 600 ? $status : 502, ['ok'=>false,'error'=>(string)($json['error']['message'] ?? 'OpenAI request failed.')]);
$text = beyond_ai_extract_text($json);
if ($text === '') ai_out(502, ['ok'=>false,'error'=>'The model returned no text.']);
$inputTokens = (int)($json['usage']['input_tokens'] ?? 0);
$outputTokens = (int)($json['usage']['output_tokens'] ?? 0);
$cost = beyond_ai_estimate_cost($mode, $inputTokens, $outputTokens);
$today = beyond_ai_record_usage($inputTokens, $outputTokens, $cost);
ai_out(200, ['ok'=>true,'text'=>$text,'model'=>$model,'usage'=>['input_tokens'=>$inputTokens,'output_tokens'=>$outputTokens,'request_cost'=>$cost,'today'=>$today]]);
