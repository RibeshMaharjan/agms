<?php

define('CNN_SERVICE_URL', 'http://100.105.55.0:8000');
define('CNN_TIMEOUT', 10);
define('CNN_CONFIDENCE_THRESHOLD', 0.7);

function detectAIGeneratedImage($imagePath)
{
    if (! file_exists($imagePath)) {
        return ['error' => 'Image file not found', 'is_ai_generated' => null];
    }

    $url = CNN_SERVICE_URL.'/detect';
    $boundary = uniqid('', true);
    $fileContents = file_get_contents($imagePath);
    $filename = basename($imagePath);

    $body = "--{$boundary}\r\n";
    $body .= "Content-Disposition: form-data; name=\"image\"; filename=\"{$filename}\"\r\n";
    $body .= "Content-Type: image/jpeg\r\n\r\n";
    $body .= $fileContents."\r\n";
    $body .= "--{$boundary}--\r\n";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => [
            "Content-Type: multipart/form-data; boundary={$boundary}",
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => CNN_TIMEOUT,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log('CNN Service Error: '.$error);

        return ['error' => $error, 'is_ai_generated' => null];
    }

    if ($httpCode !== 200) {
        error_log('CNN Service HTTP Error: '.$httpCode.' - '.$response);

        return ['error' => "Service returned HTTP {$httpCode}", 'is_ai_generated' => null];
    }

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON response', 'is_ai_generated' => null];
    }

    return [
        'is_ai_generated' => $result['prediction']['is_ai_generated'] ?? false,
        'confidence' => $result['prediction']['confidence'] ?? 0.0,
        'label' => $result['prediction']['label'] ?? 'unknown',
        'processing_time_ms' => $result['processing_time_ms'] ?? 0,
    ];
}

function isAIGenerated($imagePath)
{
    $result = detectAIGeneratedImage($imagePath);

    if (isset($result['error'])) {
        error_log('AI detection failed, allowing upload: '.$result['error']);

        return false;
    }

    return $result['is_ai_generated'] &&
           $result['confidence'] >= CNN_CONFIDENCE_THRESHOLD;
}
