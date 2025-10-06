<?php
// Simple REST API stub for GRIM
require_once __DIR__ . '/../vendor/autoload.php';
use Grim\GrimApplication;
use Grim\Utils\ResultLogger;

$app = new GrimApplication();
$logger = new ResultLogger();

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'scan':
        $target = $_GET['target'] ?? '';
        if ($target) {
            $result = $app->runFullScan($target);
            echo json_encode(['status' => 'ok', 'result' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing target']);
        }
        break;
    case 'results':
        echo json_encode($logger->listResults());
        break;
    default:
        echo json_encode(['error' => 'Unknown action']);
}
