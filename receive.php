<?php
// Allow POST requests from ESP32
header("Content-Type: application/json");

// Read incoming JSON from ESP32
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if ($data && isset($data['readings'])) {

    $file = "readings.json";
    $readings = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    // Deduplication: skip any sensor seen within the last 2 minutes.
    // Prevents duplicate readings when the nRF52 advertising burst
    // straddles two consecutive ESP32 scan cycles (~60s apart).
    $dedup_seconds = 120;
    $now = time();

    $lastSeen = [];
    foreach ($readings as $entry) {
        $t = strtotime($entry['timestamp']);
        foreach ($entry['readings'] as $r) {
            $id = $r['sensor'];
            if (!isset($lastSeen[$id]) || $t > $lastSeen[$id]) {
                $lastSeen[$id] = $t;
            }
        }
    }

    $filtered = array_values(array_filter($data['readings'], function($r) use ($lastSeen, $now, $dedup_seconds) {
        return !isset($lastSeen[$r['sensor']]) || ($now - $lastSeen[$r['sensor']]) >= $dedup_seconds;
    }));

    if (!empty($filtered)) {
        $entry = [
            "readings"  => $filtered,
            "timestamp" => date("Y-m-d H:i:s")
        ];
        array_unshift($readings, $entry);
        $readings = array_slice($readings, 0, 100);
        file_put_contents($file, json_encode($readings));
    }

    echo json_encode(["status" => "ok"]);

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Invalid JSON"]);
}
?>