<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Grim\Utils\ResultLogger;
if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">No result ID specified.</div>';
    exit;
}
$id = (int)$_GET['id'];
$logger = new ResultLogger();
$result = $logger->getResult($id);
if (!$result) {
    echo '<div class="alert alert-danger">Result not found.</div>';
    exit;
}
$json = json_encode(json_decode($result['result_json']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo '<pre style="max-height:400px;overflow:auto;">' . htmlspecialchars($json) . '</pre>';