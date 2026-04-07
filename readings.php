<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$file = __DIR__ . '/readings.json';

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$data = file_get_contents($file);

if ($data === false) {
    echo json_encode([]);
    exit;
}

echo $data;