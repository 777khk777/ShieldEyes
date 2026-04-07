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
        // Support both new format (ts/r) and old format (timestamp/readings)
        $t = strtotime(isset($entry['ts']) ? $entry['ts'] : $entry['timestamp']);
        $rows = isset($entry['r']) ? $entry['r'] : $entry['readings'];
        foreach ($rows as $r) {
            $id = isset($entry['r']) ? ("Sensor " . $r[0]) : $r['sensor'];
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
            "ts" => date("Y-m-d H:i:s"),
            "r"  => array_map(function($r) {
                $sensorNum = (int) preg_replace('/\D/', '', $r['sensor']);
                $row = [
                    $sensorNum,
                    round((float)$r['temperature'], 1),
                    round((float)$r['humidity'], 1)
                ];
                // CO2: Sensor 0 only, optional field
                if ($sensorNum === 0 && isset($r['co2'])) {
                    $row[] = (int)$r['co2'];
                } else {
                    $row[] = null;
                }
                return $row;
            }, $filtered)
        ];
        array_unshift($readings, $entry);
        $readings = array_slice($readings, 0, 20000);
        file_put_contents($file, json_encode($readings, JSON_UNESCAPED_UNICODE));
    }

    echo json_encode(["status" => "ok"]);

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Invalid JSON"]);
}
?>
