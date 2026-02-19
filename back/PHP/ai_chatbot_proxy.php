<?php
header('Content-Type: application/json; charset=utf-8');

function json_error($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'error' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metodo no permitido', 405);
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);

if (!is_array($payload)) {
    json_error('JSON invalido');
}

$messages = $payload['messages'] ?? null;
$model = $payload['model'] ?? 'gpt-3.5-turbo';
$maxTokens = isset($payload['max_tokens']) ? (int)$payload['max_tokens'] : 500;
$temperature = isset($payload['temperature']) ? (float)$payload['temperature'] : 0.7;

if (!is_array($messages) || count($messages) === 0) {
    json_error('messages es requerido');
}

$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    json_error('OPENAI_API_KEY no configurada en el servidor', 500);
}

if (!function_exists('curl_init')) {
    json_error('cURL no esta disponible en el servidor', 500);
}

$requestBody = [
    'model' => $model,
    'messages' => $messages,
    'max_tokens' => $maxTokens,
    'temperature' => $temperature
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($result === false) {
    json_error('Error de red al conectar con OpenAI: ' . $curlError, 502);
}

$openaiData = json_decode($result, true);
if (!is_array($openaiData)) {
    json_error('Respuesta invalida de OpenAI', 502);
}

if ($httpCode < 200 || $httpCode >= 300) {
    $openaiError = $openaiData['error']['message'] ?? 'Error desconocido de OpenAI';
    json_error($openaiError, $httpCode);
}

$reply = $openaiData['choices'][0]['message']['content'] ?? null;
if (!$reply) {
    json_error('OpenAI no devolvio contenido', 502);
}

echo json_encode([
    'success' => true,
    'reply' => $reply
], JSON_UNESCAPED_UNICODE);
