<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No image provided']);
    exit;
}

include_once __DIR__ . '/../includes/cnn_helper.php';

$image = $_FILES['image'];
$tmpPath = $image['tmp_name'];

if (!file_exists($tmpPath)) {
    http_response_code(400);
    echo json_encode(['error' => 'Temporary file not found']);
    exit;
}

$result = detectAIGeneratedImage($tmpPath);
echo json_encode($result);
