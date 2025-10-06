<?php
// Simple Web Dashboard (stub)
require_once __DIR__ . '/../vendor/autoload.php';

use Grim\Utils\ResultLogger;

$logger = new ResultLogger();
$results = $logger->listResults();
?>
<!DOCTYPE html>
<html>
<head>
    <title>GRIM Web Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>GRIM Security Scanner Dashboard</h1>
    <h2>Scan History</h2>
    <table>
        <tr><th>ID</th><th>Target</th><th>Time</th></tr>
        <?php foreach ($results as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['target']) ?></td>
            <td><?= htmlspecialchars($row['scan_time']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><em>Stub: Add scan controls, result view, and scheduling here.</em></p>
</body>
</html>
