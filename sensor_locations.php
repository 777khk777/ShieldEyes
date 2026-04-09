<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

$file = __DIR__ . '/sensor_locations.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!file_exists($file)) {
        echo json_encode(['sensors' => []]);
        exit;
    }
    echo file_get_contents($file);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if ($data === null) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    $result = file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if ($result === false) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Could not write file — check server permissions']);
        exit;
    }

    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
