<?php
$file = "readings.json";
$test = [["temperature" => 22.5, "humidity" => 55.0, "timestamp" => "2026-03-27 12:00:00"]];
file_put_contents($file, json_encode($test));

if (file_exists($file)) {
    echo json_encode(["status" => "write OK", "file" => $file]);
} else {
    echo json_encode(["status" => "write FAILED — check folder permissions"]);
}
?>
```

Visit `https://unityhaus.ca/sensor/writetest.php` in your browser. If it says **write OK**, then check `readings.php` — it should now return data. If it says **write FAILED**, the folder permissions need fixing.

---

**Step 3 — Confirm readings.php returns data**

Visit:
```
https://unityhaus.ca/sensor/readings.php