<?php
// Example scheduled scan script
// To use: Add to system cron, e.g.:
// 0 2 * * * /usr/bin/php /path/to/Grim/schedules/scan_schedule.php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Grim\GrimApplication();
$target = 'example.com'; // Set your target or load from config

// Run a scan (customize options as needed)
$app->runFullScan($target, true, true, true);

// Optionally export results
$app->exportResults('json');
